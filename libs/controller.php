<?php

    class Controller {

        function __construct() {
            
            $this->view = new View();

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