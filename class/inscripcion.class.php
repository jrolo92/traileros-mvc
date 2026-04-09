<?php

/*
    class: class_inscripcion
    descripción: clase para gestionar inscripciones a eventos determinados
*/

class class_inscripcion{
    public $user_id;
    public $evento_id;
    public $categoria_id;
    public $fecha_inscripcion;
    public $dorsal;
    public $metodo_pago;
    public $estado_pago;
    public $precio_final;

    public function __construct(
        $user_id = null,
        $evento_id = null,
        $categoria_id = null,
        $dorsal = null,
        $metodo_pago = null,
        $estado_pago = 'pendiente',
        $precio_final = 0.00,
        $fecha_inscripcion = null
    ) {
        $this->user_id = $user_id;
        $this->evento_id = $evento_id;
        $this->categoria_id = $categoria_id;
        $this->dorsal = $dorsal;
        $this->metodo_pago = $metodo_pago;
        $this->estado_pago = $estado_pago;
        $this->precio_final = $precio_final;
        $this->fecha_inscripcion = $fecha_inscripcion;
    }

}