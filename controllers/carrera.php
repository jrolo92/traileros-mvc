<?php

class Carrera extends Controller {

    function __construct() {
        parent::__construct();
        // Iniciamos sesión una sola vez para todos los métodos de este controlador
        if (session_status() == PHP_SESSION_NONE) {
            sec_session_start();
        }
    }

    /*
        Método: render
        Descripción: Renderiza la lista principal de carreras
    */
    function render() {

        if(empty($_SESSION['csrf_token'])){
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Compruebo si hay mensajes
        $this->checkMessages();

        // Lógica para la paginación
        $items_pp = 6;  
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $order = isset($_GET['order']) ? (int)$_GET['order'] : 1;

        if ($currentPage < 1) $currentPage = 1;
        $offset = ($currentPage - 1) * $items_pp;

        $totalItems = $this->model->countTotal();
        $totalPages = ceil($totalItems / $items_pp);


        $this->view->title = "Próximos Eventos - Traileros";
        
        $this->view->carreras = $this->model->getPaginated($items_pp, $offset, $order);
        $this->view->currentPage = $currentPage;
        $this->view->totalPages = $totalPages;
        $this->view->currentOrder = $order;

        $this->view->render('carrera/main/index');
    }

    /*
        Método: new
        Descripción: Muestra el formulario para crear una nueva carrera
    */
    function new() {

        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['carrera']['new']);

        if(empty($_SESSION['csrf_token'])){
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Objeto vacío para el formulario
        $this->view->carrera = new class_carrera();

        // Compruebo si hay mensajes
        $this->checkMessages();

        // Si hay error mantengo valores del formulario
        if (isset($_SESSION['errors'])){
            $this->view->errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
            $this->view->carrera = (array) $_SESSION['carrera'];
            unset($_SESSION['carrera']);
            $this->view->error = "Errores en el formulario";
        } else {
            // Si no hay errores, creamos el objeto vacío desde la clase
            $this->view->carrera = (array) new class_carrera();
        }

        $this->view->title = "Añadir Evento - Traileros";

        $this->view->render('carrera/new/index');
    }

    /*
        Método: create
        Descripción: Procesa el envío del formulario de nueva carrera
    */
    public function create() {
        
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['carrera']['create']);

        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $this->handleError();
        }

        // 1. Saneamiento de datos de texto
        $nombre = filter_var($_POST['nombre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $fecha = $_POST['fecha'] ?? '';
        $fecha_cierre = $_POST['fecha_cierre_inscripcion'] ?? $fecha;
        $ubicacion = filter_var($_POST['ubicacion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $dificultad = filter_var($_POST['dificultad'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $descripcion = filter_var($_POST['descripcion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $edad_minima = filter_var($_POST['edad_minima'] ?? 18, FILTER_SANITIZE_NUMBER_INT);
        $edad_maxima = filter_var($_POST['edad_maxima'] ?? 99, FILTER_SANITIZE_NUMBER_INT);
        $organizador_id = (int) ($_POST['organizador_id'] ?? $_SESSION['user_id']);
        $estado = $_POST['estado'] ?? 'borrador';

        // 2. Lógica de subida de Imagen
        $nombreImagen = 'default.png'; // Imagen por defecto
        $error = [];

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                
                $fileTmpPath = $_FILES['imagen']['tmp_name'];
                $fileName = $_FILES['imagen']['name'];
                $fileSize = $_FILES['imagen']['size'];
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                // Validar extensión
                if (in_array($fileExtension, $allowedExtensions)) {
                    // Validar tamaño (5MB)
                    if ($fileSize <= 5 * 1024 * 1024) {
                        
                        // Generar nombre único
                        $nuevoNombreImagen = md5(time() . $fileName) . '.' . $fileExtension;
                        $uploadFileDir = 'public/assets/img/carreras/';
                        
                        // Crear directorio si no existe (por seguridad)
                        if (!is_dir($uploadFileDir)) {
                            mkdir($uploadFileDir, 0777, true);
                        }

                        $dest_path = $uploadFileDir . $nuevoNombreImagen;

                        if ($this->resizeImage($fileTmpPath, $dest_path, 1200, null)) {
                            $nombreImagen = $nuevoNombreImagen;
                        } else {
                            $error['imagen'] = "Error al mover el archivo al servidor.";
                        }
                    } else {
                        $error['imagen'] = "La imagen excede el máximo de 5MB.";
                    }
                } else {
                    $error['imagen'] = "Formato no permitido (JPG, PNG, WEBP).";
                }
            } else {
                $error['imagen'] = "Error en la subida del archivo.";
            }
        }

        // 3. Recoger y sanear MODALIDADES del formulario
        $modalidades = [];
        $mod_nombres = $_POST['mod_nombre'] ?? [];
        $mod_distancias = $_POST['mod_distancia'] ?? [];
        $mod_desniveles = $_POST['mod_desnivel'] ?? [];
        $mod_precios = $_POST['mod_precio'] ?? [];
        $mod_cupos = $_POST['mod_cupo'] ?? [];
        $mod_edades_min = $_POST['mod_edad_minima'] ?? []; 
        $mod_edades_max = $_POST['mod_edad_maxima'] ?? [];

        foreach ($mod_nombres as $i => $val) {
            $modalidades[] = [
                'nombre'      => filter_var($val, FILTER_SANITIZE_SPECIAL_CHARS),
                'distancia'   => filter_var($mod_distancias[$i], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                'desnivel'    => filter_var($mod_desniveles[$i], FILTER_SANITIZE_NUMBER_INT),
                'precio'      => filter_var($mod_precios[$i], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                'cupo_maximo' => filter_var($mod_cupos[$i] ?? $_POST['cupo_maximo'], FILTER_SANITIZE_NUMBER_INT),
                'edad_minima' => filter_var($mod_edades_min[$i] ?? 18, FILTER_SANITIZE_NUMBER_INT),
                'edad_maxima' => filter_var($mod_edades_max[$i] ?? 99, FILTER_SANITIZE_NUMBER_INT)
            ];
        }

        // 4. Validaciones de negocio
        if(empty($nombre)) $error['nombre'] = "El nombre es obligatorio";
        if(empty($fecha)) $error['fecha'] = "La fecha es obligatoria";
        if(empty($modalidades)) $error['modalidades'] = "Debes añadir al menos una modalidad";

        // Si hay errores, redirigir
        if(!empty($error)){
            $_SESSION['errors'] = $error;
            header('Location: ' . URL . 'carrera/new');
            exit();
        }

        // 5. Guardar en BD pasando los datos generales + el array de modalidades
        // Creamos un array asociativo o un objeto que contenga todo
        $datosEvento = [
            'nombre' => $nombre,
            'fecha' => $fecha,
            'fecha_cierre_inscripcion' => $fecha_cierre,
            'ubicacion' => $ubicacion,
            'dificultad' => $dificultad,
            'descripcion' => $descripcion,
            'imagen' => $nombreImagen,
            'organizador_id' => $organizador_id,
            'estado' => $estado
        ];

        if ($this->model->create($datosEvento, $modalidades)) {
            $_SESSION['notify'] = "¡Carrera publicada correctamente!";
            header('Location: ' . URL . 'carrera');
            exit();
        } else {
            $this->handleError();
        }
    }

    /*
        Método: edit
        Descripción: Carga datos para editar una carrera existente
    */
    public function edit($params) {
        
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['carrera']['edit']);

        $id = (int) $params[0];
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        $carrera = $this->model->read($id);
        $modalidades = $this->model->getModalidadesByEvento($id);

        if (!$carrera) {
            // SI NO EXISTE: llamamos al método error
            $this->errorNotFound($id);
        }

        $this->view->carrera = $carrera;
        $this->view->modalidades = $modalidades;

        $this->view->id = $id;

        // Compruebo si hay mensajes
        $this->checkMessages();

        // Si hay errores mantengo los valores del form
        if (isset($_SESSION['errors'])) {
            $this->view->errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
            $this->view->carrera = (array) $_SESSION['carrera'];
            unset($_SESSION['carrera']);
            $this->view->error = "Errores en el formulario";
        }

        $this->view->title = "Editar Evento - Traileros";

        $this->view->render('carrera/edit/index');
    }

    /*
        Método: update
        Descripción: Actualiza los datos de una carrera existente
    */
    public function update($params) {
        
        $this->requireLogin();
        
        $this->requirePrivilege($GLOBALS['carrera']['update']);

        $id = (int) $params[0];

        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $this->handleError();
        }

        // 1. Saneamiento de datos
        $nombre = filter_var($_POST['nombre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $fecha = filter_var($_POST['fecha'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $ubicacion = filter_var($_POST['ubicacion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $distancia = filter_var($_POST['distancia'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $desnivel = filter_var($_POST['desnivel'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $dificultad = filter_var($_POST['dificultad'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $descripcion = filter_var($_POST['descripcion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $cupo_maximo = filter_var($_POST['cupo_maximo'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $precio = filter_var($_POST['precio'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $organizador_id = (int) ($_POST['organizador_id'] ?? $_SESSION['user_id']);
        $estado = filter_var($_POST['estado'] ?? 'borrador', FILTER_SANITIZE_SPECIAL_CHARS);
        $fecha_cierre = filter_var($_POST['fecha_cierre_inscripcion'] ?? $_POST['fecha'], FILTER_SANITIZE_SPECIAL_CHARS);
        $edad_minima = filter_var($_POST['edad_minima'] ?? 18, FILTER_SANITIZE_NUMBER_INT);
        $edad_maxima = filter_var($_POST['edad_maxima'] ?? 99, FILTER_SANITIZE_NUMBER_INT);

        // 2. Gestión de la Imagen
        // Por defecto cojo el nombre que viene del campo oculto
        $nombreImagen = (!empty($_POST['imagen_actual'])) ? $_POST['imagen_actual'] : 'default.png';
        $error = [];

        // Si hay un archivo de imagen cojo los metadatos y defino posibles extensiones
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            
            $fileTmpPath = $_FILES['imagen']['tmp_name'];
            $fileName = $_FILES['imagen']['name'];
            $fileSize = $_FILES['imagen']['size'];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Compruebo que la extensión esta permitida
            if (in_array($fileExtension, $allowedExtensions)) {
                // Si se pasa de tamaño (5mb) la redimensionamos
                if ($fileSize <= 5 * 1024 * 1024) {                  
                    $nuevoNombreImagen = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = 'public/assets/img/carreras/';
                    $dest_path = $uploadFileDir . $nuevoNombreImagen;

                    if ($this->resizeImage($fileTmpPath, $dest_path, 1200, null)) {                       
                        // Borra imagen vieja si no es la default y tiene un nombre distinto al nuevo
                        if ($nombreImagen !== 'default.png' && $nombreImagen !== $nuevoNombreImagen) {
                            $oldFile = $uploadFileDir . $nombreImagen;
                            // Verificar que es un archivo
                            if (is_file($oldFile)) {
                                unlink($oldFile);
                            }
                        }
                        // Actualizamos la variable con el nuevo nombre para la BD
                        $nombreImagen = $nuevoNombreImagen;
                    } else {
                        $error['imagen'] = "Error al mover la nueva imagen.";
                    }
                } else {
                    $error['imagen'] = "La nueva imagen es demasiado grande.";
                }
            } else {
                $error['imagen'] = "Formato de imagen no permitido.";
            }
        }

        // 3. Recoger y sanear MODALIDADES (Arrays del formulario)
        $modalidades = [];
        $mod_nombres = $_POST['mod_nombre'] ?? [];
        $mod_distancias = $_POST['mod_distancia'] ?? [];
        $mod_desniveles = $_POST['mod_desnivel'] ?? [];
        $mod_precios = $_POST['mod_precio'] ?? [];

        foreach ($mod_nombres as $i => $val) {
            $modalidades[] = [
                'nombre'    => filter_var($val, FILTER_SANITIZE_SPECIAL_CHARS),
                'distancia' => filter_var($mod_distancias[$i], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                'desnivel'  => filter_var($mod_desniveles[$i], FILTER_SANITIZE_NUMBER_INT),
                'precio'    => filter_var($mod_precios[$i], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                'cupo_maximo' => filter_var($_POST['mod_cupo'][$i], FILTER_SANITIZE_NUMBER_INT),
                'edad_minima' => filter_var($_POST['mod_edad_minima'][$i], FILTER_SANITIZE_NUMBER_INT),
                'edad_maxima' => filter_var($_POST['mod_edad_maxima'][$i], FILTER_SANITIZE_NUMBER_INT)
            ];
        }

        // 4. Crear objeto para validación y persistencia
        $carrera = new class_carrera(
            $id, 
            $nombre, 
            $fecha, 
            $fecha_cierre, 
            $ubicacion, 
            $distancia, 
            $desnivel, 
            $dificultad, 
            $descripcion, 
            $cupo_maximo, 
            $precio,
            $edad_minima,
            $edad_maxima, 
            $nombreImagen, 
            $organizador_id, 
            $estado);

        // 5. Validaciones
        if(empty($nombre)) $error['nombre'] = "El nombre es obligatorio";
        if(empty($fecha)) $error['fecha'] = "La fecha es obligatoria";

        if(!empty($error)){
            $_SESSION['errors'] = $error;
            $_SESSION['carrera'] = (array) $carrera;
            header('Location: ' . URL . 'carrera/edit/' . $id);
            exit();
        }

        // 6. Actualizar en BD
        if ($this->model->update($carrera, $id, $modalidades)) {
            $_SESSION['notify'] = "¡Carrera y modalidades actualizadas correctamente!";
            header('Location: ' . URL . 'carrera/show/' . $id);
            exit();
        } else {
            $this->handleError();
        }
    }

    /*
        Método: show
        Descripción: Muestra detalles de la carrera (Solo lectura)
    */
    public function show($params) {

        $id = (int) $params[0];
        $carrera = $this->model->read($id);

        if (!$carrera) {
            // SI NO EXISTE: Cargamos el metodo de error no encontrado.
            $this->errorNotFound($id);
            return;
        }

        // Compruebo si hay mensajes
        $this->checkMessages();

        // Obtener las modalidades para este evento
        $modalidades = $this->model->getModalidadesByEvento($id);

        // Calculamos las plazas libres para cada modalidad
        foreach ($modalidades as $key => $mod) {
            $ocupadas = $this->model->getPlazasOcupadas($mod['id']);
            $modalidades[$key]['plazas_libres'] = $mod['cupo_maximo'] - $ocupadas;
        }

        // Lógica para saber si hay plazas libres
        $total_libres = array_sum(array_column($modalidades, 'plazas_libres'));

        // Lógica para saber si la carrera ya ha terminado
        $hoy = date('Y-m-d');
        $fecha_carrera = date('Y-m-d', strtotime($carrera['fecha']));

        // Lógica para saber si existen resultados publicados (T/F)
        $tiene_resultados = $this->model->hasResults($id);
        $this->view->tiene_resultados = $tiene_resultados;

        // Preparamos variables para la vista
        $this->view->modalidades = $modalidades;
        $this->view->carrera = $carrera;
        $this->view->finalizada = ($hoy >= $fecha_carrera);
        $this->view->hay_plazas = ($total_libres>0);

        if ($carrera && isset($carrera['nombre'])) 
            $this->view->title = $carrera['nombre'] . " - Traileros";
        else 
            $this->view->title = "Carrera no encontrada";

        $this->view->render('carrera/show/index');
    }

    /*
        Método: delete
        Descripción: Elimina un evento
    */
    public function delete($params) {
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['carrera']['delete']);

        // 1. Validar que la petición sea POST y el CSRF sea correcto
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $this->handleError(); // O redirigir con un mensaje de error
            exit();
        }

        // Obtenemos el id de la carrera a eliminar
        $id = (int) $params[0];

        // 2. Obtener datos para borrar la imagen
        $carrera = $this->model->read($id);

        if ($carrera) {
            $imagen = is_array($carrera) ? $carrera['imagen'] : $carrera->imagen;

            if ($imagen !== 'default.png') {
                $ruta = 'public/assets/img/carreras/' . $imagen;
                if (file_exists($ruta)) {
                    unlink($ruta);
                }
            }

            // 3. Borra de la base de datos
            if ($this->model->delete($id)) {

                // Borra tambien el archivo físico del directorio de images
                if ($imagen && $imagen !== 'default.png'){
                    $ruta = 'public/assets/img/carreras' . $imagen;
                    if (file_exists($ruta) && is_file($ruta)) unlink($ruta);
                }

                $_SESSION['notify'] = "Carrera eliminada permanentemente.";
            } else {
                $_SESSION['error'] = "Error al eliminar la carrera.";
            }
        }

        header('Location: ' . URL . 'carrera');
        exit();
    }

    /*
        Método: order
        Descripción: Ordena la lista de carreras según el criterio seleccionado
        Parámetros: $param (id del criterio de ordenación)
    */
    public function order($param) {

        // Recogemos el criterio de ordenación del parámetro
        $criterio = $param[0];

        // Compruebo si hay mensajes
        $this->checkMessages();

        // Título de la página
        $this->view->title = "Explorar Carreras - Traileros";

        // Llamamos al método order del modelo
        // El modelo ya tiene la lógica para mapear 1->id, 2->nombre, etc.
        $this->view->carreras = $this->model->order($criterio);

        // Renderizamos la vista principal (main/index) con los datos ordenados
        $this->view->render('carrera/main/index');
    }

    /*
        Método: search
        Descripción: Filtra las carreras según un término de búsqueda
    */
    public function search() {

        // Validar que el término de búsqueda existe y no está vacío
        $term = isset($_GET['term']) ? trim($_GET['term']) : '';
        $this->view->term = $term;

        if ($term === '') {
            // Si el buscador está vacío, cargamos las carreras normales (con o sin paginación)
            $this->view->carreras = $this->model->get(); 
        } else {
            // Si hay término, filtramos
            $this->view->carreras = $this->model->search($term);
        }

        // Compruebo si hay mensajes
        $this->checkMessages();

        // Título de la página indicando la búsqueda
        $this->view->title = "Resultados de búsqueda: \"$term\" - Traileros";

        // Renderizamos la misma vista principal
        $this->view->render('carrera/main/index');
    }

    /*
        MÉTODO: inscribir
        Descripción: Permite a un corredor inscribirse en la carrera
    */
    public function inscribir($params) {

        $this->requireLogin();
        
        $evento_id = (int) $params[0];
        $user_id = $_SESSION['user_id'];
        $metodo_pago = "Efectivo/Transferencia"; // Valor por defecto o de formulario

        if ($this->model->inscribir($user_id, $evento_id, $metodo_pago)) {
            $_SESSION['notify'] = "Inscripción realizada con éxito. ¡Nos vemos en la meta!";
        } else {
            $_SESSION['notify'] = "Error: Puede que ya estés inscrito en este evento.";
        }
        
        header('Location: ' . URL . 'carrera');
        exit();
    }

    // Panel de control de eventos
    public function gestion(){
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['carrera']['gestion']);

        $this->view->title = "Panel de Gestión de Carreras";

        $this->model->actualizarEstados();

        $this->view->eventos = $this->model->getEventosPorRol($_SESSION['user_id'], $_SESSION['role_id']);

        $this->view->render('carrera/gestion/index');
    }

    public function order_gestion($param){
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['carrera']['gestion']);

        // Extrae el criterio de ordenación. Por defecto será 1
        $criterio = (isset($param[0])) ? (int)$param[0] : 1;

        $this->model->actualizarEstados();

        $this->view->title = "Panel de Gestión de Carreras";

        $this->view->eventos = $this->model->getEventosPorRolOrdenados($_SESSION['user_id'], $_SESSION['role_id'], (int)$criterio);

        $this->view->render('carrera/gestion/index');
    }

    public function search_gestion($param = null) {
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['carrera']['gestion']);
        
        // Obtenemos el término de búsqueda del POST (formulario)
        $term = $param[0] ?? $_POST['term'] ?? $_GET['term'] ?? '';

        // si no hay termino de búsqueda vamos a la vista normal
        if (trim($term) === '') {
            header('location: ' . URL . 'carrera/gestion');
            exit;
        }
        
        $this->view->title = "Buscando: " . htmlspecialchars($term);

        // Llamamos a un método específico del modelo para buscar en la gestión
        $this->view->eventos = $this->model->searchEventosPorRol($_SESSION['user_id'], $_SESSION['role_id'], $term);

        $this->view->render('carrera/gestion/index');
    }

    /* --- Métodos privados de seguridad --- */
    private function requirePrivilege($allowedRoles){
        if (!in_array($_SESSION['role_id'], $allowedRoles)){
            $_SESSION['notify'] = "No tienes permisos para realizar esta acción";
            header('Location: ' . URL . 'carrera');
            exit();
        }
    }
    
    private function requireLogin(){
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['notify'] = "Acceso restringido. Por favor, inicia sesión.";
            header('Location: ' . URL . 'auth/login');
            exit();
        }
    }

    // Métodos para el redimensionado de las imágenes que se suben
    private function resizeImage($tmp_name, $destination, $targetWidth, $targetHeight = null) {
        // Obtener metadatos de la imagen
        list ($width, $height, $type) = getimagesize($tmp_name);

        // Calcular alto proporcional si no se define altura
        if ($targetHeight === null) {
            $ratio = $width / $height;
            $targetHeight = (int)($targetWidth / $ratio);
        }

        // Crear lienzo vacío
        $newImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // cargar imagen original según su tipo
        switch ($type){
            case IMAGETYPE_JPEG: $source = imagecreatefromjpeg($tmp_name); break;
            case IMAGETYPE_PNG: $source = imagecreatefrompng($tmp_name); break;
            case IMAGETYPE_WEBP: $source = imagecreatefromwebp($tmp_name); break;
            default: return false;
        }

        // Verificar si la imagen se ha cargado bien antes de continuar
        if (!$source) return false;

        // Mantener transparencias en PNG/WEBP
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);

        // Redimensionar realizando una copia de la original
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        // Guardar la imagen redimensionada
        switch ($type) {
            case IMAGETYPE_JPEG: imagejpeg($newImage, $destination, 80); break;
            case IMAGETYPE_PNG: imagepng($newImage, $destination, 8); break;
            case IMAGETYPE_WEBP: imagewebp($newImage, $destination, 80); break;
        }

        // Liberar memoria
        imagedestroy($newImage);
        imagedestroy($source);

        return true;
    }

    private function handleError() {
        header('location:' . URL . 'error');
        exit();
    }

    // Método de error not found
    private function errorNotFound($id) {
        $this->view->tipo = "404";
        $this->view->titulo = "Recurso no encontrado";
        $this->view->mensaje = "Lo sentimos, el elemento con ID $id no existe.";
        $this->view->render('error/index');
        exit;
    }
}