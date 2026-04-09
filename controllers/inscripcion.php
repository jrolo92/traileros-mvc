<?php

class Inscripcion extends Controller {

    function __construct() {
        parent::__construct();
        // Iniciamos sesión una sola vez para todos los métodos de este controlador
        if (session_status() == PHP_SESSION_NONE) {
            sec_session_start();
        }
    }

    /*
        Método: render()
        Descripción: Renderiza la lista principal de inscripciones
    */
    function render(){
        $this->requireLogin();
        // $this->requirePrivilege($GLOBALS['inscripcion']['render']);

        if(empty($_SESSION['csrf_token'])){
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        if (isset($_SESSION['notify'])){
            $this->view->notify = $_SESSION['notify'];
            unset($_SESSION['notify']);
        }

        $this->view->title = "Mis Inscripciones - Traileros";
        
        $this->view->inscripciones = $this->model->get($_SESSION['user_id'], $_SESSION['role_id']);

        $this->view->render('inscripcion/main/index');
    }

    /*
        Método: new
        Descripción: Muestra el formulario de inscripción para un evento específico
    */
    function new($params=null){
        $this->requireLogin();
        // $this->requirePrivilege($GLOBALS['inscripcion']['new']);

        if(empty($_SESSION['csrf_token'])){
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $evento_id = (int) ($params[0] ?? 0);
        if ($evento_id === 0) header('location:' . URL . 'carrera');

        // Cargamos modelos adicionales para datos del evento y categorías
        /** @var CarreraModel $eventoModel */
        $eventoModel = $this->loadModel('carrera');
        // VERIFICACIÓN: Si $eventoModel sigue siendo null, el problema está en loadModel
        if (!$eventoModel) {
            die("Error: No se pudo cargar el modelo CarreraModel. Revisa el nombre del archivo.");
        }
        $evento = $eventoModel->read($evento_id);

        if (!$evento) $this->errorNotFound($evento_id);

        // Comprobación de plazas antes de mostrar el formulario
        $plazas_libres = $evento['cupo_maximo'] - $eventoModel->getPlazasOcupadas($evento_id);
        if ($plazas_libres <= 0) {
            $_SESSION['notify'] = "Lo sentimos, no quedan plazas libres.";
            header('location:' . URL . 'carrera/show/' . $evento_id);
            exit;
        }

        $this->view->title = "Inscripción: " . $evento['nombre'];
        $this->view->evento = $evento;
        
        $this->view->inscripcion = new class_inscripcion();
        $this->view->inscripcion->evento_id = $evento_id;

        $this->view->render('inscripcion/new/index');
    }

    /*
        Método: create
        Descripción: Procesa la inscripción y asigna categoría automáticamente
    */
    function create() {
        $this->requireLogin();
        // $this->requirePrivilege($GLOBALS['inscripcion']['create'])

        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $this->handleError();
        }

        $evento_id = (int) $_POST['evento_id'];
        $user_id = $_SESSION['user_id'];

        // Cargamos el modelo de Carrera para obtener el precio real
        /** @var CarreraModel $eventoModel */
        $eventoModel = $this->loadModel('Carrera');
        $evento = $eventoModel->read($evento_id);

        // CRÍTICO: Comprobación de plazas antes de simular el pago
        $libres = $evento['cupo_maximo'] - $eventoModel->getPlazasOcupadas($evento_id);
        if ($libres <= 0) {
            $_SESSION['notify'] = "Evento completado mientras procesabas los datos.";
            header('Location: ' . URL . 'carrera');
            exit();
        }

        // SIMULACIÓN DE PAGO (Si falla, nunca llegamos al Model)
        $pagoExitoso = true; 
        if (!$pagoExitoso) {
            $_SESSION['notify'] = "Error en la pasarela de pago.";
            header('Location: ' . URL . 'inscripcion/new/' . $evento_id);
            exit();
        }
        
        // Si llegamos aquí, el pago es OK. Obtenemos datos del usuario para lógica de categoría
        /** @var UserModel $userModel */
        $userModel = $this->loadModel('User');
        $usuario = $userModel->read($user_id);
        
        // Calcular edad
        $nacimiento = new DateTime($usuario['fecha_nacimiento']);
        $hoy = new DateTime();
        $edad = $hoy->diff($nacimiento)->y;

        // Determinar Categoría Automática
        $categoria_id = $this->model->getCategoriaAdecuada($edad, $usuario['sexo']);

        // Saneamiento de metodo de pago
        $metodo_pago = filter_var($_POST['metodo_pago'] ?? 'transferencia', FILTER_SANITIZE_SPECIAL_CHARS);

        // Crear objeto de inscripción
        $inscripcion = new class_inscripcion(
            $user_id, 
            $evento_id, 
            $categoria_id, 
            null, // El dorsal se genera en el modelo (auto) una vez se confirma el pago (método creaete())
            $metodo_pago, 
            'completado', 
            $evento['precio']
        );

        // Guardar en BD
        if ($this->model->create($inscripcion)) {
            $_SESSION['notify'] = "¡Pago confirmado! Tu dorsal es el " . $inscripcion->dorsal;
            header('Location: ' . URL . 'inscripcion/show/' . $_SESSION['user_id'] . '/' . $evento_id);
        } else {
            $this->handleError();
        }
    }

    /*
        Método: edit / update
        Descripción: Permite al admin cambiar estado de pago o dorsal
    */
    public function edit($params) {
        $this->requireLogin();
        $this->requirePrivilege($GLOBALS['inscripcion']['edit']);

        $user_id = (int) $params[0];
        $evento_id = (int) $params[1];
        
        $inscripcion = $this->model->read($user_id, $evento_id);
        if (!$inscripcion) $this->errorNotFound("$user_id-$evento_id");

        $this->view->categorias = $this->model->getAllCategorias();
        $this->view->metodos_pago = ['tarjeta', 'transferencia', 'bizum', 'efectivo'];
        $this->view->inscripcion = $inscripcion;
        $this->view->title = "Editar Inscripción";
        $this->view->render('inscripcion/edit/index');
    }

    public function update($params) {
        $this->requireLogin();
        
        $user_id = (int) $params[0];
        $evento_id = (int) $params[1];

        // Saneamiento de datos
        $estado_pago = $_POST['estado_pago'];
        $dorsal = (int) $_POST['dorsal'];

        $data = [
            'user_id'      => $user_id,
            'evento_id'    => $evento_id,
            'categoria_id' => (int) $_POST['categoria_id'],
            'dorsal'       => empty($_POST['dorsal']) ? null : (int) $_POST['dorsal'],
            'metodo_pago'  => filter_var($_POST['metodo_pago'], FILTER_SANITIZE_SPECIAL_CHARS),
            'estado_pago'  => filter_var($_POST['estado_pago'], FILTER_SANITIZE_SPECIAL_CHARS),
            'precio_final' => filter_var($_POST['precio_final'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)
        ];

        if ($this->model->update($data)) {
            $_SESSION['notify'] = "Inscripción actualizada correctamente.";
            header('Location: ' . URL . 'inscripcion');
            exit();
        } else {
            $this->handleError();
        }
    }

    /*
        Método: show
        Descripción: Ver resguardo de inscripción
    */
    public function show($params) {
        $this->requireLogin();
        $user_id = (int) $params[0];
        $evento_id = (int) $params[1];

        $this->view->inscripcion = $this->model->getDetalleCompleto($user_id, $evento_id);
        $this->view->render('inscripcion/show/index');
    }

    public function delete($params) {
        $this->requireLogin();
        // $this->requirePrivilege($GLOBALS['inscripcion']['delete']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $this->handleError();
        }

        $user_id = (int) $params[0];
        $evento_id = (int) $params[1];

        if ($this->model->delete($user_id, $evento_id)) {
            $_SESSION['notify'] = "La inscripción ha sido cancelada.";
        } else {
            $_SESSION['notify'] = "Error al intentar cancelar la inscripción.";

        }

        header('Location: ' . URL . 'inscripcion');
        exit();
    }

    /*
        Métodos: search y order
    */
    public function search() {
        $this->requireLogin();
        $term = $_GET['term'] ?? '';
        $this->view->inscripciones = $this->model->search($term);
        $this->view->title = "Resultados de búsqueda: " . $term;
        $this->view->render('inscripcion/main/index');
    }

    public function order($param) {
        $this->requireLogin();
        $criterio = $param[0] ?? 'fecha';
        $this->view->inscripciones = $this->model->order($criterio);
        $this->view->title = "Inscripciones ordenadas por: " . $criterio;
        $this->view->render('inscripcion/main/index');
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

    // Metodos para el manejo de errores
     private function handleError() {
        header('location:' . URL . 'error');
        exit();
    }

    // Método de error not found
    private function errorNotFound($id) {
        $this->view->tipo = "404";
        $this->view->titulo = "Recurso no encontrado";
        $this->view->mensaje = "Lo sentimos, el elemento con ID $id no existe.";
        $this->view->render('error/index');
        exit;
    }
}

?>