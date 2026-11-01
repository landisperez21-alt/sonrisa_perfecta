<?php
require_once __DIR__ . '/BaseModel.php';

class Cita extends BaseModel
{
    protected string $tabla = 'citas';
    protected array $columnas = ['paciente_id', 'odontologo_id', 'servicio_id', 'fecha', 'hora', 'estado', 'motivo'];

    protected function sqlListado(): string
    {
        return "SELECT c.id, c.fecha, TIME_FORMAT(c.hora, '%H:%i') AS hora, c.estado, c.motivo,
                       p.nombre AS paciente, o.nombre AS odontologo, s.nombre AS servicio
                FROM citas c
                JOIN pacientes p   ON p.id = c.paciente_id
                JOIN odontologos o ON o.id = c.odontologo_id
                JOIN servicios s   ON s.id = c.servicio_id
                ORDER BY c.fecha DESC, c.hora DESC
                LIMIT 300";
    }
}
