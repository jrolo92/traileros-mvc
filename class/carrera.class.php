<?php

/*
    clase: class_carrera
    descripción: clase para gestionar los eventos de carreras de montaña (trail)
*/

class class_carrera {
    public $id;
    public $nombre;
    public $fecha;
    public $ubicacion;
    public $distancia;
    public $desnivel;
    public $dificultad;
    public $descripcion;
    public $imagenUrl;
    public $organizador_id;

    public function __construct(
        $id = null,
        $nombre = null,
        $fecha = null,
        $ubicación = null,
        $distancia = null,
        $desnivel = null,
        $dificultad = null,
        $descripcion = null,
        $imagenUrl = null,
        $organizador_id = null
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->fecha = $fecha;
        $this->ubicacion = $ubicación;
        $this->distancia = $distancia;
        $this->desnivel = $desnivel;
        $this->dificultad = $dificultad;
        $this->descripcion = $descripcion;
        $this->imagenUrl = $imagenUrl;
        $this->organizador_id = $organizador_id;
    }
}