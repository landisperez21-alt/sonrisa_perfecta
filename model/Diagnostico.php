<?php
require_once __DIR__ . '/BaseModel.php';

class Diagnostico extends BaseModel
{
    protected string $tabla = 'diagnosticos';
    protected array $columnas = ['cita_id', 'descripcion', 'observaciones', 'fecha'];

    protected function sqlListado(): string
    {
        return "SELECT d.id, d.descripcion, d.observaciones, d.fecha,
                       p.nombre AS paciente, o.nombre AS odontologo
                FROM diagnosticos d
                JOIN citas c       ON c.id = d.cita_id
                JOIN pacientes p   ON p.id = c.paciente_id
                JOIN odontologos o ON o.id = c.odontologo_id
                ORDER BY d.id DESC
                LIMIT 300";
    }
}
