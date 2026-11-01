<?php
require_once __DIR__ . '/../controller/TratamientoController.php';

exigirSesion();
$controller = new TratamientoController();
$error = $controller->procesar();

require __DIR__ . '/crud/pagina.php';
