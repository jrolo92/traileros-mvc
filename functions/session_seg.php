<?php

    function sec_session_start($regenerate = false) {

        $session_name = "sec_session_id";
        $secure = false;
        $httponly = true;

        if (ini_set('session.use_only_cookies', 1) === FALSE) {
            header("Location: index.php");
            exit();
        }

        $cookieParams = session_get_cookie_params();

        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);

        session_name($session_name);
        session_start();
        
        // Regeneración del id personalizada
        if ($regenerate) {
            // Si forzamos la regeneración (ej. en Login o Update)
            session_regenerate_id(true);
            $_SESSION['last_regen'] = time();
        } else {
            // Si es navegación normal, solo regeneramos cada 15 minutos (900 segundos)
            if (!isset($_SESSION['last_regen'])) {
                session_regenerate_id(true);
                $_SESSION['last_regen'] = time();
            } elseif (time() - $_SESSION['last_regen'] > 900) {
                session_regenerate_id(true);
                $_SESSION['last_regen'] = time();
            }
        }

    }

    function sec_session_destroy() {

        session_destroy();
        session_unset();
        
    }

?>