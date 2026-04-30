<?php

class App {
    
  

    public function __construct()
    {
        // Obtener la URL y sanitizarla
        $url = isset($_GET['url']) ? filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL) : null;
        $url = explode('/', $url);

        // Determinar el controlador
        $controllerName = (empty($url[0]) || $url[0] === 'index') ? DEFAULT_CONTROLLER : $url[0];
        $controllerFile = CONTROLLER_PATH . $controllerName . '.php';

        try {
            if (file_exists($controllerFile)) {
                // Incluir y cargar el controlador
                require_once $controllerFile;
                $controller = new $controllerName();

                // Cargar el modelo asociado al controlador, si existe
                if ($controllerName !== 'error' && method_exists($controller, 'loadModel')) {
                    $controller->loadModel($controllerName);
                }

                // Determinar el método y los parámetros
                $methodName = isset($url[1]) ? $url[1] : 'render';
                $params = array_slice($url, 2);

                // Validar que el método exista
                if (method_exists($controller, $methodName)) {
                    $controller->$methodName($params);
                    // call_user_func_array([$controller, $methodName], (array) $params);
                } else {
                    throw new Exception("El método '{$methodName}' no existe en el controlador '{$controllerName}'.");
                }
            } else {
                throw new Exception("El controlador '{$controllerName}' no se encuentra.");
            }
        } catch (Exception $e) {
            // Manejo centralizado de errores
            $this->renderErrorPage($e);
        }
    }

    private function renderErrorPage(Exception $e)
    {
        // Incluir y cargar el controlador de errores
        $errorControllerFile = CONTROLLER_PATH . ERROR_CONTROLLER . '.php';
        
        if (file_exists($errorControllerFile)) {
            require_once $errorControllerFile;
            // Cogemos el código de la excepción (si es 0, ponemos 500)
            $code = ($e->getCode() !== 0) ? $e->getCode() : 500;
            
            $titulos = [
                403 => 'Acceso Denegado',
                404 => 'No Encontrado',
                405 => 'Método no permitido',
                500 => 'Error Interno'
            ];
            $titulo = $titulos[$code] ?? 'Error';

            new Errores($code, $titulo, $e->getMessage());
            exit;
        } else {
            // Fallback en caso de que el controlador de errores no exista
            die("Error crítico: " . $e->getMessage());       
        }
    }
}

?>