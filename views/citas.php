<?php
require_once __DIR__ . '/../controller/CitaController.php';

exigirSesion();
$controller = new CitaController();
$error = $controller->procesar();

require __DIR__ . '/crud/pagina.php';
