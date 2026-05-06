<?php
class Account extends Controller
{

    function __construct()
    {

        parent::__construct();
        // Iniciamos sesión una sola vez para todos los métodos de este controlador
        if (session_status() == PHP_SESSION_NONE) {
            sec_session_start();
        }

        // Dejamos que el Framework cargue el modelo.
        $this->loadModel('account');

    }

    /*
        Método principal
        Se  carga siempre que la url contenga sólo el primer parámetro
        url: /account
    */
    public function render()
    {

        // Comprobar si hay un usuario logueado
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['account']['render']);

        // Crear token csrf
        $this->generateTokenCsrf();

        // Compruebo si hay mensaje de éxito
        $this->checkMessages();

        // Comprobar si el perfil está completo
        $this->cargarDatosMenu();

        // Obtenemos los detalles completos del usuario
        $this->view->account = $this->model->read($_SESSION['user_id']);

        // Creo la propiedad title de la vista
        $this->view->title = $_SESSION['user_name'] . " - Mi cuenta - Traileros";

        $this->view->render('account/main/index');
    }

    /*
        Método para actualizar los datos del usuario. 
        Muestra en la vista el formulario con los datos del usuario en modo edición. 
        url: /account/edit
        @param $id int : id del usuario

    */
    public function edit()
    {

        // Comprobar si hay un usuario logueado
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['account']['edit']);

        // Crear un token CSRF para los formularios
        // Por si el usuario abre dos pestañas simultáneas del mismo formulario
        $this->generateTokenCsrf();

        // Compruebo si hay mensaje de éxito
        $this->checkMessages();

        // Comprobar si el perfil está completo
        $this->cargarDatosMenu();

        // Obtenemos el id del usuario
        $id = $_SESSION['user_id'];

        // Obtenemos los detalles completos del usuario
        $this->view->account = $this->model->read($id);

        // Capa no validación del formulario
        if (isset($_SESSION['errors'])) {

            // Creo la propiedad error en la vista
            $this->view->errors = $_SESSION['errors'];

            // Elimino la variable de sesión error
            unset($_SESSION['errors']);

            // Asigno a perfil los detalles del formulario
            $this->view->account = $_SESSION['account'];

            // Elimino la variable de sesión perfil
            unset($_SESSION['account']);

            // Creo la propiedad mensaje error
            $this->view->error = 'Revise los errores del formulario';
        }

        // Creo la propiedad title de la vista
        $this->view->title = "Editar cuenta: " . $_SESSION['user_name'];
        $this->view->render('account/edit/index');
    }

    /*
        Método para actualizar los datos del usuario. 
        Actualiza los datos del usuario name y email. 

        Incluye:
         - validación token crsf.
         - validación de los datos del formulario.
         - prevención ataques csrf.

        url: /account/update

    */
    public function update()
    {

        // Comprobar si hay usuario logueado
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['account']['update']);

        // Validacion token CSRF
        $this->checkTokenCsrf($_POST['csrf_token'] ?? '');

        // Saneamos los detalles del formulario
        $nombre = filter_var($_POST['nombre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $apellidos = filter_var($_POST['apellidos'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $dni = filter_var($_POST['dni'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $sexo = $_POST['sexo'] ?? null;
        $fecha_nac = $_POST['fecha_nac'] ?? null;
        $direccion = filter_var($_POST['direccion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $poblacion = filter_var($_POST['poblacion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $provincia = filter_var($_POST['provincia'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $cp = filter_var($_POST['cp'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $pais = filter_var($_POST['pais'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $tlf = filter_var($_POST['tlf'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $tlf_emg = filter_var($_POST['tlf_emg'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $club = filter_var($_POST['club'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $talla = $_POST['talla'] ?? null;
        $es_federado = isset($_POST['es_federado']) ? (int)$_POST['es_federado'] : 0;
        $num_licencia = filter_var($_POST['num_licencia'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        // Obtengo los detalles del usuario
        $account_actual = $this->model->read($_SESSION['user_id']);

        // Validación de los datos del formulario
        $errors = [];

        // Validación name
        // antes de validar compruebo se ha modificado
        if ($nombre != $account_actual->nombre) {
            if (empty($nombre)) {
                $errors['name'] = 'El nombre es obligatorio';
            } else if (strlen($nombre) < 5) {
                $errors['name'] = 'El nombre debe tener al menos 5 caracteres';
            } else if (strlen($nombre) > 20) {
                $errors['name'] = 'El nombre debe tener como máximo 20 caracteres';
            } else if (!$this->model->validate_unique_name($nombre)) {
                $errors['name'] = 'Nombre usuario existente';
            }
        }

        // validación email
        // antes de validar compruebo se ha modificado
        if ($email != $account_actual->email) {
            if (empty($email)) {
                $errors['email'] = 'El email es obligatorio';
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'El email no es válido';
            } else if (!$this->model->validate_unique_email($email, $_SESSION['user_id'])) {
                $errors['email'] = 'Email existente';
            }
        }

        // Si hay errores
        if (!empty($errors)) {
            // Creo la variable de sesión error
            $_SESSION['errors'] = $errors;

            // Creo la variable de sesión perfil
            $_SESSION['account'] = (object) $_POST;

            // Redirecciono al formulario de edición
            header('location:' . URL . 'account/edit');
            exit();
        }

        $role_id = $this->model->get_user_role_id($_SESSION['user_id']);

        // Creamos el objeto user con los datos recogidos del formulario
        $user_updated = new class_user(
            $_SESSION['user_id'],      // id
            $nombre,                   // nombre
            $email,                    // email
            $account_actual->password, // password (mantenemos el hash actual)
            $account_actual->avatar,   // avatar (mantenemos el actual)
            $apellidos,                // apellidos
            $sexo,                     // sexo
            $fecha_nac,                // fecha_nac
            $dni,                      // dni
            $tlf,                      // tlf
            $tlf_emg,                  // tlf_emg
            $direccion,                // direccion
            $poblacion,                // poblacion
            $provincia,                // provincia
            $cp,                       // cp
            $pais,                     // pais
            $club,                     // club
            $talla,                    // talla
            $es_federado,              // es_federado
            $num_licencia,             // num_licencia
            $account_actual->created_at, // created_at
            null,                      // updated_at (lo pondrá la BD)
            $role_id   // role_id 
        );

        // Actualizo los datos del usuario
        $this->model->update($user_updated, $_SESSION['user_id'], $role_id);

        // Actualizo el posible nuevo nombre del usuario
        $_SESSION['user_name'] = $nombre;

        // Genero mensaje de éxito
        $_SESSION['notify'] = 'Perfil actualizado correctamente';

        // Redirecciono a la vista principal de perfil
        header('location:' . URL . 'account');
    }

    /*
        Método para cambiar la contraseña del usuario. 
        Muestra en la vista el formulario para cambiar la contraseña. 

        url: /account/password

    */
    public function password()
    {

        // Crear token csrf
        $this->generateTokenCsrf();

        // Comprobar si hay un usuario logueado y tiene privilegios
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['account']['password']);

        // Compruebo si hay mensajes
        $this->checkMessages();

        // Comprobar si el perfil está completo
        $this->cargarDatosMenu();

        // Capa no validación del formulario
        if (isset($_SESSION['errors'])) {

            // Creo la propiedad error en la vista
            $this->view->errors = $_SESSION['errors'];

            // Elimino la variable de sesión error
            unset($_SESSION['errors']);

            // Creo la propiedad mensaje error
            $this->view->error = 'Formulario con errores, revísalos por favor';
        }

        // Creo la propiedad title de la vista
        $this->view->title = "Seguridad";

        $this->view->render('account/password/index');

    }

    /*
        Método para actualizar la contraseña del usuario. 
        Actualiza la contraseña del usuario. 

        Incluye:
         - validación token crsf.
         - validación de los datos del formulario.
         - prevención ataques csrf.

        url: /account/update_password

    */
    public function update_password()
    {
        // Comprobar si hay usuario logueado
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['account']['update_password']);

        // Validacion token CSRF
        $this->checkTokenCsrf($_POST['csrf_token'] ?? '');

        // Saneamos los detalles del formulario
        $password = $_POST['password'] ??= null;
        $new_password = filter_var($_POST['new_password'] ??= null, FILTER_SANITIZE_SPECIAL_CHARS);
        $confirm_password = filter_var($_POST['confirm_password'] ??= null, FILTER_SANITIZE_SPECIAL_CHARS);

        // Obtengo los detalles del usuario
        $account = $this->model->read($_SESSION['user_id']);

        // validación de los datos del formulario
        $errors = [];

        // validación password
        if (empty($password)) {
            $errors['password'] = 'Introduce el password actual';
        } else if (!password_verify($password, $account->password)) {
            $errors['password'] = 'El password actual no es correcto';
        }

        // validación new_password
        if (empty($new_password)) {
            $errors['new_password'] = 'El nuevo password es obligatorio';
        } else if (strlen($new_password) < 7) {
            $errors['new_password'] = 'El nuevo password debe tener al menos 7 caracteres';
        } else if (strcmp($new_password, $confirm_password) !== 0) {
            $errors['new_password'] = 'Passwords no coincidentes';
        }

        // Si hay errores
        if (!empty($errors)) {
            // Creo la variable de sesión error
            $_SESSION['errors'] = $errors;

            // Redirecciono al formulario de edición
            header('location:' . URL . 'account/password');
            exit();
        }

        // Limpiamos errores previos de la sesión
        unset($_SESSION['errors']);

        // Actualizo password del usuario
        $this->model->update_pass($new_password, $_SESSION['user_id']);

        // Genero mensaje de éxito
        $_SESSION['notify'] = 'Password actualizado correctamente';

        // Enviar correo informativo:
        $asunto = "Cambio de contraseña";
        $cuerpo = "<h1>Hola {$account->name}</h1><p>Su contraseña ha sido modificada. Si no ha sido usted, por favor, pongase en contacto con nosotros</p>";
        
        Email::enviar($account->email, $asunto, $cuerpo);

        // Redirecciono a la vista principal de perfil
        header('location:' . URL . 'account');
        exit();
    }

    /*
        delete()
        Método para eliminar el usuario. 
        Elimina el usuario de la base de datos. 
        url: /account/delete
    */
    public function delete()
    {

        // Comprobar si hay un usuario logueado
        $this->requireLogin();

        // Crear un token CSRF para los formularios
        // Por si el usuario abre dos pestañas simultáneas del mismo formulario
        $this->generateTokenCsrf();

        // Comprobamos si hay algún mensaje para mostrar:
        $this->checkMessages();

        // Comprobar si el perfil está completo
        $this->cargarDatosMenu();

         # Obtenemos los detalles completos del usuario
        $this->view->account = $this->model->read($_SESSION['user_id']);

        // Creo la propiedad title de la vista
        $this->view->title = "Eliminar Cuenta";

        $this->view->render('account/delete/index');

        
    }

    public function delete_confirmed()
    {

        // Comprobar si hay un usuario logueado
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['account']['delete_confirmed']);

        // Validacion token CSRF
        $this->checkTokenCsrf($_POST['csrf_token'] ?? '');

        // Elimino el usuario
        $this->model->delete($_SESSION['user_id']);

        // Cierro la sesión
        session_destroy();

        // Elimino la cookie de sesión
        setcookie(session_name(), '', time() - 3600);

        // Genero mensaje de éxito
        $_SESSION['notify'] = 'Cuenta usuario eliminada correctamente';

        // Redirecciono a la vista principal de perfil
        header('location:' . URL . 'auth/login');
        exit();
    }

    /*
        Método: requireLogin
        Descripción: Verifica que el usuario ha iniciado sesión
    */
    private function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['notify'] = "Debes iniciar sesión para acceder al sistema";
            header('Location: ' . URL . 'auth/login');
            exit();
        }
    }

    /*
        Método: requirePrivilege
        Descripción: Verifica que el usuario tiene permisos para una acción.
    */
    private function requirePrivilege($allowedRoles){
        if (!in_array($_SESSION['role_id'], $allowedRoles)){
            $_SESSION['notify'] = "No tienes permisos para realizar esta acción";
            header('Location: ' . URL . 'carrera');
            exit();
        }
    }

    public function uploadAvatar()
    {
        // 1. Verificamos que sea una petición POST y llegue el archivo
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['avatar'])) {
            
            $file = $_FILES['avatar'];
            $userID = $_SESSION['user_id'];

            // comprobación de errores nativos de subida en php
            if ($file['error'] !== UPLOAD_ERR_OK) {
            $phpErrors = [
                UPLOAD_ERR_INI_SIZE   => 'El archivo es demasiado grande para la configuración del servidor.',
                UPLOAD_ERR_FORM_SIZE  => 'El archivo excede el límite permitido por el formulario.',
                UPLOAD_ERR_PARTIAL    => 'La subida se interrumpió, intenta de nuevo.',
                UPLOAD_ERR_NO_FILE    => 'No se ha seleccionado ningún archivo.',
                UPLOAD_ERR_NO_TMP_DIR => 'Error del servidor: falta carpeta temporal.',
                UPLOAD_ERR_CANT_WRITE => 'Error del servidor: no se pudo escribir en el disco.',
            ];
            $errorMsg = $phpErrors[$file['error']] ?? 'Error desconocido en la subida.';
            echo json_encode(['success' => false, 'error' => $errorMsg]);
            return;
        }
            
            // 2. Validaciones de seguridad
            $maxSize = 2 * 1024 * 1024;
            if ($file['size'] > $maxSize){
                echo json_encode(['success' => false, 'error' => 'El archivo excede el límite de 2MB']);
                return;
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($file['type'], $allowedTypes)) {
                echo json_encode(['success' => false, 'error' => 'Formato no permitido (Solo JPG, PNG, WEBP)']);
                return;
            }

            // 3. Definir nombre y ruta
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = 'avatar_' . $userID . '_' . time() . '.' . $extension;
            $uploadDir = 'public/assets/img/avatars/';
            
            // Crear la carpeta si no existe
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $uploadPath = $uploadDir . $fileName;

            $oldAvatar = $_SESSION['user_avatar'] ?? null;

            // 4. Redimensionar imagen y mover a su carpeta (public/assets/img/avatars)
            if ($this->resizeImage($file['tmp_name'], $uploadPath, 400, 400)) {

                // Si ya existe un archivo de imagen previo, lo borramos
                if ($oldAvatar && file_exists($oldAvatar)) {
                    // Evitamos borrar imágenes por defecto
                    if (strpos($oldAvatar, 'default') === false) {
                        unlink($oldAvatar);
                    }
                }
                
                // 5. Llamar al modelo para guardar la ruta en la BD
                $this->model->updateAvatar($userID, $uploadPath);
                
                // 6. Actualizar la sesión para que el cambio sea instantáneo en toda la web
                $_SESSION['user_avatar'] = $uploadPath;

                echo json_encode([
                    'success' => true, 
                    'newImageUrl' => URL . $uploadPath
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'No se pudo guardar el archivo en el servidor']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Petición no válida']);
        }
    }

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


    public function request_upgrade() {
        $this->requireLogin();

        // 1. Obtenemos los datos frescos del usuario desde el modelo
        $id_usuario = $_SESSION['user_id'];

        // Comprobamos que el perfil está completo
        if (!$this->model->isProfileComplete($id_usuario)) {
            $_SESSION['error'] = "No puedes solicitar ser organizador sin completar tu perfil primero.";
            header('Location: ' . URL . 'account');
            exit();
        }

        // Comprobamos que no es usuario organizador o admin
        if ($_SESSION['role_id'] < 3) {
            $_SESSION['error'] = "Ya tienes privilegios de gestión.";
            header('Location: ' . URL . 'account');
            exit();
        }

        // Registra la solicitud en la tabla 'upgrade_requests'
        if ($this->model->create_upgrade_request($_SESSION['user_id'])) {
            $_SESSION['notify'] = "Solicitud enviada con éxito. El administrador la revisará pronto.";
        } else {
            $_SESSION['error'] = "Hubo un error al procesar tu solicitud.";
        }

        header('Location: ' . URL . 'account');
        exit();
    }   

}
