<?php

class Inscripcion extends Controller
{

    public function __construct()
    {
        parent::__construct();
        // Iniciamos sesión una sola vez para todos los métodos de este controlador
        if (session_status() == PHP_SESSION_NONE) {
            sec_session_start();
        }
    }

    /*
        Método: render()
        Descripción: Renderiza la lista principal de inscripciones
    */
    public function render()
    {
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripcion']['render']);

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $user_id = $_SESSION['user_id'];
        $role_id = $_SESSION['role_id'];

        if (isset($_SESSION['notify'])) {
            $this->view->notify = $_SESSION['notify'];
            unset($_SESSION['notify']);
        }

        $this->view->title = "Mis Inscripciones - Traileros";

        $this->view->inscripciones = $this->model->getInscripcionesByRole($user_id, $role_id);

        $this->view->render('inscripcion/main/index');
    }

    /*
        Método: new
        Descripción: Muestra el formulario de inscripción para un evento específico
    */
    public function new ($params = null)
    {
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripcion']['new']);

        // Antes de nada verificar los datos del perfil del usuario.
        $user_id = $_SESSION['user_id'];
        /** @var UserModel $userModel */
        $userModel = $this->loadModel('user');
        $usuario   = $userModel->read($user_id);

        // Campos fijos obligatorios para la inscripción.
        $campos_obligatorios = [
            'nombre', 'apellidos', 'email', 'sexo',
            'fecha_nac', 'dni', 'tlf', 'direccion', 
            'poblacion', 'provincia', 'cp', 'talla'
        ];

        foreach ($campos_obligatorios as $campo) {
            if (!isset($usuario->$campo) || trim($usuario->$campo) === '') {
                $this->redirectIncomplete("Debes completar los datos del perfil");
            }
        }

        // Comprobamos número de licencia en caso de usuario federado
        if ($usuario->es_federado == 1 && empty($usuario->num_licencia)) {
            $this->redirectIncomplete("Si estás federado, debes indicar tu número de licencia.");
        }

        $evento_id = (int) ($params[0] ?? 0);
        // Cargamos modelos adicionales para datos del evento y categorías
        /** @var CarreraModel $eventoModel */
        $eventoModel = $this->loadModel('carrera');
        $evento = $eventoModel->read($evento_id);

        if (! $evento) {
            $this->errorNotFound($evento_id);
        }

        // Calculamos la edad (Comparando fecha de nacimiento con fecha del evento)
        $fecha_nac    = new DateTime($usuario->fecha_nac);
        $fecha_evento = new DateTime($evento['fecha']);
        $intervalo    = $fecha_nac->diff($fecha_evento);
        $edad_usuario = $intervalo->y;

        // Validamos rangos
        $min = (int) $evento['edad_minima'];
        $max = (int) $evento['edad_maxima'];

        if ($edad_usuario < $min) {
            $_SESSION['notify'] = "Lo sentimos, esta carrera requiere un mínimo de $min años. Tienes $edad_usuario.";
            header('location:' . URL . 'carrera/show/' . $evento_id);
            exit;
        }

        if ($edad_usuario > $max) {
            $_SESSION['notify'] = "Lo sentimos, esta carrera tiene un límite de edad de $max años.";
            header('location:' . URL . 'carrera/show/' . $evento_id);
            exit;
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Comprobación de plazas antes de mostrar el formulario
        $plazas_libres = $evento['cupo_maximo'] - $eventoModel->getPlazasOcupadas($evento_id);
        if ($plazas_libres <= 0) {
            $_SESSION['notify'] = "Lo sentimos, no quedan plazas libres.";
            header('location:' . URL . 'carrera/show/' . $evento_id);
            exit;
        }

        $this->view->title  = "Inscripción: " . $evento['nombre'];

        $this->view->evento = $evento;

        $this->view->inscripcion = new class_inscripcion();
        $this->view->inscripcion->evento_id = $evento_id;

        $this->view->usuario = $usuario;

        $this->view->render('inscripcion/new/index');
    }

    /*
        Método: create
        Descripción: Procesa la inscripción y asigna categoría automáticamente
    */
    public function create()
    {
        // --- 1. SEGURIDAD Y REQUISITOS ---
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripcion']['create']);
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $this->handleError();
        }

        // --- 2. CARGA DE MODELOS (Todos juntos al principio para claridad) ---
        $userModel = $this->loadModel('user');
        $carreraModel = $this->loadModel('carrera');
        $inscripcionModel = $this->loadModel('inscripcion');

        // --- 3. RECOGIDA Y ACTUALIZACIÓN DE DATOS DEL USUARIO ---
        $user_id = $_SESSION['user_id'];
        $datosBase = $userModel->read($user_id);
        
        // Mapeo a class_user
        $user = new class_user();
        foreach ($datosBase as $prop => $val) { $user->$prop = $val; }
        $user->tlf = $_POST['tlf'];
        $user->talla = $_POST['talla'];
        $user->club = $_POST['club'];
        
        // Guardamos cambios en el perfil
        $userModel->update($user, $user_id, $_SESSION['role_id']);

        // --- 4. VALIDACIÓN DE LA CARRERA (Plazas y Evento) ---
        $evento_id = (int)$_POST['evento_id'];
        $evento = $carreraModel->read($evento_id);

        $libres = $evento['cupo_maximo'] - $carreraModel->getPlazasOcupadas($evento_id);
        if ($libres <= 0) {
            $_SESSION['notify'] = "Lo sentimos, ya no quedan plazas.";
            header('Location: ' . URL . 'carrera');
            exit();
        }

        // --- 5. LÓGICA DE NEGOCIO (Categoría y Edad) ---
        $nacimiento = new DateTime($user->fecha_nac);
        $fecha_evento = new DateTime($evento['fecha']);
        $edad = $fecha_evento->diff($nacimiento)->y;

        // Usamos el modelo de inscripción
        $categoria_id = $inscripcionModel->getCategoriaAdecuada($edad, $user->sexo);

        // --- 6. PROCESADO DE PAGO (Simulación) ---
        $pagoExitoso = true; // Aquí iría la lógica de tu pasarela

        // --- 7. FINALIZACIÓN: GUARDAR INSCRIPCIÓN ---
        if ($pagoExitoso) {
            $metodo_pago = filter_var($_POST['metodo_pago'], FILTER_SANITIZE_SPECIAL_CHARS);
            
            $nuevaInscripcion = new class_inscripcion(
                $user_id,
                $evento_id,
                $categoria_id,
                null, // El dorsal se autogenera en el modelo
                $metodo_pago,
                'completado',
                $evento['precio']
            );

            if ($inscripcionModel->create($nuevaInscripcion)) {
                $_SESSION['notify'] = "¡Inscripción realizada con éxito!";
                header('Location: ' . URL . 'inscripcion');
                exit();
            } else {
                $this->handleError();
            }
        }
    }

    /*
        Método: edit / update
        Descripción: Permite al admin cambiar estado de pago o dorsal
    */
    public function edit($params)
    {
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripcion']['edit']);

        $user_id   = (int) $params[0];
        $evento_id = (int) $params[1];

        $inscripcion = $this->model->getDetalleCompleto($user_id, $evento_id);
        if (! $inscripcion) {
            $this->errorNotFound("$user_id-$evento_id");
        }

        // Check de permisos
        $this->checkOwnership($inscripcion['user_id'], $inscripcion['organizador_id']);

        $this->view->categorias   = $this->model->getAllCategorias();
        $this->view->metodos_pago = ['tarjeta', 'transferencia', 'bizum', 'efectivo'];
        $this->view->inscripcion  = $inscripcion;
        $this->view->title        = "Editar Inscripción";
        $this->view->render('inscripcion/edit/index');
    }

    public function update($params)
    {
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripcion']['update']);

        $user_id   = (int) $_POST['user_id'];
        $evento_id = (int) $_POST['evento_id'];

        // Saneamiento de datos
        $data = [
            'user_id'      => $user_id,
            'evento_id'    => $evento_id,
            'categoria_id' => (int) $_POST['categoria_id'],
            'dorsal'       => empty($_POST['dorsal']) ? null : (int) $_POST['dorsal'],
            'metodo_pago'  => filter_var($_POST['metodo_pago'], FILTER_SANITIZE_SPECIAL_CHARS),
            'estado_pago'  => filter_var($_POST['estado_pago'], FILTER_SANITIZE_SPECIAL_CHARS),
            'precio_final' => filter_var($_POST['precio_final'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
        ];

        if ($this->model->update($data)) {
            $_SESSION['notify'] = "Inscripción actualizada correctamente.";
            header('Location: ' . URL . 'inscripcion');
            exit();
        } else {
            $this->handleError();
        }
    }

    /*
        Método: show
        Descripción: Ver resguardo de inscripción
    */
    public function show($params)
    {
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripcion']['show']);
        $user_id   = (int) $params[0];
        $evento_id = (int) $params[1];

        $inscripcion = $this->model->getDetalleCompleto($user_id, $evento_id);

        if (!$inscripcion) $this->handleError();

        $this->checkOwnership($inscripcion['user_id'], $inscripcion['organizador_id']);

        $this->view->title = "Detalles de inscripción";
        $this->view->inscripcion = $inscripcion;
        $this->view->readonly = true;

        $this->view->render('inscripcion/show/index');
    }

    public function delete($params)
    {
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripcion']['delete']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' ||
            ! hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $this->handleError();
            exit();
        }

        $user_id   = (int) $params[0];
        $evento_id = (int) $params[1];

        if ($this->model->delete($user_id, $evento_id)) {
            $_SESSION['notify'] = "La inscripción ha sido cancelada.";
        } else {
            $_SESSION['notify'] = "Error al intentar cancelar la inscripción.";

        }

        header('Location: ' . URL . 'inscripcion');
        exit();
    }

    /*
        Métodos: search y order
    */
    public function search()
    {
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripcion']['search']);

        $user_id = $_SESSION['user_id'];
        $role_id = $_SESSION['role_id'];

        $term = isset($_GET['term']) ? trim($_GET['term']) : '';

        // Si el término está vacío, cargamos todas las inscripciones (método main/get)
        if ($term === '') {
            $this->view->inscripciones = $this->model->get(null,null);
            $this->view->subtitle = null;
        } else {
            $this->view->inscripciones = $this->model->search($term);
            $this->view->subtitle = "Resultados de búsqueda: " . htmlspecialchars($term);
        }

        $this->view->title = "Resultados de búsqueda: " . $term;

        $this->view->render('inscripcion/main/index');
    }

    public function order($param)
    {
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripcion']['order']);
        
        // Obtenemos el criterio
        $criterio = $param[0] ?? 'fecha';
        $this->view->inscripciones = $this->model->order($criterio);

        $titulos = [
            'usuario' => 'Participante',
            'evento'  => 'Evento',
            'dorsal'  => 'Dorsal',
            'fecha'   => 'Fecha de Inscripción',
            'estado'  => 'Estado de Pago'
        ];
        $nombre_criterio = $titulos[$criterio] ?? 'Fecha';

        $this->view->title = "Mis Inscripciones - Traileros";
        
        $this->view->subtitle = "Inscripciones ordenadas por: " . $nombre_criterio;

        $this->view->render('inscripcion/main/index');
    }

    /*
        Método para redireccionar a edición del perfil en caso de perfil incompleto
        (Función auxiliar dentro del controlador para no repetir código)
    */
    private function redirectIncomplete($mensaje)
    {
        $_SESSION['notify'] = $mensaje;
        header('location: ' . URL . 'account/edit/' . $_SESSION['user_id']);
        exit;
    }

    /*
        Método para exportar inscripciones en formato CSV / Excel
    */
    public function export($param){
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripcion']['export']);

        $evento_id = $param[0];
        $inscritos = $this->model->getInscritosExport($evento_id);

        if (empty($inscritos)){
            // Si no hay ningún inscrito redirigimos con notificación
            $_SESSION['notify'] = "No hay inscripciones para este evento";
            header("Location: " . URL . "inscripcion");
        }

        // Obtenemos el nombre del evento y lo limpiamos
        $nombreEvento = $this->model->getNombreEvento($evento_id);
        $nombreLimpio = strtolower($nombreEvento);
        $nombreLimpio = str_replace([' ', 'á', 'é', 'í', 'ó', 'ú', 'ñ'], ['_', 'a', 'e', 'i', 'o', 'u', 'n'], $nombreLimpio);
        // Quitamos cualquier otro caracter no alfanumérico
        $nombreLimpio = preg_replace('/[^a-z0-9_]/', '', $nombreLimpio);

        // Nombre del archivo:
        $fileName = "inscritos_" . $nombreLimpio . "_" . date('Y-m-d') . ".csv";

        // Cabeceras para forzar descarga del archivo 
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');

        // Abrimos archivo y configuramos 
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));    //Formato de caracteres

        // Cabeceras del archivo
        fputcsv($output, ['Dorsal', 'Nombre', 'Email', 'Categoria', 'Estado Pago', 'Fecha Inscripcion'], ";");

        foreach ($inscritos as $fila){
            fputcsv($output, $fila, ";");
        }

        // Cerramos  archivo
        fclose($output);
        exit;

    }

    /* --- Métodos privados de seguridad --- */
    private function requirePrivilege($allowedRoles)
    {
        if (! in_array($_SESSION['role_id'], $allowedRoles)) {
            $_SESSION['notify'] = "No tienes permisos para realizar esta acción";
            header('Location: ' . URL . 'carrera');
            exit();
        }
    }

    private function requireLogin()
    {
        if (! isset($_SESSION['user_id'])) {
            $_SESSION['notify'] = "Acceso restringido. Por favor, inicia sesión.";
            header('Location: ' . URL . 'auth/login');
            exit();
        }
    }

    private function checkOwnership($userIdInscrito, $organizadorIdEvento) {
        $mi_id  = $_SESSION['user_id'];
        $mi_rol = $_SESSION['role_id'];

        // 1. El Admin (Rol 1) se salta cualquier restricción
        if ($mi_rol == 1) return true;

        // 2. El Organizador (Rol 2) puede ver si el evento es suyo O si es su propia inscripción
        if ($mi_rol == 2) {
            if ($organizadorIdEvento == $mi_id || $userIdInscrito == $mi_id) return true;
        }

        // 3. El Corredor (Rol 3) SOLO puede ver si la inscripción es suya
        if ($mi_rol == 3) {
            if ($userIdInscrito == $mi_id) return true;
        }

        // Si no ha entrado en ningún return true, es que no tiene permiso
        $_SESSION['notify'] = "No tienes permiso para acceder a este registro.";
        header('Location: ' . URL . 'inscripcion');
        exit();
    }

    // Metodos para el manejo de errores
    private function handleError()
    {
        header('location:' . URL . 'error');
        exit();
    }

    // Método de error not found
    private function errorNotFound($id)
    {
        $this->view->tipo    = "404";
        $this->view->titulo  = "Recurso no encontrado";
        $this->view->mensaje = "Lo sentimos, el elemento con ID $id no existe.";
        $this->view->render('error/index');
        exit;
    }
}
