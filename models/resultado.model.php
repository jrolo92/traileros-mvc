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
                    c.nombre AS categoria, 
                    CONCAT_WS (' ', u.name, u.apellidos) AS nombre,
                    m.nombre AS modalidad 
                FROM resultados r
                INNER JOIN inscripciones i ON r.inscripcion_id = i.id
                INNER JOIN categorias c ON i.categoria_id = c.id
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


    /**
     * Procesa e inserta los resultados de un evento proveniente de la importación CSV.
     * 
     * El método realiza las siguientes acciones:
     * 1. Valida que el tiempo sea válido y no nulo.
     * 2. Vincula el número de dorsal con su correspondiente ID de inscripción dentro del evento.
     * 3. Registra el tiempo en la tabla de resultados.
     * 4. Utiliza "ON DUPLICATE KEY UPDATE" para permitir la re-importación de datos,
     *    actualizando el tiempo si el corredor ya tenía un registro previo.
     * 5. Cambia automáticamente el estado del corredor a 'FINISHER'.
     *
    */
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
     * Calcula y actualiza la posición general de los corredores.
     * 
     * Divide los resultados por modalidad. Esto garantiza que
     * cada modalidad tenga su propia clasificación.
     * 
    */
    public function calcularRankings($evento_id){
        try{
            $db = $this->db->connect();

            $sql = "UPDATE resultados r
                INNER JOIN (
                    SELECT 
                        r.id,
                        ROW_NUMBER() OVER (
                            PARTITION BY i.modalidad_id 
                            ORDER BY r.tiempo ASC
                        ) as nueva_pos_gen
                    FROM resultados r
                    INNER JOIN inscripciones i ON r.inscripcion_id = i.id
                    INNER JOIN modalidades m ON i.modalidad_id = m.id
                    WHERE m.evento_id = :evento_id 
                    AND r.estado = 'FINISHER'
                ) AS ranking ON r.id = ranking.id
                SET r.posicion_general = ranking.nueva_pos_gen";
            
            $stmt = $db->prepare($sql);
            $stmt->execute(['evento_id' => $evento_id]);

        }catch (PDOException $e){
            $this->handleError($e);
        }
    }

    /**
     * Calcula y actualiza la posición de cada corredor dentro de su categoría específica.
     * 
     * Divide los resultados primero por modalidad y luego por categoría_id.
     * Esto permite obtener el puesto real (1º, 2º, 3º...) que ha quedado el corredor
     * comparándolo solo con los participantes de su mismo sexo, rango de edad y carrera.
     * 
    */
    public function calcularRankingsCategorias($evento_id) {
        try {
            $db = $this->db->connect();

            $sql = "UPDATE resultados r
                    INNER JOIN (
                        SELECT 
                            r.id,
                            ROW_NUMBER() OVER (
                                PARTITION BY i.modalidad_id, i.categoria_id 
                                ORDER BY r.tiempo ASC
                            ) as nueva_posicion
                        FROM resultados r
                        INNER JOIN inscripciones i ON r.inscripcion_id = i.id
                        INNER JOIN modalidades m ON i.modalidad_id = m.id
                        WHERE m.evento_id = :evento_id 
                        AND r.estado = 'FINISHER'
                    ) AS ranking ON r.id = ranking.id
                    SET r.posicion_categoria = ranking.nueva_posicion";

            $stmt = $db->prepare($sql);
            return $stmt->execute(['evento_id' => $evento_id]);

        } catch (PDOException $e){
            $this->handleError($e);
        }
    }

    /**
     * Calcula la velocidad media de carrera expresada en tiempo por kilómetro (min/km).
     * 
     * Realiza la conversión de tiempo total a segundos, divide por la distancia
     * de la modalidad y vuelve a transformar el resultado en formato TIME.
     * Incluye validación de seguridad para evitar divisiones por cero si la distancia
     * no está definida.
     *
    */
    public function calcularRitmoMedio($evento_id){
        try {
            $db = $this->db->connect();
            $sql = "UPDATE resultados r
                    INNER JOIN inscripciones i ON r.inscripcion_id = i.id
                    INNER JOIN modalidades m ON i.modalidad_id = m.id
                    SET r.ritmo_medio = SEC_TO_TIME(TIME_TO_SEC(r.tiempo) / m.distancia)
                    WHERE m.evento_id = :evento_id
                    AND r.estado = 'FINISHER'
                    AND m.distancia > 0";

            $stmt = $db->prepare($sql);
            return $stmt->execute(['evento_id' => $evento_id]);

        }catch (PDOException $e){
            $this->handleError($e);
        }
    }

    public function search($evento_id, $expresion) {
        try {
            $sql = "SELECT 
                        r.*, 
                        i.dorsal,
                        c.nombre AS categoria, 
                        CONCAT_WS (' ', u.name, u.apellidos) AS nombre,
                        m.nombre AS modalidad 
                    FROM resultados r
                    INNER JOIN inscripciones i ON r.inscripcion_id = i.id
                    INNER JOIN categorias c ON i.categoria_id = c.id
                    INNER JOIN users u ON i.user_id = u.id
                    INNER JOIN modalidades m ON i.modalidad_id = m.id
                    WHERE m.evento_id = :evento_id
                    AND CONCAT_WS(' ', u.name, u.apellidos, i.dorsal, m.nombre) LIKE :expresion
                    ORDER BY r.posicion_general ASC";

            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            
            // % para busquedas que coincidan parcialmente
            $stmt->execute([
                'evento_id' => $evento_id,
                'expresion' => '%' . $expresion . '%'
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e){
            $this->handleError($e);
        } 
    }

    /**
     * Ordena los resultados de un evento en función de un criterio de ordenación
     */
    public function order($evento_id, $orden = 'posicion_general') {
        try {
            // Validamos el criterio de orden para evitar inyección SQL
            $criterios_validos = [
                'id' => 'r.id',
                'posicion_general' => 'r.posicion_general',
                'posicion_categoria' => 'r.posicion_categoria',
                'dorsal' => 'i.dorsal',
                'nombre' => 'u.name',
                'tiempo' => 'r.tiempo',
                'modalidad' => 'm.nombre',
                'categoria' => 'c.nombre'
            ];

            // Si el orden solicitado no existe, usamos el por defecto
            $orderBy = $criterios_validos[$orden] ?? 'r.posicion_general';

            $sql = "SELECT 
                        r.*, 
                        i.dorsal,
                        c.nombre AS categoria, 
                        CONCAT_WS (' ', u.name, u.apellidos) AS nombre,
                        m.nombre AS modalidad 
                    FROM resultados r
                    INNER JOIN inscripciones i ON r.inscripcion_id = i.id
                    INNER JOIN categorias c ON i.categoria_id = c.id
                    INNER JOIN users u ON i.user_id = u.id
                    INNER JOIN modalidades m ON i.modalidad_id = m.id
                    WHERE m.evento_id = :evento_id
                    ORDER BY $orderBy ASC"; 

            $db = $this->db->connect();
            $stmt = $db->prepare($sql);
            $stmt->execute(['evento_id' => $evento_id]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

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