<?php

    class Auth Extends Controller {

        // Crea una instancia del controlador auth
        // Llama al constructor de la clase padre Controller
        // Crea una vista para el controlador auth
        // Carga el modelo si existe auth.model.php
        function __construct() {

            parent ::__construct(); 
            
        }

        /*
            Método:  login()
            Descripción: Muestra el formulario de login
            Vista asociada: views/auth/login/index.php
            URL asociada: auth/login
            Modelo asociado: models/auth.model.php
        */

        function login() {

            // Iniciar o continuar sesión
            sec_session_start();

            // Generar token CSRF:
            if(empty($_SESSION['csrf_token'])){
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            // Inicializo los campos del formulario
            $this->view->email = null;
            $this->view->password = null;

            // Comprobar si hay mensajes en la sesión y pasarlos a la vista
            if (isset($_SESSION['notify'])){
                $this->view->notify = $_SESSION['notify'];
                unset($_SESSION['notify']);
            }

            // Verificar si existe algún error
            if (isset($_SESSION['errors'])){
                // Mostrar los detalles del error
                $this->view->errors = $_SESSION ['errors'];
                unset($_SESSION['errors']);

                // Crear la propiedad error
                $this->view->error = "Error de autenticación, revise el formulario";

                // Retroalimentacion de los detalles del error en el formulario
                $this->view->email = $_SESSION['email'];
                $this->view->password = $_SESSION['password'];

                // Eliminamos las vv de sesion de ambos campos del form
                unset($_SESSION['email']);
                unset($_SESSION['password']);

            }
                        
            // Obtengo los datos del  modelo para mostrar en la vista
            
            // Creo la propiedad  title para la vista
            $this->view->title = "Autenticación de usuarios";

            // Llama a la vista para renderizar la página
            $this->view->render('auth/login/index');
        }

       /*
            Método: validate_login()
            Descripción: Recibe los datos de autenticacion para validarla: email, password
                - Validar usuario mediante email y password
                - En caso de error -> retro alimenta el formulario y muestra errores
                - En caso de exito -> Inicia sesion segura y redirecciona a la página de libro
            url asociada: auth/validate_login

            POST:
                - email
                - password
                - csrf_token
       */
       public function validate_login() {

        // Inicio o continúo sesión
        sec_session_start();

        // Verificar el token CSRF
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $this->handleError();
        }

        // Recogemos los datos del formulario saneados
        // Prevenir ataques XSS
        $email = filter_var($_POST['email'] ??= '', FILTER_SANITIZE_EMAIL);
        $password = filter_var($_POST['password'] ??= '', FILTER_SANITIZE_SPECIAL_CHARS);

        // Validamos los campos del formulario

        // Creo un array asociativo para almacenar los posibles errores del formulario
        // $errors['nombre'] =  'Nombre es obligatorio'
        $errors = [];
        // Y una variable booleana para saber si el email es valido
        $validate = true;

        // Validación email
        // Reglas de validación: obligatorio, formato email y existir en la tabla user
        if (empty($email)){
            $errors['email'] = "El campo email es obligatorio";
            $validate = false;
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "El formato email no es correcto";
            $validate = false;
        }

        // Solo vamos a validar el email y la contraseña si el email es correcto
        // Es decir si validate es true
        if ($validate){

            //  Obtener los detalles del usuario a partir del email
            // Obtener un objeto de la clase user con las propiedades email y password
            $user = $this->model->get_user_email($email);

            // Verifico usuario
            if (!$user){
                $errors['email'] = "Email no ha sido registrado";
            
            // Validación del password
            // Reglas de validación: obligatorio, longitud mínima de 7 caracteres 
            // y que coincida con el password del usuario
            } else if (empty($password)){
                $errors['password'] = "La contraseña no ha sido introducido";
            } else if (strlen($password) < 7) {
                $errors['password'] = "Longitud mínima 7 caracteres";
            } else if (!password_verify($password, $user->password)) {
                $errors['password'] = "La contraseña no es correcta";
            }
       }

        // Fin Validación

        // Si hay errores vuelvo al formulario de autenticación
        if (!empty($errors)) {

            // Almaceno los errores en la sesión
            $_SESSION['errors'] = $errors;

            // Almaceno el email
            $_SESSION['email'] = $email;

            // Almaceno el password
            $_SESSION['password'] = $password;

            // Redirijo al controlador auth
            header('Location: ' . URL . 'auth/login');
            exit();
        }

        // Almaceno los datos del usuario en la sesión
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_email'] = $user->email;

        // Obtengo los datos del rol del usuario
        $_SESSION['role_id'] = $this->model->get_id_role_user($user->id);
        $_SESSION['role_name'] = $this->model->get_name_role_user($_SESSION['role_id']);

         // Generar mensaje de inicio de sesión
        $_SESSION['notify'] = "Usuario ". $user->name. " ha iniciado sesión.";

        // Redirigir a la vista de login
        header('Location: ' . URL . 'carrera');
        exit();

    }   

        /*
            Método:  register()
            Descripción: Muestra el formulario de registro
            Vista asociada: views/auth/register/index.php
            URL asociada: auth/register
            Modelo asociado: models/auth.model.php
        */

        function register() {

            // Iniciar o continuar sesión
            sec_session_start();

            // Generar token CSRF:
            if(empty($_SESSION['csrf_token'])){
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            // Inicializo los campos del formulario
            $this->view->name= null;
            $this->view->email = null;
            $this->view->password = null;
            $this->view->password2 = null;

            // Comprobar si hay mensajes en la sesión y pasarlos a la vista
            if (isset($_SESSION['notify'])){
                $this->view->notify = $_SESSION['notify'];
                unset($_SESSION['notify']);
            }

            // Verificar si existen errores
            if (isset($_SESSION['errors'])){
                // Mostrar los detalles del error
                $this->view->errors = $_SESSION ['errors'];
                unset($_SESSION['errors']);

                // Crear la propiedad error
                $this->view->error = "Error de registro, revise el formulario";

                // Retroalimentacion de los detalles del error en el formulario
                $this->view->name = $_SESSION['name'];
                $this->view->email = $_SESSION['email'];
                $this->view->password = $_SESSION['password'];
                $this->view->password2 = $_SESSION['password2'];

                // Eliminamos las vv de sesion de ambos campos del form
                unset($_SESSION['name']);
                unset($_SESSION['email']);
                unset($_SESSION['password']);
                unset($_SESSION['password2']);

            }
                        
            // Obtengo los datos del  modelo para mostrar en la vista
            
            // Creo la propiedad  title para la vista
            $this->view->title = "Registro de usuarios";

            // Llama a la vista para renderizar la página
            $this->view->render('auth/register/index');
        }

    
        /*
            Método: validate_register()
            Descripción: Recibe los datos de registro para validarla: nombre, email, contraseña y contraseña2
                - Registrar usuario mediante nombre email y contraseña
                - En caso de error -> retro alimenta el formulario y muestra errores
                - En caso de exito -> 
            url asociada: auth/validate_register

            POST:
                - nombre
                - email
                - password
                - csrf_token
       */
       public function validate_register() {

        // Inicio o continúo sesión
        sec_session_start();

        // Verificar el token CSRF
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $this->handleError();
        }

        // Recogemos los datos del formulario saneados
        // Prevenir ataques XSS
        $name = filter_var($_POST['name'] ??= '', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($_POST['email'] ??= '', FILTER_SANITIZE_EMAIL);
        $password = filter_var($_POST['password'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $password2 = filter_var($_POST['password2'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        // Validamos los campos del formulario

        // Creo un array asociativo para almacenar los posibles errores del formulario
        // $errors['nombre'] =  'Nombre es obligatorio'
        $errors = [];
        // Y una variable booleana para saber si el email es valido
        $validate = true;

        // Validación nombre
        // Reglas de validación: obligatorio
        if (empty($name)){
            $errors['name'] = "El campo nombre es obligatorio";
            $validate = false;
        }

        // Validación email
        // Reglas de validación: obligatorio, formato email y existir en la tabla user
        if (empty($email)){
            $errors['email'] = "El campo email es obligatorio";
            $validate = false;
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "El formato email no es correcto";
            $validate = false;
        } else if ($this->model->validate_exists_email($email)){
            $errors['email'] = "El email ya ha sido registrado";
            $validate = false;
        }

        // Validación de contraseña
        if (empty($password)){
            $errors['password'] = "El campo contraseña es obligatorio";
            $validate = false;
        } else if (strlen($password) < 7) {
            $errors['password'] = "Longitud mínima 7 caracteres";
            $validate = false;
        } 

        if ($password !== $password2){
            $errors['password2'] = "Las contraseñas no coinciden";
            $validate = false;
        }

        // Fin Validación

        // Si hay errores vuelvo al formulario de autenticación
        if (!$validate) {

            // Almaceno los errores en la sesión
            $_SESSION['errors'] = $errors;

            // Almaceno el nombre
            $_SESSION['name'] = $name;

            // Almaceno el email
            $_SESSION['email'] = $email;

            // Almaceno el password 1
            $_SESSION['password'] = $password;

            // Almaceno el password 2
            $_SESSION['password2'] = $password;

            // Redirijo al controlador auth
            header('Location: ' . URL . 'auth/register');
            exit();
        }

        // Hasheamos la contraseña antes de crear el usuario
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Creamos usuario
        $this->model->create_user($name, $email, $hash);

         // Generar mensaje de inicio de sesión
        $_SESSION['notify'] = "Usuario registrado con éxito. Inicie sesión";

        // Redirigir al login
        header('Location: ' . URL . 'auth/login');
        exit();

    }   

    public function logout() {
        // 1. Iniciar o continuar sesión
        sec_session_start();

        // 2. Terminar la sesión
        sec_session_destroy();

        // 3. Mostrar mensaje y redirigir al login
        $_SESSION['notify'] = "Sesión cerrada correctamente";
        
        header('Location: ' . URL . 'auth/login');
        exit();
    }

    /*
        Método: handleError
        Descripción: Maneja los errores de la base de datos
    */

    private function handleError()
    {
        // Incluir y cargar el controlador de errores
        $errorControllerFile = CONTROLLER_PATH . ERROR_CONTROLLER . '.php';
        
        if (file_exists($errorControllerFile)) {
            require_once $errorControllerFile;
            $mensaje = "Error de validación de seguridad del formulario. Intenta acceder de nuevo desde la página principal";
            $controller = new Errores('403', 'Mensaje de Error: ', $mensaje);
            
        } else {
            // Fallback en caso de que el controlador de errores no exista
            echo "Error crítico: " . "No se pudo cargar el controlador de errores.";
            exit();
        }
    }
}

?>