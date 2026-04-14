<?php

class InscripcionModel extends Model {

    /**
     * Obtener inscripciones
     * Si es admin (role_id específico) ve todas, si no, solo las suyas.
     */
    public function get($user_id, $role_id) {
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

        $db = $this->db->connect();

        // Si el role_id no es el de administrador (ej: 1), filtramos por user_id
        if ($role_id != 1) { 
            $sql .= " WHERE i.user_id = :user_id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['user_id' => $user_id]);
        } else {
            $stmt = $db->query($sql);
        }

        return $stmt->fetchAll();
    }

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

    public function create(class_inscripcion $inscripcion) {
        try {
        $db = $this->db->connect();
        
        // 1. Generamos el dorsal
        $inscripcion->dorsal = $this->generarSiguienteDorsal($inscripcion->evento_id);
        
        // 2. Definimos la SQL
        $sql = "INSERT INTO Inscripciones (user_id, evento_id, categoria_id, dorsal, metodo_pago, estado_pago, precio_final) 
                VALUES (:user_id, :evento_id, :categoria_id, :dorsal, :metodo_pago, :estado_pago, :precio_final)";
        
        $stmt = $db->prepare($sql);
        
        // 3. Preparamos los datos
        $datos = [
            'user_id'      => $inscripcion->user_id,
            'evento_id'    => $inscripcion->evento_id,
            'categoria_id' => $inscripcion->categoria_id,
            'dorsal'       => $inscripcion->dorsal,
            'metodo_pago'  => $inscripcion->metodo_pago,
            'estado_pago'  => $inscripcion->estado_pago,
            'precio_final' => $inscripcion->precio_final
        ];

        // 4. Ejecutamos
        $resultado = $stmt->execute($datos);

        return true; 

    } catch (Throwable $e) {
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

        $sql = "UPDATE Inscripciones SET 
                categoria_id = :categoria_id,
                dorsal = :dorsal,
                metodo_pago = :metodo_pago,
                estado_pago = :estado_pago,
                precio_final = :precio_final
                WHERE user_id = :user_id AND evento_id = :evento_id";
        
        $db = $this->db->connect();
        $stmt = $db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($user_id, $evento_id) {
       try {
            // Al cancelar, ponemos el dorsal a NULL para que quede libre
            // y el estado a 'cancelado' para que no cuente como plaza ocupada.
            $sql = "UPDATE Inscripciones 
                    SET estado_pago = 'cancelado', 
                        dorsal = NULL 
                    WHERE user_id = :u AND evento_id = :e";
            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            return $stmt->execute(['u' => $user_id, 'e' => $evento_id]);
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    // Método opcional para eliminar totalmente la inscripción
    public function hardDelete($user_id, $evento_id) {
        $sql = "DELETE FROM Inscripciones WHERE user_id = :u AND evento_id = :e";
        $db = $this->db->connect();
        return $db->prepare($sql)->execute(['u' => $user_id, 'e' => $evento_id]);
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
                        c.nombre as categoria_nombre
                FROM Inscripciones i
                JOIN Eventos e ON i.evento_id = e.id
                JOIN users u ON i.user_id = u.id
                LEFT JOIN Categorias c ON i.categoria_id = c.id
                WHERE i.user_id = :u AND i.evento_id = :e";
        $db = $this->db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute(['u' => $user_id, 'e' => $evento_id]);
        return $stmt->fetch();
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

    /* Método: order
    Descripción: Ordena el listado según la columna solicitada.
    */
    public function order($criterio) {
        
        $columnas_permitidas = [
            'dorsal' => 'i.dorsal',
            'usuario' => 'u.apellidos ASC, u.name ASC',
            'evento' => 'e.nombre ASC',
            'fecha' => 'i.fecha_inscripcion DESC',
            'estado' => 'i.estado_pago ASC'
        ];

        $orden = $columnas_permitidas[$criterio] ?? 'i.fecha_inscripcion DESC';

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
                ORDER BY $orden";
        
        $db = $this->db->connect();
        return $db->query($sql)->fetchAll(PDO::FETCH_OBJ);
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