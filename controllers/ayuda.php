<?php

    class Ayuda extends Controller {

        function __construct() {

            parent ::__construct();
            
        }

        function render() {

            $this->cargarDatosMenu();
            
            $this->view->render('ayuda/index');
        }
    }

?>