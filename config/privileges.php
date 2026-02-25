<?php

    /*
        Definimos los privilegios de la aplicación

        Recordamos los perfiles:
        - 1: Administrador
        - 2: Organizador
        - 3: Corredor

        Recordamos los controladores o recursos:
        - 1: Carrera
        - 2: User

        Los privilegios son:
        - 1: render
        - 2: new
        - 3: edit
        - 4: delete
        - 5: show
        - 6: order
        - 7: search

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

    $GLOBALS['user']['render'] = [1];
    $GLOBALS['user']['new'] = [1];
    $GLOBALS['user']['create'] = [1];
    $GLOBALS['user']['edit'] = [1];
    $GLOBALS['user']['update'] = [1];
    $GLOBALS['user']['delete'] = [1];
    $GLOBALS['user']['show'] = [1];
    $GLOBALS['user']['search'] = [1];
    $GLOBALS['user']['order'] = [1];
    
    

?>