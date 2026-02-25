<?php

echo '<h3>ERROR DE BASE DE DATOS</h3>' . '<br>';
echo 'Mensaje: ' . $e->getMessage() . '<br>';
echo 'Código de error: ' . $e->getCode() . '<br>';
echo 'Fichero: ' . $e->getFile() . '<br>';
echo 'Línea: ' . $e->getLine() . '<br>';
echo 'Traza del error: ' . $e->getTraceAsString() . '<br>';
