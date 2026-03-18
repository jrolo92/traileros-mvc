<?php

    /*
        clase: class_user
        descripción: clase para gestionar los usuarios
    */

    class class_user {
        public $id;

        // Credenciales y sistema
        public $nombre;
        public $email;
        public $password;
        public $avatar;

        // Datos Personales
        public $apellidos;
        public $sexo;
        public $fecha_nac;
        public $dni;

        // Datos Contacto
        public $tlf;
        public $tlf_emg;
        public $direccion;
        public $poblacion;
        public $provincia;
        public $cp;
        public $pais;

        // Datos deportivos
        public $club;
        public $talla;
        public $es_federado;
        public $num_licencia;

        public $created_at;
        public $updated_at;

        public $role_id;
    

        public function __construct(
            $id = null,

            // Credenciales
            $nombre = null,
            $email = null,
            $password = null,
            $avatar = null,

            // Datos Personales
            $apellidos = null,
            $sexo = null,
            $fecha_nac = null,
            $dni = null,

            // Datos Contacto
            $tlf = null,
            $tlf_emg = null,
            $direccion = null,
            $poblacion = null,
            $provincia = null,
            $cp = null,
            $pais = null,

            // Datos Deportivos
            $club = null,
            $talla = null,
            $es_federado = null,
            $num_licencia = null,

            $created_at = null,
            $updated_at = null,

            $role_id = null
        ) {
            $this->id = $id;

            // Credenciales
            $this->nombre = $nombre;
            $this->email = $email;
            $this->password = $password;
            $this->avatar = $avatar;

            // Datos Personales
            $this->apellidos = $apellidos;
            $this->sexo = $sexo;
            $this->fecha_nac = $fecha_nac;
            $this->dni = $dni;

            // Datos Contacto
            $this->tlf = $tlf;
            $this->tlf_emg = $tlf_emg;
            $this->direccion = $direccion;
            $this->poblacion = $poblacion;
            $this->provincia = $provincia;
            $this->cp = $cp;
            $this->pais = $pais;

            // Datos Deportivos
            $this->club = $club;
            $this->talla = $talla;
            $this->es_federado = $es_federado;
            $this->num_licencia = $num_licencia;

            $this->created_at = $created_at;
            $this->updated_at = $updated_at;

            $this->role_id = $role_id;
        }
    }
?>