<?php
// Reutilizamos todo el código de userModel sin duplicarlo
require_once 'models/user.model.php';

// Solo declaramos la clase si no existe ya en la memoria de PHP
if (!class_exists('accountModel')) {
    class accountModel extends userModel {
    
        // Al heredar, ya tiene todos los métodos: read, update, delete, etc.
    }
}