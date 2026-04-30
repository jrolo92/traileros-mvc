<?php

class Resultado extends Controller {

    function __construct() {
        parent::__construct();
        // Iniciamos sesión una sola vez para todos los métodos de este controlador
        if (session_status() == PHP_SESSION_NONE) {
            sec_session_start();
        }
    }

    // Muestra la interfaz de resultados de una carrera
    public function render ($params) {
        // Comprobamos inicio de sesión y privilegios
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['resultado']['render']);

        // Obtenemos datos de la carrera
        $evento_id = (int) $params[0];
        $carrera = $this->model->getEvento($evento_id);

        if(!$carrera) $this->errorNotFound($evento_id);

        // Obtenemos datos de los inscritos a la carrera
        $inscritos = $this->model->getClasificacion($evento_id);

        // Creamos vv para la vista
        $this->view->carrera = $carrera;
        $this->view->resultados = $inscritos;
        $this->view->title = "Resultados - " . $carrera['nombre'];
        $this->view->render('resultado/main/index');
    }

    // Permite la importación de resultados en formato CSV
    public function pre_import ($params){
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['resultado']['pre_import']);
        // Obtenemos el id del evento
        $evento_id = (int)$params[0];

        // Lógica para la subida si existe el archivo y se ha enviado por POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file'])) {
            $file = $_FILES['csv_file']['tmp_name'];

            if (($manejador = fopen($file, "r")) !== FALSE) {
                // Detecta delimitador del csv contando ocurrencias y comparandolas
                $linea1 = fgets($manejador);
                $numComas = substr_count($linea1, ",");
                $numPuntoComas = substr_count($linea1, ";");
                $delimitador = ($numPuntoComas > $numComas) ? ";" : ",";
                rewind($manejador);

                // Lee la cabecera del csv
                $cabecera = fgetcsv($manejador, 1000, $delimitador);
                $filas = [];

                // Lee las filas
                while (($datos = fgetcsv($manejador, 1000, $delimitador)) !== FALSE){
                    $filas[]= $datos;
                }
                fclose($manejador);

                // Guarda los datos en sesión para que no se pierdan
                $_SESSION['import_data'] = [
                    'evento_id' => $evento_id,
                    'cabecera' => $cabecera,
                    'filas' => $filas
                ];

                // Pasar datos a la vista del mapeo:
                $this->view->cabecera = $cabecera;
                $this->view->evento_id = $evento_id;
                $this->view->title = "Mapeo de Columnas del CSV";
                $this->view->render('resultado/pre_import/index');
            }
        }
    }

    public function process_import(){

        if (!isset($_SESSION['import_data'])) {
            $_SESSION['notify'] = "Error: Sesión de importación expirada.";
            header('Location: ' . URL . 'carrera');
            exit();
        }

        // Obtiene los datos de la sesión y del formulario de mapeo.
        $datos = $_SESSION['import_data'];
        $evento_id = $datos['evento_id'];
        $map_dorsal = $_POST['map_dorsal'];
        $map_tiempo =  $_POST['map_tiempo'];

        foreach ($datos['filas'] as $fila){
            $dorsal = $fila[$map_dorsal];
            $tiempo = $fila[$map_tiempo];

            // Guardamos los datos del csv
            $this->model->saveResultadosCsv($datos['evento_id'], $dorsal, $tiempo);

        }

        // Calculamos posición general y por categorias
        $this->model->calcularRankings($datos['evento_id']);
        $this->model->calcularRankingsCategorias($datos['evento_id']);

        unset($_SESSION['import_data']);
        header('Location: ' . URL . 'resultado/render/' . $datos['evento_id']);
        exit();
    }

    /* --- Métodos privados de seguridad --- */
    private function requirePrivilege($allowedRoles){
        if (!in_array($_SESSION['role_id'], $allowedRoles)){
            $_SESSION['notify'] = "No tienes permisos para realizar esta acción";
            header('Location: ' . URL . 'carrera');
            exit();
        }
    }
    
    private function requireLogin(){
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['notify'] = "Acceso restringido. Por favor, inicia sesión.";
            header('Location: ' . URL . 'auth/login');
            exit();
        }
    }

    // private function handleError() {
    //     header('location:' . URL . 'error');
    //     exit();
    // }

    // Método de error not found
    private function errorNotFound($id) {
        $this->view->tipo = "404";
        $this->view->titulo = "Recurso no encontrado";
        $this->view->mensaje = "Lo sentimos, el elemento con ID $id no existe.";
        $this->view->render('error/index');
        exit;
    }
}