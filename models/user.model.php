<?php
    /*
        Modelo: userModel
        Descripción: Modelo para gestionar los datos de los usuarios y sus roles
    */

    class userModel extends Model {

        /*
            Método: get()
            Obtiene todos los usuarios con su respectivo rol
        */
        public function get() {
            try {
                $sql = "SELECT 
                            u.id,
                            u.name AS nombre,
                            u.email,
                            r.name AS rol
                        FROM users u
                        LEFT JOIN roles_users ru ON u.id = ru.user_id
                        LEFT JOIN roles r ON ru.role_id = r.id
                        ORDER BY u.id ASC";

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
            Método: get_roles()
            Obtiene array asociativo id => name para el select de la vista
        */
        public function get_roles() {
            try {
                $sql = "SELECT id, name FROM roles ORDER BY name ASC";
                $db = $this->db->connect();
                $stmt = $db->prepare($sql);
                $stmt->setFetchMode(PDO::FETCH_KEY_PAIR);
                $stmt->execute();
                return $stmt->fetchAll();
            } catch (PDOException $e) {
                $this->handleError($e);
            }
        }

        /*
            Método: create()
            Inserta usuario y asigna rol usando TRANSACCIONES
        */
        public function create(class_user $user, $role_id) {
            try {
                $db = $this->db->connect();
                $db->beginTransaction();

                $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    ':name'     => $user->nombre,
                    ':email'    => $user->email,
                    ':password' => $user->password
                ]);

                $user_id = $db->lastInsertId();

                $sql_role = "INSERT INTO roles_users (user_id, role_id) VALUES (:user_id, :role_id)";
                $stmt_role = $db->prepare($sql_role);
                $stmt_role->execute([
                    ':user_id' => $user_id,
                    ':role_id' => $role_id
                ]);

                $db->commit();
                return $user_id;

            } catch (PDOException $e) {
                if ($db->inTransaction()) $db->rollBack();
                $this->handleError($e);
            }
        }

        /*
            Método: update()
            Actualiza datos de usuario y su rol en la tabla intermedia
        */
        public function update(class_user $user, $id, $role_id) {
            try {
                $db = $this->db->connect();
                $db->beginTransaction();

                $sql = "UPDATE users SET name = :name, email = :email WHERE id = :id";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    ':name'  => $user->nombre,
                    ':email' => $user->email,
                    ':id'    => $id
                ]);

                // Actualizamos el rol (borrar e insertar o update directo)
                $sql_role = "UPDATE roles_users SET role_id = :role_id WHERE user_id = :user_id";
                $stmt_role = $db->prepare($sql_role);
                $stmt_role->execute([
                    ':role_id' => $role_id,
                    ':user_id' => $id
                ]);

                $db->commit();
                return true;

            } catch (PDOException $e) {
                if ($db->inTransaction()) $db->rollBack();
                $this->handleError($e);
            }
        }

        /*
            Método: read()
            Obtiene un usuario para edición
        */
        public function read(int $id) {
            try {
                $sql = "SELECT id, name AS nombre, email FROM users WHERE id = :id LIMIT 1";
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
            Método: delete()
            Descripción: Elimina un usuario y su asignación de rol usando TRANSACCIONES
            Parámetros: int $id
        */
        public function delete(int $id) {
            try {
                $db = $this->db->connect();
                $db->beginTransaction();

                // 1. Eliminar la relación en la tabla intermedia roles_users
                $sql_role = "DELETE FROM roles_users WHERE user_id = :id";
                $stmt_role = $db->prepare($sql_role);
                $stmt_role->execute([':id' => $id]);

                // 2. Eliminar el usuario en la tabla users
                $sql_user = "DELETE FROM users WHERE id = :id LIMIT 1";
                $stmt_user = $db->prepare($sql_user);
                $stmt_user->execute([':id' => $id]);

                $db->commit();
                return true;

            } catch (PDOException $e) {
                if ($db->inTransaction()) $db->rollBack();
                $this->handleError($e);
            }
        }

        /*
            Método: search($term)
            Descripción: Busca usuarios que coincidan con el término en nombre, email o rol
            Parámetros: 
                - $term: término de búsqueda
            Devuelve:
                - objeto PDOStatement con los resultados
        */
        public function search(string $term) {

            try {
                // Consulta con JOIN para incluir el nombre del rol en la búsqueda
                $sql = "SELECT 
                            u.id,
                            u.name AS nombre,
                            u.email,
                            r.name AS rol
                        FROM users u
                        LEFT JOIN roles_users ru ON u.id = ru.user_id
                        LEFT JOIN roles r ON ru.role_id = r.id
                        WHERE CONCAT_WS(' ', 
                                u.name, 
                                u.email, 
                                r.name
                            ) LIKE :term
                        ORDER BY u.id ASC";

                $db = $this->db->connect();
                $stmt = $db->prepare($sql);

                // Preparamos el término para LIKE
                $like = "%$term%";
                $stmt->bindParam(':term', $like, PDO::PARAM_STR);

                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();

                return $stmt;

            } catch (PDOException $e) {
                $this->handleError($e);
            }
        }

        /*
            Método: order($criterio)
            Descripción: Ordena la lista de usuarios por un criterio
            Parámetros:
                - $criterio: campo por el que se ordena la lista
                    1: id
                    2: nombre
                    3: email
                    4: rol
        */
        public function order(int $criterio) {

            // Mapa seguro de criterios para evitar inyección SQL en ORDER BY
            $ordenes = [
                1 => "id",
                2 => "nombre",
                3 => "email",
                4 => "rol"
            ];

            // Si el criterio no existe, ordenamos por id
            $orderBy = $ordenes[$criterio] ?? "id";

            try {
                // Usamos una subconsulta para que los alias coincidan con los criterios de ordenación
                $sql = "SELECT *
                        FROM (
                            SELECT 
                                u.id,
                                u.name AS nombre,
                                u.email,
                                r.name AS rol
                            FROM users u
                            LEFT JOIN roles_users ru ON u.id = ru.user_id
                            LEFT JOIN roles r ON ru.role_id = r.id
                        ) AS subconsulta
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
            Método: get_user_role_id()
            Obtiene el rol actual del usuario (para marcar 'selected' en el combo)
        */
        public function get_user_role_id($user_id) {
            try {
                $sql = "SELECT role_id FROM roles_users WHERE user_id = :user_id";
                $db = $this->db->connect();
                $stmt = $db->prepare($sql);
                $stmt->execute([':user_id' => $user_id]);
                return $stmt->fetchColumn(); 
            } catch (PDOException $e) {
                $this->handleError($e);
            }
        }

        /*
            Obtiene el nombre del rol de un usuario específico
            Parámetros: int $id ID del usuario
            Devuelve: string Nombre del rol
        */
        public function get_user_role_name($id) {
            try {
                // Consulta para obtener el nombre del rol a través de la tabla intermedia roles_users
                $sql = "
                    SELECT 
                        r.name 
                    FROM 
                        roles AS r
                    INNER JOIN 
                        roles_users AS ru ON r.id = ru.role_id
                    WHERE 
                        ru.user_id = :id
                    LIMIT 1
                ";

                // Preparamos la conexión (asumiendo que usas $this->db->connect())
                $conexion = $this->db->connect();
                $pdoSt = $conexion->prepare($sql);

                // Vinculamos parámetros
                $pdoSt->bindParam(':id', $id, PDO::PARAM_INT);

                // Establecemos el modo de obtención
                $pdoSt->setFetchMode(PDO::FETCH_OBJ);

                // Ejecutamos
                $pdoSt->execute();

                // Retornamos el nombre del rol
                $rol = $pdoSt->fetch();
                
                // Si existe el rol lo devuelve, si no, devuelve un valor por defecto
                return ($rol) ? $rol->name : 'Sin Rol';

            } catch (PDOException $e) {
                // En caso de error, podrías manejarlo o lanzarlo
                error_log("Error en get_user_role_name: " . $e->getMessage());
                return "Error";
            }
        }

        /*
            Validaciones (Siguiendo tu estilo de ISBN)
        */
        public function validate_unique_email($email, $id = null) {
            try {
                if ($id) {
                    // Caso de edición: el email debe ser único pero ignorando el ID actual
                    $sql = "SELECT email FROM users WHERE email = :email AND id != :id";
                    $params = [':email' => $email, ':id' => $id];
                } else {
                    // Caso de creación: el email no debe existir en ningún registro
                    $sql = "SELECT email FROM users WHERE email = :email";
                    $params = [':email' => $email];
                }

                $db = $this->db->connect();
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                
                return $stmt->rowCount() == 0;
            } catch (PDOException $e) {
                $this->handleError($e);
            }
        }


        /*
            Comprueba si un rol existe en la base de datos
        */
        public function validate_role_exists($role_id) {
            try {
                $sql = "SELECT id FROM roles WHERE id = :id";
                $db = $this->db->connect();
                $stmt = $db->prepare($sql);
                $stmt->execute([':id' => $role_id]);
                return $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                $this->handleError($e);
            }
        }

        /*
            Descripción: Valida si el id de un usuario existe
        */
        public function validate_id_user_exists($id) {
            try {
                $sql = "SELECT COUNT(*) FROM users WHERE id = :id";

                $conexion = $this->db->connect();
                $pdoSt = $conexion->prepare($sql);
                
                $pdoSt->bindParam(':id', $id, PDO::PARAM_INT);
                $pdoSt->execute();

                // fetchColumn() devuelve el número directamente
                return $pdoSt->fetchColumn() > 0;

            } catch (PDOException $e) {
                error_log("Error en validate_id_user_exists: " . $e->getMessage());
                return false;
            }
        }

        /*
            Método: handleError
            Descripción: Maneja los errores de la base de datos
        */
        private function handleError(PDOException $e)
        {
            // Incluir y cargar el controlador de errores
            $errorControllerFile = CONTROLLER_PATH . ERROR_CONTROLLER . '.php';
            
            if (file_exists($errorControllerFile)) {
                require_once $errorControllerFile;
                $mensaje = $e->getMessage() . " en la línea " . $e->getLine() . " del archivo " . $e->getFile();
                $controller = new Errores('DE BASE DE DATOS', 'Mensaje de Error: ', $mensaje);
                
            } else {
                // Fallback en caso de que el controlador de errores no exista
                echo "Error crítico: " . $e->getMessage();
                exit();
            }
        }
    }

?>