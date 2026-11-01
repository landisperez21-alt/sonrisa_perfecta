<?php
require_once __DIR__ . '/BaseModel.php';

class Servicio extends BaseModel
{
    protected string $tabla = 'servicios';
    protected array $columnas = ['nombre', 'descripcion', 'precio', 'duracion_min'];

    protected function sqlListado(): string
    {
        return 'SELECT * FROM servicios ORDER BY nombre ASC LIMIT 300';
    }
}
