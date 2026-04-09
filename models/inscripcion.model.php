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

        // Si el role_id no es el de administrador (ej: 1), filtramos por user_id
        if ($role_id != 1) { 
            $sql .= " WHERE i.user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $user_id]);
        } else {
            $stmt = $this->db->query($sql);
        }

        return $stmt->fetchAll();
    }

    public function create(class_inscripcion $inscripcion) {
        try {
            // Al ser pago inmediato, asignamos el dorsal ya mismo
            $inscripcion->dorsal = $this->generarSiguienteDorsal($inscripcion->evento_id);
            
            // El estado siempre será completado porque viene de un pago exitoso
            $inscripcion->estado_pago = 'completado';

            $sql = "INSERT INTO Inscripciones (user_id, evento_id, categoria_id, dorsal, metodo_pago, estado_pago, precio_final) 
                    VALUES (:user_id, :evento_id, :categoria_id, :dorsal, :metodo_pago, :estado_pago, :precio_final)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'user_id'      => $inscripcion->user_id,
                'evento_id'    => $inscripcion->evento_id,
                'categoria_id' => $inscripcion->categoria_id,
                'dorsal'       => $inscripcion->dorsal,
                'metodo_pago'  => $inscripcion->metodo_pago,
                'estado_pago'  => $inscripcion->estado_pago,
                'precio_final' => $inscripcion->precio_final
            ]);

        } catch (PDOException $e) {
            return false;
        }
    }

    public function read($user_id, $evento_id) {
        $sql = "SELECT * FROM Inscripciones WHERE user_id = :u AND evento_id = :e";
        $stmt = $this->db->prepare($sql);
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
        
        $stmt = $this->db->prepare($sql);
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
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['u' => $user_id, 'e' => $evento_id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Método opcional para eliminar totalmente la inscripción
    public function hardDelete($user_id, $evento_id) {
        $sql = "DELETE FROM Inscripciones WHERE user_id = :u AND evento_id = :e";
        return $this->db->prepare($sql)->execute(['u' => $user_id, 'e' => $evento_id]);
    }   

    // --- Lógica de Negocio ---
    public function getCategoriaAdecuada($edad, $sexo) {
        // Buscamos la categoría donde encaje la edad y el sexo (o mixto)
        $sql = "SELECT id FROM Categorias 
                WHERE :edad BETWEEN edad_min AND edad_max 
                AND (sexo = :sexo OR sexo = 'Mixto') 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['edad' => $edad, 'sexo' => $sexo]);
        return $stmt->fetchColumn();
    }

    public function isUserInscribed($user_id, $evento_id) {
        $sql = "SELECT COUNT(*) FROM Inscripciones WHERE user_id = :u AND evento_id = :e";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['u' => $user_id, 'e' => $evento_id]);
        return $stmt->fetchColumn() > 0;
    }

    public function getAllCategorias() {
        return $this->db->query("SELECT * FROM Categorias ORDER BY edad_min ASC")->fetchAll();
    }

    public function getDetalleCompleto($user_id, $evento_id) {
        // Similar al get() pero para un solo registro y con más info
        $sql = "SELECT i.*, e.*, u.*, c.nombre as categoria_nombre
                FROM Inscripciones i
                JOIN Eventos e ON i.evento_id = e.id
                JOIN users u ON i.user_id = u.id
                LEFT JOIN Categorias c ON i.categoria_id = c.id
                WHERE i.user_id = :u AND i.evento_id = :e";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['u' => $user_id, 'e' => $evento_id]);
        return $stmt->fetch();
    }

    private function generarSiguienteDorsal($evento_id) {
        // Buscamos el dorsal máximo actual para este evento
        $sql = "SELECT MAX(dorsal) FROM Inscripciones WHERE evento_id = :evento_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['evento_id' => $evento_id]);
        $max_dorsal = $stmt->fetchColumn();

        // Si no hay nadie inscrito, empezamos por el 1, si hay, sumamos 1
        return ($max_dorsal) ? $max_dorsal + 1 : 1;
    }

    /* Método: search
    Descripción: Permite buscar por nombre de usuario, apellidos o nombre del evento.
    */
    public function search($term) {
        $sql = "SELECT i.*, 
                    e.nombre as evento_nombre, 
                    u.name as usuario_nombre, 
                    u.apellidos as usuario_apellidos,
                    c.nombre as categoria_nombre
                FROM Inscripciones i
                INNER JOIN Eventos e ON i.evento_id = e.id
                INNER JOIN users u ON i.user_id = u.id
                LEFT JOIN Categorias c ON i.categoria_id = c.id
                WHERE u.name LIKE :term 
                OR u.apellidos LIKE :term 
                OR e.nombre LIKE :term
                OR i.dorsal LIKE :term";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['term' => "%$term%"]);
        return $stmt->fetchAll();
    }

    /* Método: order
    Descripción: Ordena el listado según la columna solicitada.
    */
    public function order($criterio) {
        
        $columnas_permitidas = [
            'dorsal'       => 'i.dorsal',
            'usuario'      => 'u.apellidos',
            'evento'       => 'e.nombre',
            'fecha'        => 'i.fecha_inscripcion',
            'categoria'    => 'c.nombre'
        ];

        $orden = $columnas_permitidas[$criterio] ?? 'i.fecha_inscripcion DESC';

        $sql = "SELECT i.*, 
                    e.nombre as evento_nombre, 
                    u.name as usuario_nombre, 
                    u.apellidos as usuario_apellidos,
                    c.nombre as categoria_nombre
                FROM Inscripciones i
                INNER JOIN Eventos e ON i.evento_id = e.id
                INNER JOIN users u ON i.user_id = u.id
                LEFT JOIN Categorias c ON i.categoria_id = c.id
                ORDER BY $orden";

        return $this->db->query($sql)->fetchAll();
    }
}