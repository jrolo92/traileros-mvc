<?php

    class User Extends Controller {

        /*
            Crea una instancia del controlador user
            Llama al constructor de la clase padre Controller
            Crea una vista para el controlador user
            Carga el modelo si existe user.model.php

        */
        function __construct() {

            parent::__construct();
            // Iniciamos sesión una sola vez para todos los métodos de este controlador
            if (session_status() == PHP_SESSION_NONE) {
                sec_session_start(true);
            }
        }

        /*
            Método: render
            Descripción: Renderiza la vista del panel de usuarios

        */
        function render() {

            // Capa de validación de sesión
            // Si el usuario no está autenticado
            $this->requireLogin();

            // Capa gestión rol de usuario (Solo los usuario con privilegios pueden acceder a esta función)
            $this->requirePrivilege($GLOBALS['user']['render']);

            // Generar token CSRF:
            if(empty($_SESSION['csrf_token'])){
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            // Comprobar si hay mensajes en la sesión y pasarlos a la vista
            if (isset($_SESSION['notify'])){
                $this->view->notify = $_SESSION['notify'];
                unset($_SESSION['notify']);
            }
                        
            // Obtengo los datos del  modelo para mostrar en la vista      
            // Creo la propiedad  title para la vista
            $this->view->title = "Gestión de Usuarios - Traileros";

            // Obtengo los datos del modelo
            $this->view->users = $this->model->get();

            // Llama a la vista para renderizar la página
            $this->view->render('user/main/index');
        }

        /*
            Método: new
            Descripción: Muestra el formulario para crear un nuevo usuario
        */
        function new() {

            $this->requireLogin();
            $this->requirePrivilege($GLOBALS['user']['new']);

            if(empty($_SESSION['csrf_token'])){
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            // Objeto vacío para el formulario
            $this->view->user = new class_user();

            if (isset($_SESSION['errors'])){
                $this->view->errors = $_SESSION['errors'];
                unset($_SESSION['errors']);
                $this->view->user = $_SESSION['user'];
                unset($_SESSION['user']);
                $this->view->error = "Errores en el formulario";
            }

            $this->view->title = "Formulario Nuevo Usuario";

            // Necesitamos los roles para el select del formulario
            $this->view->roles = $this->model->get_roles();

            $this->view->render('user/new/index');
        }

        /*
            Método: create
            Descripción: Recibe datos para insertar nuevo usuario y su rol
        */
        public function create() {

            $this->requireLogin();
            $this->requirePrivilege($GLOBALS['user']['create']);

            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                $this->handleError();
            }

            // Saneamiento
            $nombre = filter_var($_POST['nombre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            $role_id = filter_var($_POST['role_id'] ?? '', FILTER_SANITIZE_NUMBER_INT);

            $user = new class_user(null, $nombre, $email, $password);
            $error = [];

            // Validaciones
            if(empty($nombre)) $error['nombre'] = "El nombre es obligatorio";
            
            if(empty($email)){
                $error['email'] = "El email es obligatorio";
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $error['email'] = "Formato de email no válido";
            } else if (!$this->model->validate_unique_email($email)){
                $error['email'] = "Este email ya está registrado";
            }

            if(empty($password) || strlen($password) < 6){
                $error['password'] = "La contraseña debe tener al menos 6 caracteres";
            }else if ($password !== $password_confirm) {
                $error['password'] = "Las contraseñas no coinciden";
            }

            if(empty($role_id) || !$this->model->validate_role_exists($role_id)){
                $error['role_id'] = "Debe seleccionar un rol válido";
            }

            if(!empty($error)){
                $_SESSION['errors'] = $error;
                $_SESSION['user'] = $user;
                header('Location: ' . URL . 'user/new');
                exit();
            }

            // Cifrado de contraseña
            $user->password = password_hash($password, PASSWORD_DEFAULT);

            // Avatar
            $user->avatar = 'default-avatar.png';

            // Insertar usuario y obtener ID
            $user_id = $this->model->create($user, $role_id);

            // Asignar rol en la tabla roles_users
            $this->model->assign_role($user_id, $role_id);

            $_SESSION['notify'] = "Usuario creado correctamente";
            header('Location: ' . URL . 'user');
            exit();
        }

        /*
            Método: edit
        */
        public function edit($params) {
  
            $this->requireLogin();
            $this->requirePrivilege($GLOBALS['user']['edit']);

            $id = (int) $params[0];

            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            
            $this->view->user = $this->model->read($id);
            $this->view->id = $id;

            if (isset($_SESSION['errors'])) {
                $this->view->errors = $_SESSION['errors'];
                unset($_SESSION['errors']);
                // Cargamos el objeto que intentó guardar para que no se pierdan los datos
                $this->view->user = $_SESSION['user'];
                unset($_SESSION['user']);
                $this->view->error = "Errores en el formulario";
            }

            $this->view->title = "Editar Usuario - Traileros";
            $this->view->roles = $this->model->get_roles();
            
            // Obtener el rol actual del usuario
            $this->view->user->role_id = $this->model->get_user_role_id($id);

            $this->view->render('user/edit/index');
        }

        /*
            Método: update
        */
        public function update($params) {
            // sec_session_start();
            $this->requireLogin();
            $this->requirePrivilege($GLOBALS['user']['update']);

            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                $this->handleError();
            }

            $id = (int) $params[0];
            $user_db = $this->model->read($id);

            // Saneamiento de los campos
            $nombre = filter_var($_POST['nombre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $role_id = filter_var($_POST['role_id'] ?? '', FILTER_SANITIZE_NUMBER_INT);
            $apellidos = filter_var($_POST['apellidos'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $sexo = $_POST['sexo'] ?? null;
            $fecha_nac = $_POST['fecha_nac'] ?? null;
            $dni = filter_var($_POST['dni'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $tlf = filter_var($_POST['tlf'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $tlf_emg = filter_var($_POST['tlf_emg'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $direccion = filter_var($_POST['direccion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $poblacion = filter_var($_POST['poblacion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $provincia = filter_var($_POST['provincia'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $cp = filter_var($_POST['cp'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $pais = filter_var($_POST['pais'] ?? 'España', FILTER_SANITIZE_SPECIAL_CHARS);
            $club = filter_var($_POST['club'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $talla = $_POST['talla'] ?? null;
            $num_licencia = filter_var($_POST['num_licencia'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            // Checkbox: si no llega es 0, si llega es 1
            $es_federado = isset($_POST['es_federado']) ? 1 : 0;

            // Gestión del avatar
            $avatar = $user_db->avatar; // Por defecto mantenemos el actual
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['avatar']['tmp_name'];
                $fileName = $_FILES['avatar']['name'];
                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $newFileName = md5(time() . $nombre) . '.' . $extension;
                $uploadFileDir = 'public/assets/img/avatars/';

                if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                    // Borrar el anterior si no es el default
                    if ($avatar !== 'default-avatar.png' && file_exists($uploadFileDir . $avatar)) {
                        unlink($uploadFileDir . $avatar);
                    }
                    $avatar = $newFileName;
                }
            }

            $user_act = new class_user
                ($id, $nombre, $email, null, $avatar, $apellidos, $sexo, $fecha_nac, 
                $dni, $tlf, $tlf_emg, $direccion, $poblacion, $provincia, $cp, 
                $pais, $club, $talla, $es_federado, $num_licencia);

            $error = [];
            $cambios = false;

            // Comprobación de cambios
            if($nombre != $user_db->nombre || $email != $user_db->email || $avatar != $user_db->avatar || 
               $dni != $user_db->dni || $club != $user_db->club || $es_federado != $user_db->es_federado) {
                $cambios = true;
            }

            // Validación Nombre
            if(empty($nombre)) $error['nombre'] = "El nombre es obligatorio";

            // Validación Email
            if($email != $user_db->email){
                $cambios = true;
                if(empty($email)){
                    $error['email'] = "El email es obligatorio";
                } else if (!$this->model->validate_unique_email($email, $id)){
                    $error['email'] = "El email ya existe";
                }
            }

            // Validación Rol
            if($role_id != $this->model->get_user_role_id($id)){
                $cambios = true;
            }

            if (!empty($error)) {
                $_SESSION['errors'] = $error;
                $_SESSION['user'] = $user_act;
                header('Location: ' . URL . 'user/edit/' . $id);
                exit();
            }

            if (!$cambios && !isset($_FILES['avatar'])) {
                $_SESSION['notify'] = "No se han detectado cambios en el Usuario";
                header('Location: ' . URL . 'user');
                exit();
            }

            // Actualizar datos básicos
            $this->model->update($user_act, $id, $role_id);
            
            $_SESSION['notify'] = "Usuario actualizado correctamente";
            header('Location: ' . URL . 'user');
            exit();
        }

        /*
            Método: delete
        */
        public function delete($params) {
            // sec_session_start();
            $this->requireLogin();
            $this->requirePrivilege($GLOBALS['user']['delete']);

            $csrf_token = $_POST['csrf_token'] ?? '';
            if(!hash_equals($_SESSION['csrf_token'], $csrf_token)){
                $this->handleError();
            }

            $id = (int) $params[0];

            if (!$this->model->validate_id_user_exists($id)){
                $_SESSION['error'] = "El usuario no existe";
                header('Location: ' . URL . 'user');
                exit();
            }
            
            $this->model->delete($id);
            $_SESSION['notify'] = "Usuario eliminado con éxito";
            header('Location: ' . URL . 'user');
            exit();
        }

        /*
            Método: show
            Descripción: Muestra los detalles de un usuario (Solo lectura)
            Parámetros: id del usuario
        */
        public function show($params) {

            // Capa de validación de sesión
            $this->requireLogin();

            // Capa gestión rol de usuario
            $this->requirePrivilege($GLOBALS['user']['show']);

            // Obtener el ID del usuario
            $id = (int) $params[0];

            // Validar que el usuario existe antes de intentar leerlo
            if (!$this->model->validate_id_user_exists($id)){
                $_SESSION['error'] = "El usuario solicitado no existe";
                header('Location: ' . URL . 'user');
                exit();
            }

            // Obtener los datos del usuario a través del modelo
            // Importante: El modelo debe devolver un objeto que incluya la propiedad 'rol' (nombre del rol)
            $this->view->user = $this->model->read($id);

            // Obtener también el nombre del rol para la vista si read() no lo trae por defecto
            $this->view->user->rol = $this->model->get_user_role_name($id);

            // Título de la página
            $this->view->title = "Consultar Usuario - Traileros";

            // Renderizar la vista correspondiente
            $this->view->render('user/show/index');
        }

        /*
            Método: search()
            Descripción: Busca usuarios a partir de una expresión
            url asociada: user/search
        */
        public function search() {

            // Iniciar o continuar sesión
            // sec_session_start();

            // Capa de validación de sesión
            $this->requireLogin();

            // Capa gestión rol de usuario
            $this->requirePrivilege($GLOBALS['user']['search']);

            // Obtengo el término de búsqueda del formulario (por GET)
            $term = filter_var($_GET['term'] ??= '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Si el término está vacío, podríamos redirigir al render principal o dejar que el modelo traiga todo
            
            // Creo la notificación para la vista
            $this->view->notify = "Resultados de la búsqueda para: \"$term\"";
            $this->view->title = "Búsqueda de Usuarios";

            // Llamar al modelo para buscar los usuarios
            $this->view->users = $this->model->search($term);

            // Llama a la vista principal para mostrar los resultados
            $this->view->render('user/main/index');
        }

        /*
            Método: order()
            Descripción: Ordena la lista de usuarios por un criterio
            url asociada: user/order/criterio
        */
        public function order($params) {

            // Iniciar o continuar sesión
            // sec_session_start();

            // Capa de validación de sesión
            $this->requireLogin();

            // Capa gestión rol de usuario
            $this->requirePrivilege($GLOBALS['user']['order']);

            // Obtener el criterio de ordenación
            $criterio = (int) $params[0];

            // Mapa de nombres legibles para la vista
            $columnas = [
                1 => 'Id',
                2 => 'Nombre',
                3 => 'Email',
                4 => 'Rol'
            ];

            // Nombre del criterio para mostrar en la vista
            $nombreCriterio = $columnas[$criterio] ?? 'Id';

            // Título y notificación
            $this->view->title  = "Usuarios ordenados por $nombreCriterio";
            $this->view->notify = $this->view->title;

            // Obtener los usuarios ordenados del modelo
            $this->view->users = $this->model->order($criterio);

            // Renderizar la vista principal de usuarios
            $this->view->render('user/main/index');
        }

        /*
            Método: requirePrivilege($allowedRoles)
            Descripción: Verifica que el usuario tiene privilegios para acceder a la funcionalidad
        */
        private function requirePrivilege($allowedRoles){
            if (!in_array($_SESSION['role_id'], $allowedRoles)){
                $_SESSION['notify'] = "Debes iniciar sesión para acceder al sistema";
                header('Location: ' . URL . 'auth/login');
                exit();
            }
        }

        /*
            Método: requireLogin()
            Descripción: Verifica que el usuario ha iniciado sesión
        */
        private function requireLogin(){
            if(!isset($_SESSION['user_id'])) {
                $_SESSION['notify'] = "Debes iniciar sesión para acceder al sistema";
                header('Location: ' . URL . 'auth/login');
                exit();
            }
        }

        private function handleError() {
            header('location:' . URL . 'error');
            exit();
        }
    }

    
?>