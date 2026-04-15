<?php

    class Controller {

        function __construct() {
            
            $this->view = new View();

            // Centralizamos la captura de mensajes de sesión
            if (session_status() === PHP_SESSION_NONE) {
                sec_session_start();
            }

            if (isset($_SESSION['notify'])) {
                $this->view->notify = $_SESSION['notify'];
                unset($_SESSION['notify']);
            }

            if (isset($_SESSION['error'])) {
                $this->view->error = $_SESSION['error'];
                unset($_SESSION['error']);
            }

        }
        
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
    }


?>