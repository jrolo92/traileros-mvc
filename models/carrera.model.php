<?php

/*
    Modelo: carreraModel
    Descripción: Gestiona el acceso a los datos de las carreras en la BD traileros
*/

class carreraModel extends Model {

    /*
        Método: get()
        Descripción: Obtiene todos los eventos con el nombre del organizador
        Uso: en búsqueda de vista principal de carreras.
    */
    public function get() {
        try {
            $sql = "SELECT 
                        e.id, e.nombre, e.fecha, e.ubicacion, e.dificultad, e.imagen,
                        m.distancia, m.desnivel, m.cupo_maximo, m.precio,
                        u.name AS organizador
                    FROM Eventos AS e
                    INNER JOIN users AS u ON e.organizador_id = u.id
                    LEFT JOIN modalidades AS m ON e.id = m.evento_id
                    GROUP BY e.id
                    ORDER BY e.id ASC";

            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            
            return $stmt;

        } catch (PDOException $e) {
           $this->handleError($e); 
        }
    }

    /*
        Método: getProximas()
        Descripción: Devuelve las 4 próximas carreras ordenadas por fecha mas cercana 
        a partir de la fecha actual.
        Uso: en render del controlador Main (Vista inicio)
    */
    public function getProximas() {
        $items = [];
        try {
            // Forzamos la obtención de la conexión
            $conexion = $this->db->connect(); 
            
            // Usamos una consulta simple primero para asegurar resultados
            $sql = "SELECT e.*, m.distancia, m.desnivel 
                    FROM eventos e
                    LEFT JOIN modalidades m ON e.id = m.evento_id
                    WHERE fecha >= CURDATE()
                    GROUP BY e.id
                    ORDER BY e.fecha ASC 
                    LIMIT 4";
            
            $query = $conexion->query($sql);

            if ($query) {
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $items[] = $row;
                }
            }
            
            return $items;

        } catch (PDOException $e) {
            return [];
        }
    }

    /*
        Método: create(classCarrera $carrera)
        Descripción: Inserta un nuevo evento
        Uso: en método create del controlador carrera
    */
    public function create($carrera, $modalidades) {
        try {
            $db = $this->db->connect();
            $db->beginTransaction();

            // 1. Insertar en tabla EVENTOS
            $sqlEvento = "INSERT INTO Eventos 
                    (nombre, fecha, fecha_cierre_inscripcion, ubicacion, dificultad, descripcion, imagen, organizador_id, estado)
                    VALUES
                    (:nombre, :fecha, :fecha_cierre, :ubicacion, :dificultad, :descripcion, :imagen, :organizador_id, :estado)";

            
            $stmt = $db->prepare($sqlEvento);

            $stmt->bindParam(':nombre',         $carrera['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha',          $carrera['fecha']);
            $stmt->bindValue(':fecha_cierre',   $carrera['fecha_cierre_inscripcion'] ?? $carrera['fecha']);
            $stmt->bindParam(':ubicacion',      $carrera['ubicacion'], PDO::PARAM_STR);
            $stmt->bindParam(':dificultad',     $carrera['dificultad'], PDO::PARAM_STR);
            $stmt->bindParam(':descripcion',    $carrera['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(':imagen',         $carrera['imagen'], PDO::PARAM_STR);
            $stmt->bindParam(':organizador_id', $carrera['organizador_id'], PDO::PARAM_INT);
            $stmt->bindValue(':estado',         $carrera['estado'] ?? 'borrador');

            $stmt->execute();
            $idEvento = $db->lastInsertId();

            // 2. Insertar en tabla MODALIDADES
            $sqlMod = "INSERT INTO modalidades (evento_id, nombre, distancia, desnivel, precio, cupo_maximo, edad_minima, edad_maxima, track_url)
                       VALUES (:evento_id, :nombre_mod, :distancia, :desnivel, :precio, :cupo_maximo, :edad_min, :edad_max, :track_url)";
            
            $stmt = $db->prepare($sqlMod);

            foreach ($modalidades as $mod) {
                $stmt->execute([
                    ':evento_id'   => $idEvento,
                    ':nombre_mod'  => $mod['nombre'],
                    ':distancia'   => $mod['distancia'],
                    ':desnivel'    => $mod['desnivel'],
                    ':precio'      => $mod['precio'],
                    ':cupo_maximo' => $mod['cupo_maximo'],
                    ':edad_min'    => $mod['edad_minima'] ?? 18,
                    ':edad_max'    => $mod['edad_maxima'] ?? 99,
                    ':track_url'   => $mod['track_url'] ?? null
                ]);
            }
            

            $db->commit();
            return true;
            

        } catch (PDOException $e) {
            $db->rollBack();   
            $this->handleError($e);
            return false;
        }
    }

    /*
        Método: read(int $id)
        Descripción: Obtiene un evento por su ID con su modalidad principal
        Uso: en métodos edit, show y delete del controlador carrera
    */
    public function read(int $id) {
        try {
            $sql = "SELECT 
                    e.*,
                    m.distancia, 
                    m.desnivel, 
                    m.precio, 
                    m.cupo_maximo, 
                    m.id as modalidad_id,
                    m.nombre as modalidad,
                    m.edad_minima, 
                    m.edad_maxima,
                    m.track_url,
                    u.name AS organizador 
                FROM Eventos e
                INNER JOIN Users AS u ON e.organizador_id = u.id
                LEFT JOIN modalidades AS m ON e.id = m.evento_id
                WHERE e.id = :id 
                LIMIT 1";
            
            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            return $stmt->fetch();

        } catch (PDOException $e) {
           $this->handleError($e); 
        }
    }

    /*
        Método: update(classCarrera $carrera, $id)
        Descripción: Actualiza los datos de un evento
        Uso: en método update del controlador carrera
    */
    public function update(class_carrera $carrera, $id, $modalidades) {
        try {

            $db = $this->db->connect();
            $db->beginTransaction();

            // Solo incluyo la imagen si viene la por defecto o vacía. Así evito eliminar imágenes que ya están bien
            $updateImagen = "";
            if (!empty($carrera->imagen) && $carrera->imagen !== 'default.jpg') {
                $updateImagen = ", imagen = :imagen";
            }

            // Update EVENTO
            $sql = "UPDATE Eventos 
                    SET 
                        nombre = :nombre,
                        fecha = :fecha,
                        fecha_cierre_inscripcion = :fecha_cierre,
                        ubicacion = :ubicacion, 
                        dificultad = :dificultad,
                        descripcion = :descripcion                            
                        $updateImagen,
                        estado = :estado
                    WHERE id = :id 
                    LIMIT 1";

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':nombre',         $carrera->nombre, PDO::PARAM_STR);
            $stmt->bindParam(':fecha',          $carrera->fecha);
            $stmt->bindParam(':fecha_cierre',   $carrera->fecha_cierre_inscripcion);
            $stmt->bindParam(':ubicacion',      $carrera->ubicacion, PDO::PARAM_STR);
            $stmt->bindParam(':dificultad',     $carrera->dificultad, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion',    $carrera->descripcion, PDO::PARAM_STR);
            if (!empty($updateImagen)) {
                $stmt->bindParam(':imagen', $carrera->imagen);
            }
            $stmt->bindParam(':estado',         $carrera->estado, PDO::PARAM_STR);
            $stmt->bindParam(':id',             $id, PDO::PARAM_INT);

            $stmt->execute();

            // Update MODALIDADES
            // 1. Borrar modalidades antiguas
            $sqlDelete = "DELETE FROM modalidades WHERE evento_id = :id";
            $stmtDel = $db->prepare($sqlDelete);
            $stmtDel->bindValue(':id', $id, PDO::PARAM_INT);
            $stmtDel->execute();

            // 2. Insertar modalidades nuevas
            $sql2 = "INSERT INTO modalidades (evento_id, nombre, distancia, desnivel, precio, cupo_maximo, edad_minima, edad_maxima, track_url)
                     VALUES (:ev_id, :nom, :dist, :des, :pre, :cupo, :e_min, :e_max, :track_url)";

            $stmt2 = $db->prepare($sql2);

            foreach ($modalidades as $mod) {
                $stmt2->execute([
                    ':ev_id' => $id,
                    ':nom'   => $mod['nombre'],
                    ':dist'  => $mod['distancia'],
                    ':des'   => $mod['desnivel'],
                    ':pre'   => $mod['precio'],
                    ':cupo'  => $mod['cupo_maximo'],
                    ':e_min' => $mod['edad_minima'],
                    ':e_max' => $mod['edad_maxima'],
                    ':track_url' => $mod['track_url'] ?? null
                ]);
            }

            $db->commit();
            return true;

        } catch (PDOException $e) {
            $db->rollBack();
            $this->handleError($e);
            return false;
        }
    }

    /*
        Método: delete(int $id)
    */
    public function delete(int $id) {
        try {
            $sql = "DELETE FROM Eventos WHERE id = :id LIMIT 1";
            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
           $this->handleError($e); 
        }
    }

    /*
        Método: search(string $term)
    */
    public function search(string $term) {
        try {
            $sql = "SELECT 
                        e.id, e.nombre, e.fecha, e.ubicacion, e.dificultad, e.imagen,
                        m.distancia, m.desnivel,
                        u.name AS organizador
                    FROM Eventos e
                    INNER JOIN users u ON e.organizador_id = u.id
                    LEFT JOIN modalidades m ON e.id = m.evento_id
                    WHERE CONCAT_WS(' ', e.nombre, e.ubicacion, e.dificultad, u.name) LIKE :term
                    GROUP BY e.id
                    ORDER BY e.fecha ASC";

            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            $term = "%$term%";
            $stmt->bindParam(':term', $term, PDO::PARAM_STR);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            return $stmt;

        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    /*
        Método: order(int $criterio)
    */
    public function order(int $criterio) {
        $ordenes = [
            1 => "id",
            2 => "nombre",
            3 => "ubicacion", 
            4 => "distancia",
            5 => "desnivel",
            6 => "dificultad",
            7 => "fecha",      
            8 => "organizador"
        ];

        $orderBy = $ordenes[$criterio] ?? "id";

        try {
            $sql = "SELECT 
                        e.id, e.nombre, e.fecha, e.ubicacion, m.distancia, m.desnivel, e.dificultad, e.imagen,
                        u.name AS organizador
                    FROM Eventos e
                    INNER JOIN users u ON e.organizador_id = u.id
                    LEFT JOIN modalidades AS m ON e.id = m.evento_id
                    ORDER BY $orderBy ASC";

            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            return $stmt;

        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    /*
        Método: countTotal()
        Descripción: Cuenta el total de carreras disponibles en la tabla eventos.
        Uso: en el método render del controlador de carreras, para la paginación.
    */
    public function countTotal(){
        $sql = "SELECT COUNT(*) FROM eventos";
        $db = $this->db->connect();
        $stmt = $db->query($sql);

        return $stmt->fetchColumn();
    }

    /*
        Descripción: Obtiene el numero de plazas disponibles para cada modalidad
        Uso: En método show de controlador carrera. Vista detalles de carrera
    */
    public function getPlazasOcupadas($modalidad_id) {

        try {
            // Contamos todos menos los cancelados y fallidos
            $sql = "SELECT COUNT(*) FROM Inscripciones 
                    WHERE modalidad_id = :id AND estado_pago NOT IN ('cancelado', 'fallido')";
            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $modalidad_id]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /*
        Método para obtener todas las modalidades de un evento
        Uso: En método show de controlador carrera. Vista detalles de carrera
    */
    public function getModalidadesByEvento($evento_id) {
        try {
            $sql = "SELECT * FROM modalidades WHERE evento_id = :id ORDER BY distancia ASC";
            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $evento_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Versión corta del método anterior para obtener los datos de una sola modalidad
    public function getModalidad($id) {
        try {
            $sql = "SELECT * FROM modalidades WHERE id = :id LIMIT 1";
            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     *  Método para llevar a cabo la paginación de carreras
     */
    public function getPaginated($limit, $offset, $order = 1) {
        // Definimos los criterios de ordenación según tu dropdown
        $criterios = [
            1 => 'fecha DESC',      // Por defecto
            2 => 'nombre ASC',      // A-Z
            3 => 'ubicacion ASC',   // Ciudad
            4 => 'distancia ASC',   // Distancia
            5 => 'fecha ASC'        // Fecha próxima
        ];

        $orderBy = $criterios[$order] ?? $criterios[1];

        $sql = "SELECT e.id, e.nombre, e.fecha, e.ubicacion, e.dificultad, e.imagen,
                        m.distancia, m.desnivel, m.cupo_maximo, m.precio,
                        u.name AS organizador
                    FROM Eventos AS e
                    INNER JOIN users AS u ON e.organizador_id = u.id
                    LEFT JOIN modalidades AS m ON e.id = m.evento_id
                    GROUP BY e.id
                    ORDER BY $orderBy 
                    LIMIT :limit OFFSET :offset";
        
        try {
            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);   

        } catch (PDOException $e){
            $this->handleError($e);
        }
    }

    /**
     * Comprueba si hay resultados en la bd para una carrera
     * Devuelve T/F
     */
    public function hasResults($id) {
        try{
            $sql = "SELECT COUNT(*) FROM resultados r
                    INNER JOIN inscripciones i ON r.inscripcion_id = i.id
                    INNER JOIN modalidades m ON i.modalidad_id = m.id
                    WHERE m.evento_id = :id";
            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $id]);

            $count = $stmt->fetchColumn();

            return $count > 0;

        }catch (PDOException $e){
            $this->handleError($e);
        }
    }

    /*
        Método que obtiene los eventos en funcion del rol:
            - Si eres admin los obtiene todos
            - Si eres organizador solo los que hayas organizado
        Uso: en gestion de carreras.
    */
    public function getEventosPorRol($user_id, $role_id) {
        try {
            $db = $this->db->connect();
            
            // Si es Admin (role_id = 1), no filtramos por organizador
            if ($role_id == 1) {
                $sql = "SELECT e.*, COUNT(i.id) AS total_inscritos 
                        FROM eventos e
                        LEFT JOIN inscripciones i ON e.id = i.evento_id
                        GROUP BY e.id 
                        ORDER BY e.id ASC";
                $stmt = $db->prepare($sql);
                $stmt->execute();
            } else {
                // Si es Organizador, filtramos por el campo organizador_id
                $sql = "SELECT e.*, COUNT(i.id) AS total_inscritos 
                        FROM eventos e 
                        LEFT JOIN inscripciones i ON e.id = i.evento_id
                        WHERE organizador_id = :user_id
                        GROUP BY e.id 
                        ORDER BY e.id ASC";
                $stmt = $db->prepare($sql);
                $stmt->execute(['user_id' => $user_id]);
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

     public function getEventosPorRolOrdenados($user_id, $role_id, int $criterio){
       $ordenes = [
            1 => "e.id",
            2 => "e.nombre",
            7 => "e.fecha",
            8 => "e.estado"      
        ];

        $orderBy = $ordenes[$criterio] ?? "id";

        try{

            $db = $this->db->connect();

            $sql = "SELECT e.*, COUNT(i.id) AS total_inscritos 
                    FROM eventos e 
                    LEFT JOIN inscripciones i ON e.id = i.evento_id";  
            // Filtra por organizador si es el caso
            if ($role_id == 2) $sql .= " WHERE e.organizador_id = :user_id";
            // Añade el orden de búsqueda
            $sql .= " GROUP BY e.id
                      ORDER BY $orderBy ASC";

            $stmt = $db->prepare($sql);
            if ($role_id == 2) $stmt->bindParam(':user_id', $user_id);
            
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $resultados;

        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    public function searchEventosPorRol($user_id, $role_id, $term) {
        try {
            $db = $this->db->connect();
            $likeTerm = "%$term%"; 

                $sql = "SELECT e.*, COUNT(i.id) AS total_inscritos 
                        FROM eventos e 
                        LEFT JOIN inscripciones i ON e.id = i.evento_id 
                        WHERE (e.nombre LIKE :term OR e.ubicacion LIKE :term2)";

                if ($role_id == 2) {
                    $sql .= " AND e.organizador_id = :user_id";
                }

                $sql .= " GROUP BY e.id ORDER BY e.fecha DESC";

                $stmt = $db->prepare($sql);
                $params = [':term' => $likeTerm, ':term2' => $likeTerm];
                if ($role_id == 2) $params[':user_id'] = $user_id;

                $stmt->execute($params);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->handleError($e);
            return [];
        }
    }

    /**
     *      Modifica el estado de una carrera automaticamente cuando:
     *      - Se acaba el plazo de inscripcion pasa a 'cerrado'
     *      - Se realiza el evento pasa a 'finalizado'
     *      Uso en panel de control de eventos (vista gestión del controlador carrera)
     */

    public function actualizarEstados(){

        $ahora = date('Y-m-d H:i:s');   

        try{
            $db = $this->db->connect();
            
            $sql = "UPDATE eventos SET estado = 
                    CASE 
                        WHEN fecha < :ahora THEN 'Finalizado'
                        WHEN fecha_cierre_inscripcion < :ahora2 THEN 'Cerrado'
                        ELSE estado
                    END
                    WHERE estado != 'Finalizado'";

            $stmt = $db->prepare($sql);
            $stmt->execute(['ahora' => $ahora, 'ahora2' => $ahora]);

        }catch (PDOException $e){
            $this->handleError($e);
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

?>