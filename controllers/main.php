<?php

    class Main Extends Controller {

        function __construct() {

            parent ::__construct(); 
            // Iniciamos sesión una sola vez para todos los métodos de este controlador
            if (session_status() == PHP_SESSION_NONE) {
                sec_session_start();
            }
            
        }

        function render() {

            require_once 'models/carrera.model.php';
            $carreraModel = new CarreraModel();

            $this->view->carreras = $carreraModel->getProximas();

            $this->view->title = "Inicio - Traileros";

            $this->view->render('main/index');
        }
    }

?>