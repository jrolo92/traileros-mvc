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
                        e.id,
                        e.nombre,
                        e.fecha,
                        e.ubicacion,
                        e.distancia,
                        e.desnivel,
                        e.dificultad,
                        e.cupo_maximo,
                        e.precio,      
                        e.imagen,
                        u.name AS organizador
                    FROM Eventos AS e
                    INNER JOIN users AS u ON e.organizador_id = u.id
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
            $sql = "SELECT * FROM eventos 
                    WHERE fecha >= CURDATE() 
                    ORDER BY fecha ASC 
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
    public function create(class_carrera $carrera) {
        try {
            $sql = "INSERT INTO Eventos 
                    (nombre, fecha, ubicacion, distancia, desnivel, dificultad, descripcion, cupo_maximo, precio, imagen, organizador_id)
                    VALUES
                    (:nombre, :fecha, :ubicacion, :distancia, :desnivel, :dificultad, :descripcion, :cupo_maximo, :precio, :imagen, :organizador_id)";

            $db = $this->db->connect();
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':nombre',         $carrera->nombre, PDO::PARAM_STR);
            $stmt->bindParam(':fecha',          $carrera->fecha);
            $stmt->bindParam(':ubicacion',      $carrera->ubicacion, PDO::PARAM_STR);
            $stmt->bindParam(':distancia',      $carrera->distancia);
            $stmt->bindParam(':desnivel',       $carrera->desnivel, PDO::PARAM_INT);
            $stmt->bindParam(':dificultad',     $carrera->dificultad, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion',    $carrera->descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':cupo_maximo',    $carrera->cupo_maximo, PDO::PARAM_INT);
            $stmt->bindParam(':precio',         $carrera->precio);
            $stmt->bindParam(':imagen',         $carrera->imagen, PDO::PARAM_STR);
            $stmt->bindParam(':organizador_id', $carrera->organizador_id, PDO::PARAM_INT);

            $stmt->execute();
            return $db->lastInsertId();

        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    /*
        Método: read(int $id)
        Descripción: Obtiene un evento por su ID
    */
    public function read(int $id) {
        try {
            $sql = "SELECT 
                    e.*, 
                    u.name AS organizador 
                FROM Eventos e
                INNER JOIN Users AS u ON e.organizador_id = u.id
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
            $sql = "UPDATE Eventos 
                    SET 
                        nombre = :nombre,
                        fecha = :fecha,
                        ubicacion = :ubicacion, 
                        distancia = :distancia, 
                        desnivel = :desnivel,
                        dificultad = :dificultad,
                        descripcion = :descripcion,
                        cupo_maximo = :cupo_maximo,
                        precio = :precio,           
                        imagen = :imagen,
                        organizador_id = :organizador_id
                    WHERE id = :id 
                    LIMIT 1";

            $db = $this->db->connect();
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':nombre',         $carrera->nombre, PDO::PARAM_STR);
            $stmt->bindParam(':fecha',          $carrera->fecha);
            $stmt->bindParam(':ubicacion',      $carrera->ubicacion, PDO::PARAM_STR);
            $stmt->bindParam(':distancia',      $carrera->distancia);
            $stmt->bindParam(':desnivel',       $carrera->desnivel, PDO::PARAM_INT);
            $stmt->bindParam(':dificultad',     $carrera->dificultad, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion',    $carrera->descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':cupo_maximo',    $carrera->cupo_maximo, PDO::PARAM_INT);
            $stmt->bindParam(':precio',         $carrera->precio);
            $stmt->bindParam(':imagen',         $carrera->imagen, PDO::PARAM_STR);
            $stmt->bindParam(':organizador_id', $carrera->organizador_id, PDO::PARAM_INT);
            $stmt->bindParam(':id',             $id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
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
                        e.id, 
                        e.nombre, 
                        e.fecha, 
                        e.ubicacion, 
                        e.distancia, 
                        e.desnivel, 
                        e.dificultad,
                        e.cupo_maximo,
                        e.precio,      
                        e.cupo_maximo,
                        e.precio,      
                        e.imagen,
                        u.name AS organizador
                    FROM Eventos e
                    INNER JOIN users u ON e.organizador_id = u.id
                    WHERE CONCAT_WS(' ', e.nombre, e.ubicacion, e.dificultad, u.name) LIKE :term
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
                        e.id, e.nombre, e.fecha, e.ubicacion, e.distancia, e.desnivel, e.dificultad, e.imagen,
                        u.name AS organizador
                    FROM Eventos e
                    INNER JOIN users u ON e.organizador_id = u.id
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
        Método: getPlazasDisponibles($evento_id)
        Descripcion: Calcula las plazas disponibles teniendo en cuenta las inscripciones actuales
        Devuelve: Plazas disponibles en el momento de la consulta.
    */
    public function getPlazasDisponibles($evento_id) {
        try {
            $db = $this->db->connect();
            
            // 1. Obtener cupo máximo
            $sqlCupo = "SELECT cupo_maximo FROM Eventos WHERE id = :id";
            $stmt1 = $db->prepare($sqlCupo);
            $stmt1->execute(['id' => $evento_id]);
            $cupo = $stmt1->fetchColumn();

            // 2. Contar inscritos
            $sqlInscritos = "SELECT COUNT(*) FROM Inscripciones WHERE evento_id = :id";
            $stmt2 = $db->prepare($sqlInscritos);
            $stmt2->execute(['id' => $evento_id]);
            $inscritos = $stmt2->fetchColumn();

            return $cupo - $inscritos;

        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getPlazasOcupadas($evento_id) {
        // Solo contamos a los que ya han pasado por caja
        $sql = "SELECT COUNT(*) FROM Inscripciones 
                WHERE evento_id = :id AND estado_pago = 'completado'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $evento_id]);
        return $stmt->fetchColumn();
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