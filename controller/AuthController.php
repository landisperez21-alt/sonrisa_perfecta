<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/Usuario.php';

class AuthController
{
    private Usuario $usuarios;

    public function __construct()
    {
        $this->usuarios = new Usuario(db());
    }

    public function ingresar(string $email, string $password): bool
    {
        $usuario = $this->usuarios->porCorreo($email);

        if (!$usuario || !$usuario['activo'] || !password_verify($password, $usuario['password_hash'])) {
            return false;
        }

        // Cambia el identificador de sesión al entrar (evita "session fixation").
        session_regenerate_id(true);
        $_SESSION['uid'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];
        return true;
    }

    public function registrar(string $nombre, string $email, string $password, string $repetir): void
    {
        if ($nombre === '' || mb_strlen($nombre) > 120) {
            throw new RuntimeException('Indica tu nombre (máximo 120 caracteres).');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Correo electrónico inválido.');
        }
        if (strlen($password) < 10) {
            throw new RuntimeException('La contraseña debe tener al menos 10 caracteres.');
        }
        if ($password !== $repetir) {
            throw new RuntimeException('Las contraseñas no coinciden.');
        }
        if ($this->usuarios->porCorreo($email)) {
            throw new RuntimeException('Ese correo ya está registrado. Inicia sesión.');
        }

        $this->usuarios->crear($nombre, $email, $password);
    }
}
