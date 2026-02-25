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
        Método: create(classCarrera $carrera)
        Descripción: Inserta un nuevo evento
    */
    public function create(class_carrera $carrera) {
        try {
            $sql = "INSERT INTO Eventos 
                    (nombre, fecha, ubicacion, distancia, desnivel, dificultad, descripcion, imagenUrl, organizador_id)
                    VALUES
                    (:nombre, :fecha, :ubicacion, :distancia, :desnivel, :dificultad, :descripcion, :imagenUrl, :organizador_id)";

            $db = $this->db->connect();
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':nombre',         $carrera->nombre, PDO::PARAM_STR);
            $stmt->bindParam(':fecha',          $carrera->fecha);
            $stmt->bindParam(':ubicacion',      $carrera->ubicacion, PDO::PARAM_STR);
            $stmt->bindParam(':distancia',      $carrera->distancia);
            $stmt->bindParam(':desnivel',       $carrera->desnivel, PDO::PARAM_INT);
            $stmt->bindParam(':dificultad',     $carrera->dificultad, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion',    $carrera->descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':imagenUrl',      $carrera->imagenUrl, PDO::PARAM_STR);
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
            $sql = "SELECT * FROM Eventos WHERE id = :id LIMIT 1";
            
            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_OBJ);
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
                        imagenUrl = :imagenUrl,
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
            $stmt->bindParam(':imagenUrl',      $carrera->imagenUrl, PDO::PARAM_STR);
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
                        e.id, e.nombre, e.fecha, e.ubicacion, e.distancia, e.desnivel, e.dificultad,
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
                        e.id, e.nombre, e.fecha, e.ubicacion, e.distancia, e.desnivel, e.dificultad,
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