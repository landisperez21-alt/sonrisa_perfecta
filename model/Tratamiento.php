<?php
require_once __DIR__ . '/BaseModel.php';

class Tratamiento extends BaseModel
{
    protected string $tabla = 'tratamientos';
    protected array $columnas = ['diagnostico_id', 'procedimiento', 'materiales', 'indicaciones', 'fecha_inicio', 'fecha_fin', 'costo'];

    protected function sqlListado(): string
    {
        return "SELECT t.id, t.procedimiento, t.materiales, t.indicaciones,
                       t.fecha_inicio, t.fecha_fin, t.costo,
                       p.nombre AS paciente
                FROM tratamientos t
                JOIN diagnosticos d ON d.id = t.diagnostico_id
                JOIN citas c        ON c.id = d.cita_id
                JOIN pacientes p    ON p.id = c.paciente_id
                ORDER BY t.id DESC
                LIMIT 300";
    }
}
