<?php
require_once __DIR__ . '/../controller/DiagnosticoController.php';

exigirSesion();
$controller = new DiagnosticoController();
$error = $controller->procesar();

require __DIR__ . '/crud/pagina.php';
