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
    public $cupo_maximo;
    public $precio;
    public $edad_minima;
    public $edad_maxima;
    public $imagen;
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
        $cupo_maximo = null,
        $precio = null,
        $edad_minima = null,
        $edad_maxima = null,
        $imagen = null,
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
        $this->cupo_maximo = $cupo_maximo;
        $this->precio = $precio;
        $this->edad_minima = $edad_minima;
        $this->edad_maxima = $edad_maxima;
        $this->imagen = $imagen;
        $this->organizador_id = $organizador_id;
    }
}