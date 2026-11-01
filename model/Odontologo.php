<?php
require_once __DIR__ . '/BaseModel.php';

class Odontologo extends BaseModel
{
    protected string $tabla = 'odontologos';
    protected array $columnas = ['nombre', 'colegiatura', 'especialidad', 'telefono', 'email'];
}
