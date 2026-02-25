<?php

class Carrera extends Controller {

    function __construct() {
        parent::__construct(); 
    }

    /*
        Método: render
        Descripción: Renderiza la lista principal de carreras
    */
    function render() {
        sec_session_start();
        $this->requireLogin();

        // Capa gestión rol de usuario
        $this->requirePrivilege($GLOBALS['carrera']['render']);

        if(empty($_SESSION['csrf_token'])){
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        if (isset($_SESSION['notify'])){
            $this->view->notify = $_SESSION['notify'];
            unset($_SESSION['notify']);
        }

        $this->view->title = "Próximos Eventos - Traileros";
        
        // El método get() del modelo ahora trae el nombre del organizador
        $this->view->carreras = $this->model->get();

        $this->view->render('carrera/main/index');
    }

    /*
        Método: new
        Descripción: Muestra el formulario para crear una nueva carrera
    */
    function new() {
        sec_session_start();
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['carrera']['new']);

        if(empty($_SESSION['csrf_token'])){
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Objeto vacío para el formulario
        $this->view->carrera = new class_carrera();

        if (isset($_SESSION['errors'])){
            $this->view->errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
            $this->view->carrera = $_SESSION['carrera'];
            unset($_SESSION['carrera']);
            $this->view->error = "Errores en el formulario";
        }

        $this->view->title = "Nueva Carrera -Traileros";

        $this->view->render('carrera/new/index');
    }

    /*
        Método: create
        Descripción: Procesa el envío del formulario de nueva carrera
    */
    public function create() {
        sec_session_start();
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['carrera']['create']);

        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $this->handleError();
        }

        // Saneamiento de datos
        $nombre = filter_var($_POST['nombre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $fecha = filter_var($_POST['fecha'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $ubicacion = filter_var($_POST['ubicacion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $distancia = filter_var($_POST['distancia'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $desnivel = filter_var($_POST['desnivel'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $dificultad = filter_var($_POST['dificultad'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $descripcion = filter_var($_POST['descripcion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $imagenUrl = filter_var($_POST['imagenUrl'] ?? 'assets/images/default.jpg', FILTER_SANITIZE_URL);
        $organizador_id = filter_var($_POST['organizador_id'] ?? $_SESSION['user_id'], FILTER_SANITIZE_NUMBER_INT);

        $carrera = new class_carrera(null, $nombre, $fecha, $ubicacion, $distancia, $desnivel, $dificultad, $descripcion, $imagenUrl, $organizador_id);

        // Validación
        $error = [];
        if(empty($nombre)) $error['nombre'] = "El nombre es obligatorio";
        if(empty($fecha)) $error['fecha'] = "La fecha es obligatoria";
        if($distancia <= 0) $error['distancia'] = "La distancia debe ser positiva";

        if(!empty($error)){
            $_SESSION['errors'] = $error;
            $_SESSION['carrera'] = $carrera;
            header('Location: ' . URL . 'carrera/new');
            exit();
        }

        if ($this->model->create($carrera)) {
            $_SESSION['notify'] = "Carrera publicada correctamente";
            header('Location: ' . URL . 'carrera');
            exit();
        }
    }

    /*
        Método: edit
        Descripción: Carga datos para editar una carrera existente
    */
    public function edit($params) {
        sec_session_start();
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['carrera']['edit']);

        $id = (int) $params[0];
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        $this->view->carrera = $this->model->read($id);
        $this->view->id = $id;

        if (isset($_SESSION['errors'])) {
            $this->view->errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
            $this->view->carrera = $_SESSION['carrera'];
            unset($_SESSION['carrera']);
            $this->view->error = "Errores en el formulario";
        }

        $this->view->title = "Editar Carrera";

        $this->view->render('carrera/edit/index');
    }

    /*
        Método: update
        Descripción: Actualiza los datos de la carrera
    */
    public function update($params) {
        sec_session_start();
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['carrera']['update']);

        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            header('location:' . URL . 'error');
            exit();
        }

        $id = (int) $params[0];
        $carrera_orig = $this->model->read($id);

        $nombre = filter_var($_POST['nombre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        // ... (resto de filtros igual que en create)
        
        $carrera_act = new class_carrera($id, $nombre, $_POST['fecha'], $_POST['ubicacion'], $_POST['distancia'], $_POST['desnivel'], $_POST['dificultad'], $_POST['descripcion'], $_POST['imagenUrl'], $_POST['organizador_id']);

        // Detección de cambios simplificada
        if ($this->model->update($carrera_act, $id)) {
            $_SESSION['notify'] = "Carrera actualizada con éxito";
        }

        header('Location: ' . URL . 'carrera');
        exit();
    }

    /*
        Método: show
        Descripción: Muestra detalles de la carrera (Solo lectura)
    */
    public function show($params) {
        sec_session_start();
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['carrera']['show']);

        $id = (int) $params[0];
        $this->view->carrera = $this->model->read($id);
        $this->view->title = "Detalles de la Carrera";
        $this->view->render('carrera/show/index');
    }

    /*
        Método: delete
        Descripción: Elimina un evento
    */
    public function delete($params) {
        sec_session_start();
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['carrera']['delete']);

        if(!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])){
            $this->handleError();
        }

        $id = (int) $params[0];
        $this->model->delete($id);
        $_SESSION['notify'] = "Evento eliminado definitivamente";
        header('Location: ' . URL . 'carrera');
        exit();
    }

    /*
        Método: order
        Descripción: Ordena la lista de carreras según el criterio seleccionado
        Parámetros: $param (id del criterio de ordenación)
    */
    public function order($param) {
        sec_session_start();
        $this->requireLogin();

        // Capa gestión rol de usuario (usamos el permiso de order)
        $this->requirePrivilege($GLOBALS['carrera']['order']);

        // Recogemos el criterio de ordenación del parámetro
        $criterio = $param[0];

        // Título de la página
        $this->view->title = "Ordenar Carreras - Traileros";

        // Llamamos al método order del modelo
        // El modelo ya tiene la lógica para mapear 1->id, 2->nombre, etc.
        $this->view->carreras = $this->model->order($criterio);

        // Renderizamos la vista principal (main/index) con los datos ordenados
        $this->view->render('carrera/main/index');
    }

    /*
        Método: search
        Descripción: Filtra las carreras según un término de búsqueda
    */
    public function search() {
        sec_session_start();
        $this->requireLogin();

        // Capa gestión rol de usuario
        $this->requirePrivilege($GLOBALS['carrera']['search']);

        // Validar que el término de búsqueda existe y no está vacío
        if (isset($_GET['term']) && (!empty($_GET['term']))) {
            $term = $_GET['term'];
        } else {
            // Si el término está vacío, redirigimos al listado general
            header('location:' . URL . 'carrera');
            exit();
        }

        // Título de la página indicando la búsqueda
        $this->view->title = "Resultados de búsqueda: \"$term\" - Traileros";

        // Llamamos al modelo pasándole el término
        $this->view->carreras = $this->model->search($term);

        // Renderizamos la misma vista principal
        $this->view->render('carrera/main/index');
    }

    /*
        MÉTODO: inscribir
        Descripción: Permite a un corredor inscribirse en la carrera
    */
    public function inscribir($params) {
        sec_session_start();
        $this->requireLogin();
        
        $evento_id = (int) $params[0];
        $user_id = $_SESSION['user_id'];
        $metodo_pago = "Efectivo/Transferencia"; // Valor por defecto o de formulario

        if ($this->model->inscribir($user_id, $evento_id, $metodo_pago)) {
            $_SESSION['notify'] = "Inscripción realizada con éxito. ¡Nos vemos en la meta!";
        } else {
            $_SESSION['notify'] = "Error: Puede que ya estés inscrito en este evento.";
        }
        
        header('Location: ' . URL . 'carrera');
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

    private function handleError() {
        header('location:' . URL . 'error');
        exit();
    }
}