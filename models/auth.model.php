<?php
/*
    Modelo:  auth.model.php
    Descripción: Modelo para gestionar los datos del controlador auth
*/
   

class authModel extends Model {

    /*
        Método: get_user_emial($email)
        Descripción: obtiene los detalles  de un usuario a partir del email
        Parámetros: 
            - email
        Devuelve:
            - Objeto de la clase  user
                - id
                - name
                - email
                - password
            - False. Si el email no corresponde a nigún usuario
    */
    public function get_user_email($email) {
        try {
        // Generamos select 
        $sql = "SELECT id, name, email, password FROM users WHERE email = :email LIMIT 1";
        // Conectar con la base de datos
        $geslibros = $this->db->connect();
        // Preparar la consulta obteniendo el objeto PDOStatement
        $stmt = $geslibros->prepare($sql);
        // Tipo fetch
         $stmt->setFetchMode(PDO::FETCH_OBJ);
        // Vincular los parámetros
        $stmt->bindParam(':email', $email, PDO::PARAM_STR, 50);
        // Ejecutamos sql
        $stmt->execute();
        // Devolvemos el objeto o falso
        return $stmt->fetch();
        
        } catch (PDOException $e) {
            // Manejo del error
            $this->handleError($e); 

        }
    }

    /*
        Método: validate_exists_email($email)
        Descripción: valida email existe en la tabla user
        Parámetros: 
            - $email
        Devuelve:
            - Verdadero - email existente
            - Falso - si no existe 
    */
    public function validate_exists_email($email) {
        try {
        // Generamos select 
        $sql = "SELECT email FROM users WHERE email = :email";
        // Conectar con la base de datos
        $geslibros = $this->db->connect();
        // Preparar la consulta obteniendo el objeto PDOStatement
        $stmt = $geslibros->prepare($sql);
        // Vincular los parámetros
        $stmt->bindParam(':email', $email, PDO::PARAM_STR, 50);
        // Ejecutamos sql
        $stmt->execute();

        // Validamos
        if ($stmt->rowCount() > 0) {
            return TRUE;
        }

        return FALSE;   

        } catch (PDOException $e) {
            // Manejo del error
            $this->handleError($e); 

        }
    }

     /*
        Método: validate_exists_name($name)
        Descripción: valida name existe en la tabla user
        Parámetros: 
            - $name
        Devuelve:
            - Verdadero - name existente
            - Falso - si no existe
    */
    public function validate_exists_name($name){
        try {

            // sentencia sql
            $sql = "SELECT * FROM Users WHERE name = :name"; 


            // conectamos con la base de datos
            $geslibros = $this->db->connect();

            // ejecuto prepare
            $stmt = $geslibros->prepare($sql);

            // vinculamos parámetros
            $stmt->bindParam(':name', $name, PDO::PARAM_STR, 50);

            // ejecutamos
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return TRUE;
            } 

            return FALSE;

        } catch (PDOException $e) {

            // Manejo del error
            $this->handleError($e); 
        }
    }

    /*
        Método: get_id_role_user($user_id)
        Descripción: obtiene el id del perfil de un usuario
        @param: 
            - $user_id: id del usuario
        Devuelve:
            - id del rol del usuario
        Observaciones:
            - Sólo va a devolver el primer rol asignado en la tabla roles_users por simplicidad
    */
    public function get_id_role_user(int $user_id)
    {

        try {

            // sentencia sql
            $sql = "SELECT role_id FROM roles_users WHERE user_id = :user_id LIMIT 1"; 

            // conectamos con la base de datos
            $geslibros = $this->db->connect();

            // ejecuto prepare
            $stmt = $geslibros->prepare($sql);

            // Tipo de fetch
            $stmt->setFetchMode(PDO::FETCH_OBJ);

            // vinculamos parámetros
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            // ejecutamos
            $stmt->execute();

            // Devuelvo el id del rol
            return $stmt->fetch()->role_id;

        } catch (PDOException $e) {

            $this->handleError($e); 

        }
    }

    /*
        Método: get_name_role_user($role_id)
        Descripción: obtiene el nombre del perfil de un usuario
        @param: 
            - $role_id: id del rol
        Devuelve:
            - nombre del rol del usuario
        Observaciones:
            - Sólo va a devolver el primer rol asignado en la tabla roles_users por simplicidad

    */

    public function get_name_role_user(int $role_id)
    {

        try {

            // sentencia sql
            $sql = "SELECT name FROM roles WHERE id = :role_id LIMIT 1"; 

            // conectamos con la base de datos
            $geslibros = $this->db->connect();

            // ejecuto prepare
            $stmt = $geslibros->prepare($sql);

            // Tipo de fetch
            $stmt->setFetchMode(PDO::FETCH_OBJ);

            // vinculamos parámetros
            $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);

            // ejecutamos
            $stmt->execute();

            // Devuelve el nombre del rol
            return $stmt->fetch()->name;

        } catch (PDOException $e) {

            $this->handleError($e); 

        }
    }

    /*
        metodo: create_user($name, $email, $hash)
        descripción: Crea un usuario en la bd a partir de los campos del formulario de registro
        parámetros: 
            - nombre.
            - email.
            - contraseña hasheada
        Devuelve:
            - id del último registro
    */
    public function create_user($name, $email, $hash){
        try {

            // PROCESO 1
            // Registro usuario
            // Sentencia sql
            $sql = "INSERT INTO users (name, email, password) 
                    VALUES (:name, :email, :password)";

            // Conexion con la bd
            $geslibros = $this->db->connect();

            // Iniciamos transacción
            $geslibros->beginTransaction();

            // Ejecuto prepare
            $stmt = $geslibros->prepare($sql);

            // Vinculamos parámetros
            $stmt->bindParam(":name", $name, PDO::PARAM_STR, 50);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR, 50);
            $stmt->bindParam(":password", $hash, PDO::PARAM_STR, 60);

            // Ejecutamos consulta
            $stmt->execute();

            // Obtenemos el id del ultimo registro
            $ultimo_id = $geslibros->lastInsertId();

            // SEGUNDO PROCEDIMIENTO
            // Añadir rol del usuario registrado
            $sql2 = "INSERT INTO roles_users (user_id, role_id) VALUES (:user_id, :role_id)";

            // Ejecuto prepare
            $stmt = $geslibros->prepare($sql2);

            // Rol de usuario registrado (id=3)
            $role_id = 3;

            // Vinculamos parámetros
            $stmt->bindParam(':user_id', $ultimo_id, PDO::PARAM_INT);
            $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);

            // Ejecutamos consulta
            $stmt->execute();

            // Confirmamos transacción
            $geslibros->commit();

            // Devolvermos el último id del usuario registrado
            return $ultimo_id;

        } catch (PDOException $e){

            // Deshacer transacción
            $geslibros->rollBack();

            // Manejo de errores
            $this->handleError($e); 
        }
    }

    public function handleError(PDOException $e) {
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