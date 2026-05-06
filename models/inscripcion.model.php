<?php

class InscripcionModel extends Model {

    /*
        Descripción: filtra las inscripciones en funcion del rol y el id del usuario:
            1. Muestra todas las inscripciones a usuario ADMIN.
            2. Muestra inscripciones de los eventos creados a usuarios ORGANIZADORES.
            3. Muestra inscripciones propias a usuarios CORREDORES.
    */
    public function getInscripcionesByRole($user_id, $role_id) {
        try {
            $db = $this->db->connect();
            
            // Base de la consulta
            $sql = "SELECT  i.*,
                            e.nombre as evento_nombre, 
                            e.fecha as evento_fecha, 
                            c.nombre as categoria_nombre, 
                            u.name as usuario_nombre
                    FROM Inscripciones i
                    LEFT JOIN Eventos e ON i.evento_id = e.id
                    LEFT JOIN Categorias c ON i.categoria_id = c.id
                    LEFT JOIN users u ON i.user_id = u.id";

            // --- FILTRADO POR ROL ---
            if ($role_id == 1) { 
                // ADMIN: ve todo.
                $query = $db->prepare($sql . " ORDER BY e.fecha DESC, i.user_id ASC");
                $query->execute();
            } 
            elseif ($role_id == 2) { 
                // ORGANIZADOR: Solo ve inscripciones de SUS eventos
                $sql .= " WHERE e.organizador_id = :user_id ORDER BY e.fecha DESC, i.user_id ASC";
                $query = $db->prepare($sql);
                $query->execute(['user_id' => $user_id]);
            } 
            else { 
                // USUARIO: Solo ve SUS propias inscripciones
                $sql .= " WHERE i.user_id = :user_id ORDER BY e.fecha DESC, i.user_id ASC";
                $query = $db->prepare($sql);
                $query->execute(['user_id' => $user_id]);
            }

            $res = $query->fetchAll(PDO::FETCH_OBJ);
        
            return $res;

        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    /*
        Filtra las inscripciones por usuario y solo muestra las de cada uno.
        Permite mantener la privacidad de cada usuario
        Se usa en la vista principal de inscripciones ("Mis Carreras").
    */
    public function getInscripcionesByUser($user_id) {
        try {
            $db = $this->db->connect();
            // Solo traemos las inscripciones donde el ID de usuario coincida
            $sql = "SELECT i.*, e.nombre as evento_nombre, e.fecha as evento_fecha 
                    FROM inscripciones i
                    INNER JOIN eventos e ON i.evento_id = e.id
                    WHERE i.user_id = :user_id
                    ORDER BY e.fecha DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute(['user_id' => $user_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Metodo get para el envío de correos al hacer una inscripción exitosa
    public function getByPagoId($id_pago) {
        $sql = "SELECT i.*,
                    e.nombre as evento_nombre, 
                    e.fecha as evento_fecha, 
                    u.name as usuario_nombre, 
                    u.email as email
                FROM Inscripciones i
                INNER JOIN Eventos e ON i.evento_id = e.id
                INNER JOIN users u ON i.user_id = u.id
                WHERE i.id_pago = :id_pago"; 

        $db = $this->db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute(['id_pago' => $id_pago]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método para obtener los detalles de una inscripción para email
    public function getDetalleParaEmail($id) {
        // Traemos solo lo necesario para el correo
        $sql = "SELECT 
                    i.id,
                    u.name AS usuario_nombre, 
                    u.email AS usuario_email, 
                    e.nombre AS evento_nombre
                FROM inscripciones i
                INNER JOIN users u ON i.user_id = u.id
                INNER JOIN eventos e ON i.evento_id = e.id
                WHERE i.id = :id";
                
        $db = $this->db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        // Usamos fetch() porque solo esperamos UNA fila
        return $stmt->fetch(); 
    }

    // Metodo para comprobar el estado del pago antes de intentar inscribirse 
    // Por si ya existe una inscripción cancelada o pendiente
    public function getInscripcionSimple($user_id, $evento_id) {
        $sql = "SELECT id, estado_pago 
                FROM Inscripciones 
                WHERE user_id = :u AND evento_id = :e 
                LIMIT 1";
        $db = $this->db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute(['u' => $user_id, 'e' => $evento_id]);
        return $stmt->fetch(); // Devuelve el array con id y estado, o false si no existe
    }

    /*
        Método: create()
        Descripción: Inserta una nueva inscripcion en la base de datos
        IMPORTANTE: Este método inicia el procedimiento de pago por lo que estado_pago no se modifica
        y seguirá siendo 'pendiente'.
    */
    public function create(class_inscripcion $inscripcion) {
        try {
            $db = $this->db->connect();
            
            
            $sql = "INSERT INTO inscripciones (
                    user_id, 
                    evento_id, 
                    modalidad_id, 
                    categoria_id, 
                    id_pago, 
                    metodo_pago, 
                    estado_pago, 
                    precio_final
                ) VALUES (
                    :user_id, 
                    :evento_id, 
                    :modalidad_id, 
                    :categoria_id, 
                    :id_pago, 
                    :metodo_pago, 
                    :estado_pago, 
                    :precio_final
                )";
            
            $stmt = $db->prepare($sql);
            
            $datos = [
                ':user_id'      => $inscripcion->user_id,
                ':evento_id'    => $inscripcion->evento_id,
                ':modalidad_id' => $inscripcion->modalidad_id,
                ':categoria_id' => $inscripcion->categoria_id,
                ':id_pago'      => $inscripcion->id_pago,
                ':metodo_pago'  => $inscripcion->metodo_pago,
                ':estado_pago'  => $inscripcion->estado_pago,
                ':precio_final' => $inscripcion->precio_final
            ];

            return $stmt->execute($datos); 

        } catch (Throwable $e) {
            $this->handleError($e);
        }
    }

    /*
        Método: confirmarPago()
        Descripción: Confirma el pago y asigna el dorsal definitivo a partir del id_pago. 
        IMPORTANTE: Cambia el estado_pago a completado y asigna el dorsal
    */
    public function confirmarPago($id_pago) {
        try {
            $db = $this->db->connect();
            
            // 1. Buscamos la inscripción que tiene ese ID de pago
            $sql = "SELECT id, evento_id, estado_pago FROM inscripciones WHERE id_pago = :id_pago LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute(['id_pago' => $id_pago]);
            $inscripcion = $stmt->fetch(PDO::FETCH_OBJ);

            // 2. Si existe y no está ya pagada (evitar duplicar dorsales si refresca la página)
            if ($inscripcion && $inscripcion->estado_pago !== 'completado') {
                
                // Asignamos el dorsal ahora que el pago es seguro
                $nuevoDorsal = $this->generarSiguienteDorsal($inscripcion->evento_id);

                $sqlConfirm = "UPDATE inscripciones SET 
                            estado_pago = 'completado', 
                            dorsal = :dorsal 
                            WHERE id = :id";
                
                $stmtConfirm = $db->prepare($sqlConfirm);
                return $stmtConfirm->execute([
                    'dorsal' => $nuevoDorsal,
                    'id'     => $inscripcion->id
                ]);
            }
            
            return ($inscripcion && $inscripcion->estado_pago === 'completado');

        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    public function read($user_id, $evento_id) {
        $sql = "SELECT * FROM Inscripciones WHERE user_id = :u AND evento_id = :e";
        $db = $this->db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute(['u' => $user_id, 'e' => $evento_id]);
        return $stmt->fetch();
    }

    public function update($data) {

        $sql = "UPDATE inscripciones SET 
            categoria_id = :categoria_id,
            dorsal       = :dorsal,
            metodo_pago  = :metodo_pago,
            estado_pago  = :estado_pago,
            precio_final = :precio_final,
            id_pago      = :id_pago
            WHERE id = :id";
        
        $db = $this->db->connect();
        $stmt = $db->prepare($sql);
        return $stmt->execute($data);
    }

    public function cancel($id) {
       try {
            // Al cancelar, ponemos el dorsal a NULL para que quede libre
            // y el estado a 'cancelado' para que no cuente como plaza ocupada.
            $sql = "UPDATE inscripciones 
                    SET estado_pago = 'cancelado', 
                        dorsal = NULL 
                    WHERE id = :id
                    LIMIT 1";
            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    // Método opcional para eliminar totalmente la inscripción
    public function hardDelete($user_id) {
        try {
            $sql = "DELETE FROM Inscripciones WHERE id = :id LIMIT 1";
            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            return $stmt->execute(['id' => $user_id]);
        } catch (PDOException $e) {
            $this->handleError($e);
            return false;
        }
    }   

    // --- Lógica de Negocio ---
    public function getCategoriaAdecuada($edad, $sexo) {
        // Buscamos la categoría donde encaje la edad y el sexo (o mixto)
        $sql = "SELECT id FROM Categorias 
                WHERE :edad BETWEEN edad_min AND edad_max 
                AND (sexo = :sexo OR sexo = 'Mixto') 
                LIMIT 1";
        $db = $this->db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute(['edad' => $edad, 'sexo' => $sexo]);
        return $stmt->fetchColumn();
    }

    public function isUserInscribed($user_id, $evento_id) {
        $sql = "SELECT COUNT(*) FROM Inscripciones WHERE user_id = :u AND evento_id = :e";
        $db = $this->db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute(['u' => $user_id, 'e' => $evento_id]);
        return $stmt->fetchColumn() > 0;
    }

    public function getAllCategorias() {
        $db = $this->db->connect();
        return $db->query("SELECT * FROM Categorias ORDER BY edad_min ASC")->fetchAll();
    }

    public function getDetalleCompleto($user_id, $evento_id) {
        // Similar al get() pero para un solo registro y con más info
        $sql = "SELECT  i.*,
                        e.nombre as evento_nombre, 
                        e.organizador_id,
                        e.fecha as evento_fecha, 
                        e.ubicacion as evento_lugar, 
                        u.name as usuario_nombre, 
                        u.apellidos as usuario_apellidos,
                        u.email as usuario_email, 
                        u.dni as usuario_dni, 
                        u.telefono as usuario_telefono,
                        c.nombre as categoria_nombre,
                        m.nombre as modalidad_nombre
                FROM Inscripciones i
                INNER JOIN Eventos e ON i.evento_id = e.id
                INNER JOIN users u ON i.user_id = u.id
                LEFT JOIN Categorias c ON i.categoria_id = c.id
                INNER JOIN modalidades m ON i.modalidad_id = m.id
                WHERE i.user_id = :u AND i.evento_id = :e";
        $db = $this->db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute(['u' => $user_id, 'e' => $evento_id]);
        return $stmt->fetch();
    }

    public function getInscritosPorEvento($id_evento){
        try {
            $db = $this->db->connect();

            $sql = "SELECT  i.id AS inscripcion_id, 
                            CONCAT_WS(' ', u.apellidos, u.name) AS nombre_completo, 
                            u.email, 
                            i.fecha_inscripcion 
                    FROM inscripciones i
                    INNER JOIN users u ON i.user_id = u.id
                    WHERE i.evento_id = :id
                    ORDER BY nombre_completo ASC";

            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $id_evento]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    private function generarSiguienteDorsal($evento_id) {
        // Buscamos el dorsal máximo actual para este evento
        $sql = "SELECT MAX(dorsal) FROM Inscripciones WHERE evento_id = :evento_id";
        $db = $this->db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute(['evento_id' => $evento_id]);
        $max_dorsal = $stmt->fetchColumn();

        // Si no hay nadie inscrito, empezamos por el 1, si hay, sumamos 1
        return ($max_dorsal) ? $max_dorsal + 1 : 1;
    }

    /* Método: search
    Descripción: Permite buscar por nombre de usuario, apellidos o nombre del evento.
    */
    public function search($term) {
        try{
            $sql = "SELECT i.*, 
                    e.nombre as evento_nombre,
                    e.fecha as evento_fecha,
                    u.name as usuario_nombre, 
                    u.apellidos as usuario_apellidos,
                    c.nombre as categoria_nombre
                FROM Inscripciones i
                INNER JOIN Eventos e ON i.evento_id = e.id
                INNER JOIN users u ON i.user_id = u.id
                LEFT JOIN Categorias c ON i.categoria_id = c.id
                WHERE CONCAT_WS('', u.name, u.apellidos, e.nombre, e.fecha, i.dorsal) LIKE :term
                ORDER BY i.fecha_inscripcion DESC";

            $db = $this->db->connect();
            $stmt = $db->prepare($sql);

            $term = "%$term%";

            $stmt->bindParam(':term', $term, PDO::PARAM_STR);
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            $stmt->execute();
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    /* 
    Método: order
    Descripción: Ordena el listado según la columna solicitada. En función del rol del usuario muestra todas las inscripciones (admin)
                 o solo las suyas (organizador o corredor).
    */
    public function order($criterio, $user_id, $role_id) {
        
        $columnas_permitidas = [
            'id' => 'i.id ASC',
            'dorsal' => 'i.dorsal',
            'usuario' => 'u.apellidos ASC, u.name ASC',
            'evento' => 'e.nombre ASC',
            'fecha' => 'i.fecha_inscripcion DESC',
            'estado' => 'i.estado_pago ASC'
        ];

        $orden = $columnas_permitidas[$criterio] ?? 'i.fecha_inscripcion DESC';

        // Consulta base
        $sql = "SELECT i.*, 
                   e.nombre as evento_nombre, 
                   e.fecha as evento_fecha,
                   u.name as usuario_nombre, 
                   u.apellidos as usuario_apellidos,
                   c.nombre as categoria_nombre
            FROM Inscripciones i
            INNER JOIN Eventos e ON i.evento_id = e.id
            INNER JOIN users u ON i.user_id = u.id
            LEFT JOIN Categorias c ON i.categoria_id = c.id";

        // Filtro por rol
        $params = [];
        if ($role_id == 1) { 
            // ADMIN: lo ve todo.
        } 
        elseif ($role_id == 2) { 
            // ORGANIZADOR: Solo ve inscripciones de sus eventos
            $sql .= " WHERE e.organizador_id = :user_id";
            $params['user_id'] = $user_id;
        } 
        else { 
            // USUARIO: Solo ve sus propias inscripciones
            $sql .= " WHERE i.user_id = :user_id";
            $params['user_id'] = $user_id;
        }

        // Añade el orden al final de la consulta
        $sql .= " ORDER BY $orden";
        
        try {
            $db = $this->db->connect();
            $query = $db->prepare($sql);
            $query->execute($params);
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    /*
        Descripción: Método para obtener todos los inscritos a una carrera para su exportación.
    */
    public function getInscritosExport($evento_id){
        try{
            $sql = "SELECT  i.dorsal,
                            CONCAT_WS(' ', u.apellidos, u.name) AS nombre,
                            u.email,
                            m.nombre AS modalidad,
                            c.nombre AS categoria,
                            i.metodo_pago,
                            i.fecha_inscripcion
                    FROM inscripciones i 
                    INNER JOIN users u ON i.user_id = u.id
                    INNER JOIN modalidades m ON i.modalidad_id = m.id
                    LEFT JOIN categorias c ON i.categoria_id = c.id
                    WHERE i.evento_id = :evento_id
                    ORDER BY m.nombre ASC, i.dorsal ASC";
            
            $db = $this->db->connect();
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':evento_id', $evento_id, PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            return $stmt->fetchAll();

        }catch (PDOException $e){
            $this->handleError($e);
        }
    }

    // Método para obtener el nombre del evento (para el nombre del archivo exportable)
    public function getNombreEvento($id){
        $sql = "SELECT nombre FROM eventos WHERE id = :id LIMIT 1";
        $db = $this->db->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);      
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $res = $stmt->fetch();
        return ($res) ? $res->nombre : 'evento';

    }

    /*
     Método para limpiar inscripciones cuyo pago no se ha completado y han quedado en pendiente.
     Permite liberar dorsales en un plazo de 60 minutos tran un intento de inscripción no completado
    */
    public function limpiarInscripcionesPendientes($minutos = 60) {
        try {
            $db = $this->db->connect();
            
            // Buscamos inscripciones con estado 'PENDIENTE' 
            // cuya fecha de creación sea anterior al intervalo definido.
            $sql = "DELETE FROM Inscripciones 
                    WHERE estado_pago = 'PENDIENTE' 
                    AND fecha_inscripcion < DATE_SUB(NOW(), INTERVAL :minutos MINUTE)";
            
            $query = $db->prepare($sql);
            $query->execute(['minutos' => $minutos]);
            
            return $query->rowCount(); // Devuelve cuántas se han borrado
        } catch (PDOException $e) {
            $this->handleError($e);
            return false;
        }
    }

    /*
        Gestión de errores
    */
    private function handleError(PDOException $e) {
        $errorControllerFile = CONTROLLER_PATH . ERROR_CONTROLLER . '.php';
        if (file_exists($errorControllerFile)) {
            require_once $errorControllerFile;
            $mensaje = $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine();
            $controller = new Errores('DE BASE DE DATOS', 'Mensaje de Error: ', $mensaje);
            exit();
        } else {
            echo "Error crítico: " . $e->getMessage();
            exit();
        }
    }
}