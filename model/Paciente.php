<?php
require_once __DIR__ . '/BaseModel.php';

class Paciente extends BaseModel
{
    protected string $tabla = 'pacientes';
    protected array $columnas = ['nombre', 'documento', 'fecha_nacimiento', 'telefono', 'email', 'direccion'];
}
