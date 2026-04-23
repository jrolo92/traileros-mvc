<?php

/*
    Modelo: carreraModel
    Descripción: Gestiona el acceso a los datos de las carreras en la BD traileros
*/

class carreraModel extends Model {

    /*
        Método: get()
        Descripción: Obtiene todos los eventos con el nombre del organizador
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
    */
    public function getProximas() {
        $items = [];
        try {
            // Forzamos la obtención de la conexión
            $conexion = $this->db->connect(); 
            
            // Usamos una consulta simple primero para asegurar resultados
            $sql = "SELECT e.*, m.distancia 
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
    */
    public function create($carrera, $modalidades) {
        try {
            $db = $this->db->connect();
            $db->beginTransaction();

            // 1. Insertar en tabla EVENTOS
            $sqlEvento = "INSERT INTO Eventos 
                    (nombre, fecha, ubicacion, dificultad, descripcion, imagen, organizador_id, edad_minima, edad_maxima)
                    VALUES
                    (:nombre, :fecha, :ubicacion, :dificultad, :descripcion, :imagen, :organizador_id, :edad_minima, :edad_maxima)";

            
            $stmt = $db->prepare($sqlEvento);

            $stmt->bindParam(':nombre',         $carrera['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha',          $carrera['fecha']);
            $stmt->bindParam(':ubicacion',      $carrera['ubicacion'], PDO::PARAM_STR);
            $stmt->bindParam(':dificultad',     $carrera['dificultad'], PDO::PARAM_STR);
            $stmt->bindParam(':descripcion',    $carrera['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(':imagen',         $carrera['imagen'], PDO::PARAM_STR);
            $stmt->bindParam(':organizador_id', $carrera['organizador_id'], PDO::PARAM_INT);
            $stmt->bindParam(':edad_minima',    $carrera['edad_minima'], PDO::PARAM_INT);
            $stmt->bindParam(':edad_maxima',    $carrera['edad_maxima'], PDO::PARAM_INT);

            $stmt->execute();
            $idEvento = $db->lastInsertId();

            // 2. Insertar en tabla MODALIDADES
            $sqlMod = "INSERT INTO modalidades (evento_id, nombre, distancia, desnivel, precio, cupo_maximo)
                       VALUES (:evento_id, :nombre_mod, :distancia, :desnivel, :precio, :cupo_maximo)";
            
            $stmt = $db->prepare($sqlMod);

            foreach ($modalidades as $mod) {
                $stmt->bindParam(':evento_id',      $idEvento, PDO::PARAM_INT);
                $stmt->bindParam(':nombre_mod',     $mod['nombre'], PDO::PARAM_STR);
                $stmt->bindParam(':distancia',      $mod['distancia'], PDO::PARAM_INT);
                $stmt->bindParam(':desnivel',       $mod['desnivel'], PDO::PARAM_INT);
                $stmt->bindParam(':precio',         $mod['precio'], PDO::PARAM_INT);
                $stmt->bindParam(':cupo_maximo',    $mod['cupo_maximo'], PDO::PARAM_INT);

                $stmt->execute();
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
    */
    public function read(int $id) {
        try {
            $sql = "SELECT 
                    e.*,
                    m.distancia, m.desnivel, m.precio, m.cupo_maximo, m.id as modalidad_id,
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
    */
    public function update(class_carrera $carrera, $id) {
        try {

            $db = $this->db->connect();
            $db->beginTransaction();

            // Update EVENTO
            $sql = "UPDATE Eventos 
                    SET 
                        nombre = :nombre,
                        fecha = :fecha,
                        ubicacion = :ubicacion, 
                        dificultad = :dificultad,
                        descripcion = :descripcion,       
                        imagen = :imagen,
                    WHERE id = :id 
                    LIMIT 1";

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':nombre',         $carrera->nombre, PDO::PARAM_STR);
            $stmt->bindParam(':fecha',          $carrera->fecha);
            $stmt->bindParam(':ubicacion',      $carrera->ubicacion, PDO::PARAM_STR);
            $stmt->bindParam(':dificultad',     $carrera->dificultad, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion',    $carrera->descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':imagen',         $carrera->imagen, PDO::PARAM_STR);
            $stmt->bindParam(':id',             $id, PDO::PARAM_INT);

            // Update Modalidad (buscamos la primera del evento)
            $sql2 = "UPDATE modalidades SET distancia=:dist, desnivel=:desn, precio=:pre, cupo_maximo=:cup WHERE evento_id=:id LIMIT 1";
            $db->prepare($sql2);
            $db->execute([
                ':dist' => $carrera->distancia, ':desn' => $carrera->desnivel, 
                ':pre' => $carrera->precio, ':cup' => $carrera->cupo_maximo, ':id' => $id
            ]);

            return $db->commit();

        } catch (PDOException $e) {
            $db->rollBack();
            $this->handleError($e);
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

    // Cuenta el total de carreras disponibles en la tabla eventos
    public function countTotal(){
        $sql = "SELECT COUNT(*) FROM eventos";
        $db = $this->db->connect();
        $stmt = $db->query($sql);

        return $stmt->fetchColumn();
    }

    public function getPlazasOcupadas($evento_id) {

        // Contamos todos menos los cancelados y fallidos
        $sql = "SELECT COUNT(*) FROM Inscripciones 
                WHERE evento_id = :id AND estado_pago NOT IN ('cancelado', 'fallido')";
        $db = $this->db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $evento_id]);
        return $stmt->fetchColumn();
    }

    // Método para obtener todas las modalidades de un evento
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
                    ORDER BY $orderBy LIMIT :limit OFFSET :offset";
        
        $db = $this->db->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
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