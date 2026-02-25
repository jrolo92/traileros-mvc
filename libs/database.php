<?php

    class Database {

        private $host;
        private $db;
        private $user;
        private $password;
        private $charset;
        
        public function __construct() {

            $this->host = HOST;
            $this->db = DB;
            $this->user = USER;
            $this->password = PASSWORD;
            $this->charset = CHARSET;

        }

        public function connect() {

            try {
                
                $dbh = "mysql:host=".$this->host.";dbname=".$this->db;
                $charset = $this->charset;
                $opciones = [

                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => FALSE,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
                ];

                $pdo = new PDO($dbh, $this->user, $this->password, $opciones);
                
                return $pdo;
            
            } catch(PDOException $e) {

                // Manejo centralizado de errores
                $this->handleError($e);
             
            }

        }

    private function handleError(PDOException $e)
    {
        // Incluir y cargar el controlador de errores
        $errorControllerFile = CONTROLLER_PATH . ERROR_CONTROLLER . '.php';
        
        if (file_exists($errorControllerFile)) {
            require_once $errorControllerFile;
            $controller = new Errores('DE BASE DE DATOS', 'Error de Conexión', $e->getMessage());
            // $controller->renderError($e->getMessage());
        } else {
            // Fallback en caso de que el controlador de errores no exista
            echo "Error crítico: " . $e->getMessage();
        }
    }
    }

?>