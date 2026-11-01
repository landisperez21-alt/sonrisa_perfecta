<?php
require_once __DIR__ . '/../controller/PagoController.php';

exigirSesion();
$controller = new PagoController();
$error = $controller->procesar();

require __DIR__ . '/crud/pagina.php';
