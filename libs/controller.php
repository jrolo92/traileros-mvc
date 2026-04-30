<?php

    class Controller {

        function __construct() {
            
            $this->view = new View();

        }
        
        // Método común a todos los controladores para cargar otro modelo distinto al suyo (cuando sea necesario)
        public function loadModel($model) {

            $url = 'models/' . $model . '.model.php';
            if (file_exists($url)) {

                require_once $url;

                $modelName = ucfirst($model).'Model';
                
                $objModel = new $modelName();
        
                //  Esto mantiene la compatibilidad con los controladores 
                $this->model = $objModel; 
                
                // Esto permite que el controlador de Inscripciones reciba el modelo extra
                return $objModel;
            }
        }

        // Función para mostrar mensajes en los métodos de los controladores.
        protected function checkMessages() {
        
            if (isset($_SESSION['notify'])) {
                $this->view->notify = $_SESSION['notify'];
                unset($_SESSION['notify']);
            }

            if (isset($_SESSION['error'])) {
                $this->view->error = $_SESSION['error'];
                unset($_SESSION['error']);
            }
        }

        // Metodo para crear token CSRF para los formularios
        protected function generateTokenCsrf(){
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            return $_SESSION['csrf_token'];
        }

        /*
            Método checkTokenCsrf()
            Permite checkear si el token CSRF es válido
            @param
                - string $csrf_token: token CSRF
        */
        protected function checkTokenCsrf($token){

            // Validación CSRF
            if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
                $this->handleError('Error de validación CSRF: Token no válido');
            }
        }

        protected function handleError($mensaje, $codigo = 403) {
            // Lanzamos la excepción con el código que queramos
            // El App.php la atrapará automáticamente
            throw new Exception($mensaje, $codigo);
        }

    }


?>