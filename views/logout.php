<?php
require_once __DIR__ . '/../controller/BaseController.php';

// Cierre de sesión seguro: vacía los datos, borra la cookie y destruye la sesión.
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();
redirigir('login.php');
