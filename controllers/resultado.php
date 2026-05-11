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

        $this->cargarDatosMenu();

        // Lógica de retorno
        $user_role = $_SESSION['role_id'] ?? 3;

        if ($user_role == 1 || $user_role == 2) {
            // Si es Admin u Organizador, lo mandamos a la gestión del evento
            $this->view->back_url = URL . "carrera/gestion/";
        } else {
            // Si es un corredor, lo mandamos a la ficha pública
            $this->view->back_url = URL . "carrera/show/" . $evento_id;
        }

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
        $this->model->calcularRitmoMedio($datos['evento_id']);

        unset($_SESSION['import_data']);
        header('Location: ' . URL . 'resultado/render/' . $datos['evento_id']);
        exit();
    }

    public function search($params) {
        $this->requireLogin();
        
        $evento_id = isset($params[0]) ? (int) $params[0] : 0;
        
        $term = isset($_GET['term']) ? trim($_GET['term']) : '';

        $carrera = $this->model->getEvento($evento_id);
        if(!$carrera) {
            $this->errorNotFound($evento_id);
            return;
        }

        // Si no hay búsqueda
        if (empty($term)) {
            // Trae la clasificación completa
            $inscritos = $this->model->getClasificacion($evento_id);
        } else {
            // Si hay texto, buscamos
            $inscritos = $this->model->search($evento_id, $term);
        }

        $this->cargarDatosMenu();
        
        $user_role = $_SESSION['role_id'] ?? 3;
        $this->view->back_url = ($user_role <= 2) ? URL . "carrera/gestion/" : URL . "carrera/show/" . $evento_id;

        // Pasamos los datos a la vista
        $this->view->carrera = $carrera;
        $this->view->resultados = $inscritos; 
        $this->view->term = $term;
        $this->view->title = "Resultados - " . $carrera['nombre'];
        
        $this->view->render('resultado/main/index');
    }

    public function order($params) {
        $this->requireLogin();
        
        $evento_id = (int) $params[0];
        $criterio = $params[1] ?? 'posicion_general';

        $carrera = $this->model->getEvento($evento_id);
        if(!$carrera) $this->errorNotFound($evento_id);

        // Obtenemos los datos con el nuevo orden
        $inscritos = $this->model->order($evento_id, $criterio);

        $this->cargarDatosMenu();

        // Reutilizamos la lógica de back_url y vista del render()
        $user_role = $_SESSION['role_id'] ?? 3;
        $this->view->back_url = ($user_role <= 2) ? URL . "carrera/gestion/" : URL . "carrera/show/" . $evento_id;

        $this->view->carrera = $carrera;
        $this->view->resultados = $inscritos;
        $this->view->title = "Resultados - " . $carrera['nombre'];
        
        // Renderizamos la misma vista que el método render()
        $this->view->render('resultado/main/index');
    }

    /**
     * Descripción: Exporta en PDF la clasificación general de un evento.
     * URL: resultados/exportPdf/ID_EVENTO
    */
    public function exportPdf($params) {
        // 1. Validaciones de seguridad
        $this->requireLogin();
        // $this->requirePrivilege($GLOBALS['resultados']['export'] ?? 1);

        if (!isset($params[0])) {
            header('Location: ' . URL . 'resultados');
            return;
        }

        $evento_id = (int) $params[0];

        // 2. Obtención de datos desde el modelo
        $evento = $this->model->getEvento($evento_id);
        $clasificacion = $this->model->getClasificacion($evento_id);

        if (!$evento) {
            header('Location: ' . URL . 'resultados');
            return;
        }

        // 3. Configuración de FPDF
        $pdf = new \Fpdf\Fpdf();
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 20);

        // --- CABECERA ---
        // Título principal
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(33, 47, 61); // Azul oscuro
        $pdf->Cell(0, 15, utf8_decode('CLASIFICACIÓN GENERAL'), 0, 1, 'C');
        
        // Nombre del evento y fecha
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 7, utf8_decode(strtoupper($evento['nombre'])), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $fecha_formateada = date('d/m/Y', strtotime($evento['fecha']));
        $pdf->Cell(0, 7, utf8_decode('Fecha de la prueba: ' . $fecha_formateada), 0, 1, 'C');
        $pdf->Ln(10);

        // --- TABLA DE RESULTADOS ---
        // Colores de la cabecera de tabla
        $pdf->SetFillColor(44, 62, 80); 
        $pdf->SetTextColor(255);        
        $pdf->SetDrawColor(128, 128, 128);
        $pdf->SetFont('Arial', 'B', 10);

        // Anchos de columna (Total: 190)
        // Pos(15) + Dorsal(15) + Nombre(75) + Modalidad(55) + Tiempo(30) = 190
        $pdf->Cell(15, 8, 'POS', 1, 0, 'C', true);
        $pdf->Cell(15, 8, 'DOR', 1, 0, 'C', true);
        $pdf->Cell(75, 8, 'CORREDOR', 1, 0, 'C', true);
        $pdf->Cell(55, 8, 'MODALIDAD', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'TIEMPO', 1, 1, 'C', true);

        // --- LISTADO DE CORREDORES ---
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial', '', 9);
        $fill = false; 
        $pdf->SetFillColor(242, 244, 244); 

        foreach ($clasificacion as $row) {
            $pdf->Cell(15, 7, $row['posicion_general'], 1, 0, 'C', $fill);
            $pdf->Cell(15, 7, $row['dorsal'], 1, 0, 'C', $fill);
            
            // Usamos utf8_decode
            $pdf->Cell(75, 7, utf8_decode($row['nombre']), 1, 0, 'L', $fill);
            $pdf->Cell(55, 7, utf8_decode($row['modalidad']), 1, 0, 'C', $fill);
            
            // Destacamos el tiempo en negrita
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(30, 7, $row['tiempo'], 1, 1, 'C', $fill);
            $pdf->SetFont('Arial', '', 9);

            $fill = !$fill; 
        }

        // Pie de página con fecha de generación
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, utf8_decode('Documento generado el ' . date('d/m/Y H:i')), 0, 0, 'R');

        // 4. Salida del PDF (se abre en el navegador)
        $nombre_archivo = 'Clasificacion_' . str_replace(' ', '_', $evento['nombre']) . '.pdf';
        $pdf->Output('I', $nombre_archivo);
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