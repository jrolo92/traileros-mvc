<?php

    class Errores extends Controller {

        function __construct($tipo = '', $titulo = '', $mensaje_error = '') {

            parent ::__construct();
            $this->view->tipo = $tipo;
            $this->view->titulo = $titulo;
            $this->view->mensaje = $mensaje_error;

            $this->render();
        }

        function render() {
            $this->cargarDatosMenu();
            $this->view->render('error/index');
            exit();
        }

    }

?>