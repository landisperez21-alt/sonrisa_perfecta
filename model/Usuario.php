<?php
class Usuario
{
    public function __construct(private PDO $db)
    {
    }

    public function porCorreo(string $email): array|false
    {
        $consulta = $this->db->prepare('SELECT * FROM usuarios WHERE email = ?');
        $consulta->execute([$email]);
        return $consulta->fetch();
    }

    // El registro público SIEMPRE crea usuarios con rol 'recepcion'.
    public function crear(string $nombre, string $email, string $password): void
    {
        $consulta = $this->db->prepare(
            "INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (?, ?, ?, 'recepcion')"
        );
        $consulta->execute([$nombre, $email, password_hash($password, PASSWORD_DEFAULT)]);
    }
}
