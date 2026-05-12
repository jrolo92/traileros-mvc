<?php

    /*
        Definimos los privilegios de la aplicación

        Recordamos los perfiles:
        - 1: Administrador
        - 2: Organizador
        - 3: Corredor

        Los perfiles se asignarán mediante un array asociativo, 
        donde la clave principal se corresponde con el controlador 
        la clave secundaria con el método.

        $GLOBALS['controlador']['método'] = [1, 2, 3];
        $GLOBALS['carrera']['edit'] = [1, 2];

        Se asignan los perfiles que tienen acceso a un determinado método del controlador carrera.

    */ 
    $GLOBALS['carrera']['render'] = [1, 2, 3];
    $GLOBALS['carrera']['new'] = [1, 2];
    $GLOBALS['carrera']['create'] = [1, 2];
    $GLOBALS['carrera']['edit'] = [1, 2];
    $GLOBALS['carrera']['update'] = [1, 2];
    $GLOBALS['carrera']['delete'] = [1];
    $GLOBALS['carrera']['show'] = [1, 2, 3];
    $GLOBALS['carrera']['search'] = [1, 2, 3];
    $GLOBALS['carrera']['order'] = [1, 2, 3];
    $GLOBALS['carrera']['inscribir'] = [1, 2, 3];
    $GLOBALS['carrera']['gestion'] = [1, 2];

    $GLOBALS['user']['render'] = [1];
    $GLOBALS['user']['new'] = [1];
    $GLOBALS['user']['create'] = [1];
    $GLOBALS['user']['edit'] = [1];
    $GLOBALS['user']['update'] = [1];
    $GLOBALS['user']['delete'] = [1];
    $GLOBALS['user']['show'] = [1];
    $GLOBALS['user']['search'] = [1];
    $GLOBALS['user']['order'] = [1];

    $GLOBALS['account']['render'] = [1, 2, 3];
    $GLOBALS['account']['edit'] = [1, 2, 3];
    $GLOBALS['account']['update'] = [1, 2, 3];
    $GLOBALS['account']['password'] = [1, 2, 3];
    $GLOBALS['account']['update_password'] = [1, 2, 3];
    $GLOBALS['account']['delete_confirmed'] = [1, 2, 3];
    $GLOBALS['account']['show'] = [1, 2, 3];
    $GLOBALS['account']['search'] = [1, 2, 3];
    $GLOBALS['account']['order'] = [1, 2, 3];

    $GLOBALS['inscripcion']['render'] = [1, 2, 3];
    $GLOBALS['inscripcion']['new'] = [1, 2, 3];
    $GLOBALS['inscripcion']['create'] = [1, 2, 3];
    $GLOBALS['inscripcion']['edit'] = [1, 2];
    $GLOBALS['inscripcion']['update'] = [1, 2];
    $GLOBALS['inscripcion']['delete'] = [1];
    $GLOBALS['inscripcion']['cancel'] = [1, 2];
    $GLOBALS['inscripcion']['show'] = [1, 2, 3];
    $GLOBALS['inscripcion']['search'] = [1, 2, 3];
    $GLOBALS['inscripcion']['order'] = [1, 2, 3];
    $GLOBALS['inscripcion']['export'] = [1, 2];
    $GLOBALS['inscripcion']['participantes'] = [1, 2];

    $GLOBALS['resultado']['render'] = [1, 2, 3];
    $GLOBALS['resultado']['pre_import'] = [1, 2];
    
?>