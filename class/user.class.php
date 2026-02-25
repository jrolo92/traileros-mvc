<?php

    /*
    clase: class_user
    descripción: clase para gestionar los usuarios

    */

    class class_user {
        public $id;
        public $nombre;
        public $email;
        public $password;
        public $role_id;
    

        public function __construct(
            $id = null,
            $nombre = null,
            $email = null,
            $password = null,
            $role_id = null
        ) {
            $this->id = $id;
            $this->nombre = $nombre;
            $this->email = $email;
            $this->password = $password;
            $this->role_id = $role_id;
        }
    }

?>