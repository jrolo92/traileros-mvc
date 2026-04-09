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

        if (isset($_SESSION['notify'])){
            $this->view->notify = $_SESSION['notify'];
            unset($_SESSION['notify']);
        }

        $this->view->title = "Próximos Eventos - Traileros";
        
        // El método get() del modelo ahora trae el nombre del organizador
        $this->view->carreras = $this->model->get();

        $this->view->render('carrera/main/index');
    }

    /*
        Método: new
        Descripción: Muestra el formulario para crear una nueva carrera
    */
    function new() {
        // sec_session_start();
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['carrera']['new']);

        if(empty($_SESSION['csrf_token'])){
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Objeto vacío para el formulario
        $this->view->carrera = new class_carrera();

        if (isset($_SESSION['errors'])){
            $this->view->errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
            $this->view->carrera = (object) $_SESSION['carrera'];
            unset($_SESSION['carrera']);
            $this->view->error = "Errores en el formulario";
        } else {
            // Si no hay errores, creamos el objeto vacío desde la clase
            $this->view->carrera = new class_carrera();
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
        $fecha = filter_var($_POST['fecha'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $ubicacion = filter_var($_POST['ubicacion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $distancia = filter_var($_POST['distancia'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $desnivel = filter_var($_POST['desnivel'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $dificultad = filter_var($_POST['dificultad'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $descripcion = filter_var($_POST['descripcion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $cupo_maximo = filter_var($_POST['cupo_maximo'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $precio = filter_var($_POST['precio'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $organizador_id = (int) ($_POST['organizador_id'] ?? $_SESSION['user_id']);

        // 2. Lógica de subida de Imagen
        $nombreImagen = 'default.jpg'; // Imagen por defecto
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
                        
                        // RUTA CORREGIDA: Incluyendo 'assets' como confirmamos en el test
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

        // 3. Crear objeto con el nombre de la imagen procesado
        $carrera = new class_carrera(null, $nombre, $fecha, $ubicacion, $distancia, $desnivel, $dificultad, $descripcion, $cupo_maximo, $precio, $nombreImagen, $organizador_id);

        // 4. Validaciones de negocio
        if(empty($nombre)) $error['nombre'] = "El nombre es obligatorio";
        if(empty($fecha)) $error['fecha'] = "La fecha es obligatoria";
        if($distancia <= 0) $error['distancia'] = "La distancia debe ser positiva";
        if($cupo_maximo <= 0) $error['cupo_maximo'] = "El cupo debe ser mayor que cero";
        if($precio < 0) $error['precio'] = "El precio no puede ser negativo";

        // Si hay errores, redirigir
        if(!empty($error)){
            $_SESSION['errors'] = $error;
            $_SESSION['carrera'] = $carrera;
            header('Location: ' . URL . 'carrera/new');
            exit();
        }

        // 5. Guardar en BD
        if ($this->model->create($carrera)) {
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
        // sec_session_start();
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['carrera']['edit']);

        $id = (int) $params[0];
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        $carrera = $this->model->read($id);

        if (!$carrera) {
            // SI NO EXISTE: llamamos al método error
            $this->errorNotFound($id);
        }

        $this->view->carrera = $carrera;

        $this->view->id = $id;

        if (isset($_SESSION['errors'])) {
            $this->view->errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
            $this->view->carrera = (object) $_SESSION['carrera'];
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

        // 2. Gestión de la Imagen (La clave del Update)
        // Por defecto, tomamos el nombre que viene del campo oculto
        $nombreImagen = $_POST['imagen_actual'] ?? 'default.jpg';
        $error = [];

        // ¿Se ha seleccionado un archivo nuevo?
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            
            $fileTmpPath = $_FILES['imagen']['tmp_name'];
            $fileName = $_FILES['imagen']['name'];
            $fileSize = $_FILES['imagen']['size'];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (in_array($fileExtension, $allowedExtensions)) {
                if ($fileSize <= 5 * 1024 * 1024) {
                    
                    $nuevoNombreImagen = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = 'public/assets/img/carreras/';
                    $dest_path = $uploadFileDir . $nuevoNombreImagen;

                    if ($this->resizeImage($fileTmpPath, $dest_path, 1200, null)) {
                        
                        // Si se subió la nueva, borramos la vieja físicamente (si no es la default)
                        if ($nombreImagen !== 'default.jpg') {
                            $oldFile = $uploadFileDir . $nombreImagen;
                            if (file_exists($oldFile)) {
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

        // 3. Crear objeto para validación y persistencia
        $carrera = new class_carrera($id, $nombre, $fecha, $ubicacion, $distancia, $desnivel, $dificultad, $descripcion, $cupo_maximo, $precio, $nombreImagen, $organizador_id);

        // 4. Validaciones
        if(empty($nombre)) $error['nombre'] = "El nombre es obligatorio";
        if(empty($fecha)) $error['fecha'] = "La fecha es obligatoria";

        if(!empty($error)){
            $_SESSION['errors'] = $error;
            $_SESSION['carrera'] = $carrera;
            header('Location: ' . URL . 'carrera/edit/' . $id);
            exit();
        }

        // 5. Actualizar en BD
        if ($this->model->update($carrera, $carrera->id)) {
            $_SESSION['notify'] = "¡Carrera actualizada correctamente!";
            header('Location: ' . URL . 'carrera');
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
        }

        // Preparamos variables para la vista
        $this->view->plazas_libres = $this->model->getPlazasDisponibles($id);
        $this->view->carrera = $carrera;
        if ($carrera && isset($carrera['nombre'])) {
            $this->view->title = $carrera['nombre'] . " - Traileros";
        } else {
            $this->view->title = "Carrera no encontrada";
        }
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

        $id = (int) $params[0];

        // 2. Obtener datos para borrar la imagen
        $carrera = $this->model->read($id);

        if ($carrera) {
            // Accedemos como array o objeto según devuelva tu método read()
            // Si read() devuelve un array: $carrera['imagen']
            // Si read() devuelve un objeto: $carrera->imagen
            $imagen = is_array($carrera) ? $carrera['imagen'] : $carrera->imagen;

            if ($imagen !== 'default.jpg') {
                $ruta = 'public/assets/img/carreras/' . $imagen;
                if (file_exists($ruta)) {
                    unlink($ruta);
                }
            }

            // 3. Borrar de la base de datos
            if ($this->model->delete($id)) {
                $_SESSION['notify'] = "Carrera eliminada permanentemente.";
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

        // $this->requireLogin();

        // Capa gestión rol de usuario (usamos el permiso de order)
        // $this->requirePrivilege($GLOBALS['carrera']['order']);

        // Recogemos el criterio de ordenación del parámetro
        $criterio = $param[0];

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
        // sec_session_start();
        // $this->requireLogin();

        // Capa gestión rol de usuario
        // $this->requirePrivilege($GLOBALS['carrera']['search']);

        // Validar que el término de búsqueda existe y no está vacío
        if (isset($_GET['term']) && (!empty($_GET['term']))) {
            $term = $_GET['term'];
        } else {
            // Si el término está vacío, redirigimos al listado general
            header('location:' . URL . 'carrera');
            exit();
        }

        // Título de la página indicando la búsqueda
        $this->view->title = "Resultados de búsqueda: \"$term\" - Traileros";

        // Llamamos al modelo pasándole el término
        $this->view->carreras = $this->model->search($term);

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
            $targetHeight = (int) round ($targetWidth / $ratio);
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