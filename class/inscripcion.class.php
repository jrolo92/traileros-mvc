<?php

/*
    class: class_inscripcion
    descripción: clase para gestionar inscripciones a eventos determinados
*/

class class_inscripcion{
    public $id;
    public $user_id;
    public $evento_id;
    public $modalidad_id;
    public $categoria_id;
    public $fecha_inscripcion;
    public $dorsal;
    public $id_pago;
    public $metodo_pago;
    public $estado_pago;
    public $precio_final;

    public function __construct(
        $id = null,
        $user_id = null,
        $evento_id = null,
        $modalidad_id = null,
        $categoria_id = null,
        $fecha_inscripcion = null,
        $dorsal = null,
        $id_pago = null,
        $metodo_pago = null,
        $estado_pago = 'pendiente',
        $precio_final = 0.00,
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->evento_id = $evento_id;
        $this->modalidad_id = $modalidad_id;
        $this->categoria_id = $categoria_id;
        $this->fecha_inscripcion = $fecha_inscripcion;
        $this->dorsal = $dorsal;
        $this->id_pago = $id_pago;
        $this->metodo_pago = $metodo_pago;
        $this->estado_pago = $estado_pago;
        $this->precio_final = $precio_final;
    }

}