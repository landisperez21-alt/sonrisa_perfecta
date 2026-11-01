<?php
require_once __DIR__ . '/../controller/ServicioController.php';

exigirSesion();
$controller = new ServicioController();
$error = $controller->procesar();

require __DIR__ . '/crud/pagina.php';
