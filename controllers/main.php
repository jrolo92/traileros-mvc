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

            $this->cargarDatosMenu();

            $this->view->title = "Inicio - Traileros";

            $this->view->render('main/index');
        }

        public function testEmail() {
            if (Email::enviar('coleguito92@gmail.com', 'Prueba Traileros', '¡Funciona!')) {
                echo "Correo enviado con éxito.";
            } else {
                echo "Error al enviar.";
            }
        }
    }

?>