<?php
require_once __DIR__ . '/../controller/PacienteController.php';

exigirSesion();
$controller = new PacienteController();
$error = $controller->procesar();

require __DIR__ . '/crud/pagina.php';
