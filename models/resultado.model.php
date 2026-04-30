<?php

class ResultadoModel extends Model {

    // Obtener datos básicos de la carrera
    public function getEvento($id) {
        try {
            $sql = "SELECT id, nombre, fecha FROM eventos WHERE id = :id";
            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e){
            $this->handleError($e);
        }
    }

    /**
     * Descripción: Obtiene todas las clasificaciones ordenadas por tiempo y posición
     * Uso: en render() de resultados.
    */
    public function getClasificacion($evento_id) {
        try {
            $sql = "SELECT 
                    r.*, 
                    i.dorsal, 
                    CONCAT_WS (' ', u.name, u.apellidos) AS nombre,
                    m.nombre AS modalidad 
                FROM resultados r
                INNER JOIN inscripciones i ON r.inscripcion_id = i.id
                INNER JOIN users u ON i.user_id = u.id
                INNER JOIN modalidades m ON i.modalidad_id = m.id
                WHERE m.evento_id = :evento_id
                ORDER BY  r.posicion_general ASC";

            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            $stmt->execute(['evento_id' => $evento_id]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e){
            $this->handleError($e);
        } 
    }

    /**
     *  Descripción: Obtiene todos los inscritos y sus resultados (incluso si no tienen)
     *  Uso: 
     */
    public function getInscritosConResultado($evento_id) {
        try{
            $items = [];

            // Hacemos LEFT JOIN con resultados para que aparezcan TODOS los inscritos
            // aunque todavía no tengan tiempo asignado
            $sql ="SELECT 
                    i.id AS inscripcion_id, 
                    i.dorsal, 
                    CONCAT_WS (' ', u.name, u.apellidos) AS nombre, 
                    m.nombre AS modalidad,
                    r.tiempo, 
                    r.estado
                FROM inscripciones i
                INNER JOIN users u ON i.user_id = u.id
                INNER JOIN modalidades m ON i.modalidad_id = m.id
                LEFT JOIN resultados r ON i.id = r.inscripcion_id
                WHERE m.evento_id = :evento_id
                ORDER BY i.dorsal ASC";

            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            $stmt->execute(['evento_id' => $evento_id]);

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $items[] = $row;
            }

            return $items;

        } catch (PDOException $e){
            $this->handleError($e);
        }
    }

    public function saveResultadosCsv ($evento_id, $dorsal, $tiempo) {

        // 1. Limpieza y validación previa
        $dorsal = trim($dorsal);
        $tiempo = trim($tiempo);

        // Si no hay tiempo, no podemos marcarlo como FINISHER, así que saltamos la fila
        if (empty($tiempo) || $tiempo == "" || $tiempo == "00:00:00") {
            return false;
        }

        try{
            $db = $this->db->connect();

            // Obtiene el id de la inscripcion
            $sql_ins = "SELECT i.id FROM inscripciones i 
                        INNER JOIN modalidades m ON i.modalidad_id = m.id
                        WHERE m.evento_id = :evento_id AND i.dorsal = :dorsal
                        LIMIT 1";
            
            $stmt = $db->prepare($sql_ins);
            $stmt->execute(['evento_id' => $evento_id, 'dorsal' => $dorsal]);

            $inscripcion = $stmt->fetch();

            if ($inscripcion) {
                $inscripcion_id = $inscripcion['id'];

                // Inserta el resultado (si ya existe actualiza el tiempo) y cambia estado a FINISHER
                $sql = "INSERT INTO resultados (inscripcion_id, tiempo, estado)
                VALUES (:inscripcion_id, :tiempo, 'FINISHER')
                ON DUPLICATE KEY UPDATE
                    tiempo = VALUES(tiempo),
                    estado = 'FINISHER'";

                $stmt = $db->prepare($sql);
                return $stmt->execute([
                    'inscripcion_id' => $inscripcion_id,
                    'tiempo'         => $tiempo
                ]);
            }
            return false;

        } catch (PDOException $e){
            $this->handleError($e);
        }
    }

    /**
     *  Actualiza la tabla de resultados ordenandola por tiempo y estableciendo la posición
     *  general de los corredores.
     */
    public function calcularRankings($evento_id){
        try{
            $db = $this->db->connect();

            // Usa una variable de usuario de MySQL inicializandola en 0
            $db->query("SET @posicion := 0");

            /*
                Al ejecutar el update va a ordenar los resultados por tiempo y le va a ir
                asignando la posición siguiendo ese orden
            */
            $sql = "UPDATE resultados r
                    INNER JOIN inscripciones i ON r.inscripcion_id = i.id
                    INNER JOIN modalidades m ON i.modalidad_id = m.id
                    SET r.posicion_general = (@posicion := @posicion + 1)
                    WHERE m.evento_id = :evento_id 
                    AND r.estado = 'FINISHER'
                    ORDER BY r.tiempo ASC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute(['evento_id' => $evento_id]);

        }catch (PDOException $e){
            $this->handleError($e);
        }
    }

    /**
     *  Mismo método que el anterior pero para posición por categorias.
     *  Ordena por categorias y calcula la posición mediante un contador.
     *  Si cambia de categoría reinicia el contador.
     */ 
    public function calcularRankingsCategorias($evento_id) {
        try {
            $db = $this->db->connect();

            //  Inicializamos variables de MySQL: 
            // @pos: el contador de posición
            // @cat: para guardar el id de la categoria actual
            $db->query("SET @pos := 0, @cat_actual := 0");

            $sql = "UPDATE resultados r
                    INNER JOIN inscripciones i ON r.inscripcion_id = i.id
                    INNER JOIN modalidades m ON i.modalidad_id = m.id
                    SET r.posicion_categoria = (
                        SELECT @pos := IF(@cat_actual = i.categoria_id, @pos + 1, 
                        GREATEST(0, @cat_actual := i.categoria_id) + 1)
                    )
                    WHERE m.evento_id = :evento_id 
                    AND r.estado = 'FINISHER'
                    ORDER BY i.categoria_id ASC, r.tiempo ASC";

            $stmt = $db->prepare($sql);
            return $stmt->execute(['evento_id' => $evento_id]);

        } catch (PDOException $e){
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