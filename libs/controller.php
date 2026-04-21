<?php

    class Controller {

        function __construct() {
            
            $this->view = new View();

        }
        
        // Método común a todos los controladores para cargar otro modelo distinto al suyo (cuando sea necesario)
        function loadModel($model) {

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
    }


?>