<?php
require_once __DIR__ . '/../controller/OdontologoController.php';

exigirSesion();
$controller = new OdontologoController();
$error = $controller->procesar();

require __DIR__ . '/crud/pagina.php';
