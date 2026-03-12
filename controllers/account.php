<?php
class Account extends Controller
{

    function __construct()
    {

        parent::__construct();
    }

    /*
        Método principal

        Se  carga siempre que la url contenga sólo el primer parámetro

        url: /account
    */
    public function render()
    {
        // inicio o continuo la sesión
        sec_session_start();

        // Comprobar si hay un usuario logueado
        $this->requireLogin();

        // Crear un token CSRF para los formularios
        // Por si el usuario abre dos pestañas simultáneas del mismo formulario
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Compruebo si hay mensaje de éxito
        if (isset($_SESSION['notify'])) {

            // Creo la propiedad mensaje en la vista
            $this->view->notify = $_SESSION['notify'];

            // Elimino la variable de sesión mensaje
            unset($_SESSION['notify']);
        }

        // Compruebo si hay mensaje de error
        if (isset($_SESSION['error'])) {

            // Creo la propiedad mensaje en la vista
            $this->view->error = $_SESSION['error'];

            // Elimino la variable de sesión mensaje
            unset($_SESSION['error']);
        }


        # Obtenemos los detalles completos del usuario
        $this->view->account = $this->model->getUserId($_SESSION['user_id']);

        // Creo la propiedad title de la vista
        $this->view->title = "Mi cuenta " . $_SESSION['user_name'];

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
        // inicio o continuo la sesión
        sec_session_start();

        // Comprobar si hay un usuario logueado
        $this->requireLogin();

        // Crear un token CSRF para los formularios
        // Por si el usuario abre dos pestañas simultáneas del mismo formulario
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Compruebo si hay mensaje de éxito
        if (isset($_SESSION['notify'])) {

            // Creo la propiedad mensaje en la vista
            $this->view->mensaje = $_SESSION['notify'];

            // Elimino la variable de sesión mensaje
            unset($_SESSION['notify']);
        }

        // Compruebo si hay mensaje de error
        if (isset($_SESSION['error'])) {

            // Creo la propiedad mensaje en la vista
            $this->view->error = $_SESSION['error'];

            // Elimino la variable de sesión mensaje
            unset($_SESSION['error']);
        }

        // Obtenemos el id del usuario
        $id = $_SESSION['user_id'];

        // Obtenemos los detalles completos del usuario
        $this->view->account = $this->model->getUserId($id);

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
        $this->view->title = "Editar cuenta " . $_SESSION['user_name'];
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
        // inicio o continuo la sesión
        sec_session_start();

        // comprobar si hay usuario logueado
        $this->requireLogin();

        // Validación token CSRF
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $this->handleError();
        }

        // Saneamos los detalles del formulario
        $name = filter_var($_POST['name'] ??= null, FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($_POST['email'] ??= null, FILTER_SANITIZE_EMAIL);

        // Obtengo los detalles del usuario
        $account = $this->model->getUserId($_SESSION['user_id']);

        // validación de los datos del formulario
        $errors = [];

        // validación name
        // antes de validar compruebo se ha modificado
        if ($name != $account->name) {
            if (empty($name)) {
                $errors['name'] = 'El nombre es obligatorio';
            } else if (strlen($name) < 5) {
                $error['name'] = 'El nombre debe tener al menos 5 caracteres';
            } else if (strlen($name) > 20) {
                $errors['name'] = 'El nombre debe tener como máximo 20 caracteres';
            } else if (!$this->model->validateUniqueName($name)) {
                $errors['name'] = 'Nombre usuario existente';
            }
        }

        // validación email
        // antes de validar compruebo se ha modificado
        if ($email != $account->email) {
            if (empty($email)) {
                $errors['email'] = 'El email es obligatorio';
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'El email no es válido';
            } else if (!$this->model->validateUniqueEmail($email)) {
                $errors['email'] = 'Email existente';
            }
        }

        // Si hay errores
        if (!empty($errors)) {
            // Creo la variable de sesión error
            $_SESSION['errors'] = $errors;

            // Creo la variable de sesión perfil
            $_SESSION['account'] = (object) [
                'name' => $name,
                'email' => $email
            ];

            // Redirecciono al formulario de edición
            header('location:' . URL . 'account/edit');
            exit();
        }

        // Actualizo los datos del usuario
        $this->model->update($name, $email, $_SESSION['user_id']);

        // Actualizo el posible nuevo nombre del usuario
        $_SESSION['user_name'] = $name;

        // Genero mensaje de éxito
        $_SESSION['notify'] = 'Cuenta actualizada correctamente';

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
        // inicio o continuo la sesión
        sec_session_start();

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Comprobar si hay un usuario logueado
        $this->requireLogin();

        // Compruebo si hay mensaje de éxito
        if (isset($_SESSION['notify'])) {

            // Creo la propiedad mensaje en la vista
            $this->view->mensaje = $_SESSION['notify'];

            // Elimino la variable de sesión mensaje
            unset($_SESSION['notify']);
        }

        // Compruebo si hay mensaje de error
        if (isset($_SESSION['error'])) {

            // Creo la propiedad mensaje en la vista
            $this->view->error = $_SESSION['error'];

            // Elimino la variable de sesión mensaje
            unset($_SESSION['error']);
        }

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
        $this->view->title = "Cambiar password " . $_SESSION['user_name'];

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
        // inicio o continuo la sesión
        sec_session_start();

        // comprobar si hay usuario logueado
        $this->requireLogin();

        // Validación toekn CSRF
         // Verificar el token CSRF
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $this->handleError();
        }

        // Saneamos los detalles del formulario
        $password = filter_var($_POST['password'] ??= null, FILTER_SANITIZE_SPECIAL_CHARS);
        $new_password = filter_var($_POST['new_password'] ??= null, FILTER_SANITIZE_SPECIAL_CHARS);
        $confirm_password = filter_var($_POST['confirm_password'] ??= null, FILTER_SANITIZE_SPECIAL_CHARS);

        // Obtengo los detalles del usuario
        $account = $this->model->getUserId($_SESSION['user_id']);

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

        // Actualizo password del usuario
        $this->model->updatePass($new_password, $_SESSION['user_id']);

        // Genero mensaje de éxito
        $_SESSION['notify'] = 'Password actualizado correctamente';

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
        // inicio o continuo la sesión
        sec_session_start();

        // Comprobar si hay un usuario logueado
        $this->requireLogin();

        // Crear un token CSRF para los formularios
        // Por si el usuario abre dos pestañas simultáneas del mismo formulario
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

         # Obtenemos los detalles completos del usuario
        $this->view->account = $this->model->getUserId($_SESSION['user_id']);

        // Creo la propiedad title de la vista
        $this->view->title = "Mi cuenta " . $_SESSION['user_name'];

        $this->view->render('account/delete/index');

        
    }

    public function delete_confirmed()
    {
        // inicio o continuo la sesión
        sec_session_start();

        // Comprobar si hay un usuario logueado
        $this->requireLogin();

        // Validación token CSRF
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $this->handleError();
        }

        // Elimino el usuario
        $this->model->delete($_SESSION['user_id']);

        // Cierro la sesión
        session_destroy();

        // Elimino la cookie de sesión
        setcookie(session_name(), '', time() - 3600);

        // vuelvo a abrir sesión
        sec_session_start();

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
