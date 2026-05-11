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
        Descripción: Renderiza la lista principal de inscripciones en funcion del rol de usuario
    */
    public function render()
    {
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripcion']['render']);

        // Crear token csrf
        $this->generateTokenCsrf();

        $user_id = $_SESSION['user_id'];
        $role_id = $_SESSION['role_id'];

        // Comprobar si hay mensajes:
        $this->checkMessages();

        // Limpia inscripciones que hayan quedado como pendientes durante un periodo mayor a 60 minutos
        $this->model->limpiarInscripcionesPendientes(60);

        $this->view->title = "Gestión de Inscripciones - Traileros";

        $inscripciones = $this->model->getInscripcionesByRole($user_id, $role_id);
        $this->view->inscripciones = json_decode(json_encode($inscripciones), true);

        // Cargamos datos que se repiten en todas las vistas
        $this->cargarDatosMenu();

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

        // Compruebo si hay mensajes
        $this->checkMessages();

        // Antes de nada verificar los datos del perfil del usuario.
        $user_id = $_SESSION['user_id'];
        /** @var UserModel $userModel */
        $userModel = $this->loadModel('user');
        $usuario   = $userModel->read($user_id);

        // Campos fijos obligatorios para la inscripción.
        $campos_obligatorios = [
            'apellidos', 'sexo', 'fecha_nacimiento', 'dni', 
            'telefono', 'direccion', 'poblacion', 'provincia',
            'codigo_postal', 'pais', 'club', 'talla_camiseta'
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

        // VALIDACIÓN DE PLAZO DE INSCRIPCIÓN
        $hoy = new DateTime();
        $fecha_cierre = new DateTime($evento['fecha_cierre_inscripcion']);

        if ($hoy > $fecha_cierre) {
            $_SESSION['notify'] = "Lo sentimos, el plazo de inscripción finalizó el " . $fecha_cierre->format('d/m/Y H:i');
            header('location:' . URL . 'carrera/show/' . $evento_id);
            exit;
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

        // Crear token csrf
        $this->generateTokenCsrf();

        // Lógica de modalidades y plazas: Obtneemos todas las modalidades
        $modalidades = $eventoModel->getModalidadesByEvento($evento_id);
        $modalidades_disponibles = [];

        foreach ($modalidades as $mod) {
            // Calculamos plazas para cada modalidad
            $ocupadas = $eventoModel->getPlazasOcupadas($mod['id']);
            $libres = $mod['cupo_maximo'] - $ocupadas;

            if ($libres > 0) {
                // Inyectamos el dato de plazas libres para mostrarlo en el select
                $mod['plazas_libres'] = $libres;
                $modalidades_disponibles[] = $mod;
            }
        }

        //  Si después de filtrar no queda ninguna modalidad con plazas
        if (empty($modalidades_disponibles)) {
            $_SESSION['notify'] = "Lo sentimos, ya no quedan plazas libres en ninguna modalidad.";
            header('location:' . URL . 'carrera/show/' . $evento_id);
            exit;
        }

        $this->view->title  = "Inscripción: " . $evento['nombre'];
        $this->view->evento = $evento;

        $this->view->modalidades = $modalidades_disponibles;

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

        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripcion']['create']);
        // Validacion token CSRF
        $this->checkTokenCsrf($_POST['csrf_token'] ?? '');

        // Carga de todos los modelos que vamos a usar en este método
        $userModel = $this->loadModel('user');
        $carreraModel = $this->loadModel('carrera');
        $inscripcionModel = $this->loadModel('inscripcion');

        // Recoge datos del usuario
        $user_id = $_SESSION['user_id'];
        $datos = $userModel->read($user_id);
        
        // Mapeo a class_user
        $user = new class_user();
        foreach ($datos as $clave => $val) { $user->$clave = $val; }
        // Datos que pueden cambiar en el formulario de inscripción
        $user->tlf = $_POST['tlf'];
        $user->talla = $_POST['talla'];
        $user->club = $_POST['club'];
        // Guardamos cambios en el perfil
        $userModel->update($user, $user_id, $_SESSION['role_id']);

        // Valida si existen plazas en el evento
        $evento_id = (int)$_POST['evento_id'];
        $evento = $carreraModel->read($evento_id);
        $modalidad_id = (int)$_POST['modalidad_id'];

        // Validación de fecha tope de inscripción previa al pago
        $hoy = new DateTime();
        $fecha_cierre = new DateTime($evento['fecha_cierre_inscripcion']);

        if ($hoy > $fecha_cierre) {
            $_SESSION['error'] = "El plazo de inscripción ha expirado. No se puede procesar el pago.";
            header('Location: ' . URL . 'carrera/show/' . $evento_id);
            exit();
        }

        // Comprobamos si existe alguna inscripción previa que se haya cancelado por algun motivo
        $inscripcionPrevia = $inscripcionModel->getInscripcionSimple($user_id, $evento_id);

        if ($inscripcionPrevia) {
            $estado = $inscripcionPrevia['estado_pago'];

            // Si está cancelada o fallida, hacemos Hard Delete para permitir la nueva
            if ($estado === 'cancelado' || $estado === 'fallido') {
                $inscripcionModel->hardDelete($inscripcionPrevia['id']);
            } else {
                // Si ya está pagado o pendiente (esperando a Stripe), no dejamos continuar
                $_SESSION['notify'] = "Ya tienes una inscripción activa o pendiente para esta carrera.";
                header('Location: ' . URL . 'carrera/show/' . $evento_id);
                exit();
            }
        }

        $libres = $evento['cupo_maximo'] - $carreraModel->getPlazasOcupadas($evento_id);
        if ($libres <= 0) {
            $_SESSION['notify'] = "Lo sentimos, ya no quedan plazas.";
            header('Location: ' . URL . 'carrera/show/' . $evento_id);
            exit();
        }

        // Asigna automáticamente la categoría en función de la edad
        $nacimiento = new DateTime($user->fecha_nac);
        $fecha_evento = new DateTime($evento['fecha']);
        $edad = $fecha_evento->diff($nacimiento)->y;

        // Usamos el modelo de inscripción
        $categoria_id = $inscripcionModel->getCategoriaAdecuada($edad, $user->sexo);

        // Obtenemos los datos de la modalidad elegida paran el precio
        $modalidad_datos = $carreraModel->getModalidad($modalidad_id);

        if (!$modalidad_datos) {
            $_SESSION['error'] = "Modalidad no válida.";
            header('Location: ' . URL . 'carrera');
            exit();
        }

        $precio_real = $modalidad_datos['precio'];

        try {
            // Stripe (entorno de pruebas): Número = 4242 4242 4242 4242 | Fecha = 12/34 | CVC = 123
            // $pagoExitoso = true; 
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

            $checkout_session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Inscripción: ' . $evento['nombre'],
                            'description' => 'Modalidad: ' . $modalidad['nombre'],
                        ],
                        'unit_amount' => $precio_real * 100, // Céntimos
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => URL . "inscripcion/success?session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => URL . "inscripcion/new/$evento_id",
            ]);

            // Guarda la inscripción
            $metodo_pago = filter_var($_POST['metodo_pago'], FILTER_SANITIZE_SPECIAL_CHARS);
            $id_pago = $checkout_session->id;
            
            $nuevaInscripcion = new class_inscripcion(
                null,
                $user_id,
                $evento_id,
                $modalidad_id,
                $categoria_id,
                null, // Fecha current_timestamp en la bd
                null, // El dorsal se autogenera en el modelo
                $id_pago,
                $metodo_pago,
                'pendiente',
                $precio_real
            );

            if ($inscripcionModel->create($nuevaInscripcion)) {
                // Redirigimos a Stripe
                header("HTTP/1.1 303 See Other");
                header("Location: " . $checkout_session->url);
                exit();
            } else {
                $this->handleError("Fallo al crear la inscripción", 500);
            }

        } catch (Exception $e){
            // $_SESSION['notify'] = "Error al conectar con la pasarela de pago: " . $e->getMessage();
            // header('Location: ' . URL . 'carrera/show/' . $evento_id);
            // exit();
            die("Error en Stripe o BD: " . $e->getMessage());
        }
    }


    // Procesa y confirma el pago (Asigna dorsal automático)
    public function success()
    {
        $this->requireLogin();
        $id_pago = $_GET['session_id'] ?? null;

        if ($id_pago) {
            // El modelo se encarga de verificar que no esté ya completada y asignar dorsal
            if ($this->model->confirmarPago($id_pago)) {
                $_SESSION['notify'] = "¡Inscripción confirmada! Ya puedes ver tu dorsal en el listado.";

                // Envía un correo de confirmación
                // Primero obtenemos los datos del usuario, carrera e inscripcion:
                $inscripcion = $this->model->getByPagoId($id_pago);
                if ($inscripcion){
                    // Enviamos correo de confirmación
                    $asunto = "¡Inscripción confirmada! - " . $inscripcion['evento_nombre'];
                    // Generamos la URL del QR (usamos el ID de inscripción)
                    $url_qr = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=INS-" . $inscripcion['id'];
                    $texto = "
                        <div style='font-family: sans-serif; color: #333; max-width: 600px; margin: auto;'>
                            <p>Hola <strong>{$inscripcion['usuario_nombre']}</strong>,</p>
                            <p>¡Ya es oficial! Tienes una cita con la montaña.</p>
                            
                            <div style='background: #f9f9f9; padding: 20px; border-radius: 10px; border: 1px solid #eee; margin: 20px 0; text-align: center;'>
                                <h2 style='margin-top: 0; color: #2ecc71;'>{$inscripcion['evento_nombre']}</h2>
                                <p style='font-size: 16px;'>Tu dorsal asignado es:</p>
                                <div style='font-size: 48px; font-weight: bold; color: #2ecc71; margin: 10px 0;'>#{$inscripcion['dorsal']}</div>
                                
                                <div style='margin-top: 20px;'>
                                    <img src='{$url_qr}' alt='Código QR Check-in' style='border: 10px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.1);'>
                                    <p style='font-size: 12px; color: #777; margin-top: 10px;'>Muestra este QR en el control de firmas</p>
                                </div>  
                                
                                <p style='margin-top: 20px;'><strong>Fecha:</strong> " . date('d/m/Y', strtotime($inscripcion['evento_fecha'])) . "</p>
                            </div>
                            
                            <p>¡Nos vemos en la salida!</p>
                        </div>
                    ";

                    Email::enviar($inscripcion['email'], $asunto, $texto);

                    // Pasamos los datos a la vista de post-pago:
                    $this->view->datos = $inscripcion;
                    $this->view->title = "¡Inscripción confirmada!";

                    // Renderizamos la vista de post-pago:
                    $this->view->render('inscripcion/success/index');
                    return; // Nos asegura que acaba aquí si todo va bien
                }
                
            } else {
                $_SESSION['notify'] = "Hubo un problema al confirmar tu pago.";
            }
        }
        // Si algo falla redirige a la lista de inscripciones
        header('Location: ' . URL . 'inscripcion');
        exit();
    }

    /*
        Método: edit / update
        Descripción: Permite al admin cambiar estado de pago o dorsal
    */
    public function edit($params)
    {
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripcion']['edit']);

        // Compruebo si hay mensajes
        $this->checkMessages();

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
            $this->handleError("No se ha podido actualizar la inscripcion", 500);
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

        // Compruebo si hay mensajes
        $this->checkMessages();

        $user_id   = (int) $params[0];
        $evento_id = (int) $params[1];

        $inscripcion = $this->model->getDetalleCompleto($user_id, $evento_id);

        if (!$inscripcion) $this->handleError("No existe inscripcion para este usuario en este evento", 500);

        $this->checkOwnership($inscripcion['user_id'], $inscripcion['organizador_id']);

        $this->view->title = "Detalles de inscripción";
        $this->view->inscripcion = $inscripcion;
        $this->view->readonly = true;

        $this->view->render('inscripcion/show/index');
    }

    public function cancel($params)
    {
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripcion']['cancel']);

        // Validacion token CSRF
        $this->checkTokenCsrf($_POST['csrf_token'] ?? '');

        $inscripcion_id   = (int) $params[0];

        // Obtenemos detalles de la inscripcion
        $inscripcion = $this->model->getDetalleParaEmail($inscripcion_id);

        if ($this->model->cancel($inscripcion_id)) {
            // Envía un correo de confirmación
            // Si tenemos los datos preparamos el correo    
            if ($inscripcion){
                $asunto = "Cancelación de inscripción - " . $inscripcion['evento_nombre'];
                $cuerpo = "<h1>Hola {$inscripcion['usuario_nombre']}</h1>
                   <p>Te confirmamos que tu inscripción para la carrera <strong>{$inscripcion['evento_nombre']}</strong> ha sido cancelada con éxito.</p>
                   <p>Esperamos verte en la próxima montaña.</p>";
                   
                // Envia el correo
                Email::enviar($inscripcion['usuario_email'], $asunto, $cuerpo);

                $_SESSION['notify'] = "La inscripción ha sido cancelada.";
            }
        } else {
            $_SESSION['error'] = "Error al intentar cancelar la inscripción.";

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

        // Compruebo si hay mensajes
        $this->checkMessages();

        $user_id = $_SESSION['user_id'];
        $role_id = $_SESSION['role_id'];

        $term = isset($_GET['term']) ? trim($_GET['term']) : '';
        $this->view->term = $term;

        // Si el término está vacío, cargamos todas las inscripciones (render)
        if ($term === '') {
            $this->render(); 
            return;
        } else {
            $inscripciones = $this->model->search($term);
            $this->view->subtitle = "Resultados de búsqueda: " . htmlspecialchars($term);
        }

        // Pasamos a array para que no de problemas en las vistas
        $inscripcionesArray = json_decode(json_encode($inscripciones), true);

        $this->view->inscripciones = $inscripcionesArray;
        $this->view->title = "Resultados de búsqueda: " . $term;
        $this->view->render('inscripcion/main/index');
    }

    public function order($param)
    {
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripcion']['order']);

        // Compruebo si hay mensajes
        $this->checkMessages();
        
        // Obtenemos el criterio
        $criterio = $param[0] ?? 'fecha';

        // Obtenemos datos de sesión para filtrar por rol del usuario
        $user_id = $_SESSION['user_id'];
        $role_id = $_SESSION['role_id'];

        $this->view->inscripciones = $this->model->order($criterio, $user_id, $role_id);

        $titulos = [
            'id' => 'Id',
            'usuario' => 'Participante',
            'evento'  => 'Evento',
            'dorsal'  => 'Dorsal',
            'fecha'   => 'Fecha de Inscripción',
            'estado'  => 'Estado de Pago'
        ];
        $nombre_criterio = $titulos[$criterio] ?? 'Fecha';

        $this->view->title = "Mis Inscripciones - Traileros";
        
        $this->view->notify = "Inscripciones ordenadas por: " . $nombre_criterio;
        // $this->view->subtitle = "Inscripciones ordenadas por: " . $nombre_criterio;

        $this->view->render('inscripcion/main/index');
    }

    public function participantes($param){
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripcion']['participantes']);

        $id_carrera = $param[0];

        $this->view->inscritos = $this->model->getInscritosPorEvento($id_carrera);

        $carreraModel = $this->loadModel('carrera');
        $this->view->carrera = $carreraModel->read($id_carrera);
    
        $this->view->title = "Listado de Inscritos - ";
        $this->view->render('inscripcion/participantes/index');
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

    /*
        Descripción: exporta en PDF la lista de inscritos a una carrera.
    */
    public function exportPdf($params) {
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripciones']['export']);
        $evento_id = (int) $params[0];


        $carrera = $this->model->getNombreEvento($evento_id);
        $inscritos = $this->model->getInscritosExport($evento_id);

        // Instancia de FPDF (Composer autoload)
        $pdf = new \Fpdf\Fpdf();
        $pdf->AddPage();
        
        if (!$carrera) {
            header('Location: ' . URL . 'carrera');
            return;
        }

        // Título
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, utf8_decode('LISTADO GENERAL DE INSCRITOS'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, utf8_decode($carrera), 0, 1, 'C');
        $pdf->Ln(10);

        // Encabezados de la tabla (Ancho total: 190)
        $pdf->SetFillColor(46, 204, 113); 
        $pdf->SetTextColor(255);
        $pdf->SetFont('Arial', 'B', 9);
        
        $pdf->Cell(15, 7, 'Dorsal', 1, 0, 'C', true);
        $pdf->Cell(65, 7, 'Nombre y Apellidos', 1, 0, 'C', true);
        $pdf->Cell(40, 7, 'Modalidad', 1, 0, 'C', true); 
        $pdf->Cell(40, 7, 'Categoria', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Fecha', 1, 1, 'C', true);

        // Datos
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial', '', 8);
        
        foreach ($inscritos as $row) {
            $pdf->Cell(15, 6, $row['dorsal'] ?? '-', 1, 0, 'C');
            $pdf->Cell(65, 6, utf8_decode($row['nombre']), 1);
            $pdf->Cell(40, 6, utf8_decode($row['modalidad']), 1); 
            $pdf->Cell(40, 6, utf8_decode($row['categoria'] ?? 'General'), 1);
            $pdf->Cell(30, 6, date('d/m/Y', strtotime($row['fecha_inscripcion'])), 1, 1, 'C');
        }

        $pdf->Output('I', 'Inscritos_' . $carrera . '.pdf');
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
