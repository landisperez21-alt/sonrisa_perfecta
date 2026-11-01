<?php
// Configuración de MySQL. Este es el único archivo dentro de config.
class Database
{
    private string $host = 'localhost';
    private string $name = 'odontologia_db';
    private string $user = 'root';
    private string $password = '';

    public function conectar(): PDO
    {
        try {
            return new PDO(
                "mysql:host={$this->host};dbname={$this->name};charset=utf8mb4",
                $this->user,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $ex) {
            // El detalle técnico va al registro del servidor; al usuario solo un mensaje claro.
            error_log($ex->getMessage());
            throw new RuntimeException(
                'No se pudo conectar a la base de datos. Revisa que MySQL esté encendido en XAMPP y que odontologia_db esté importada.'
            );
        }
    }
}
