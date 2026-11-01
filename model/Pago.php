<?php
require_once __DIR__ . '/BaseModel.php';

class Pago extends BaseModel
{
    protected string $tabla = 'pagos';
    protected array $columnas = ['cita_id', 'monto', 'metodo', 'estado', 'referencia', 'fecha_pago'];

    protected function sqlListado(): string
    {
        return "SELECT pg.id, pg.monto, pg.metodo, pg.estado, pg.referencia, pg.fecha_pago,
                       p.nombre AS paciente
                FROM pagos pg
                JOIN citas c     ON c.id = pg.cita_id
                JOIN pacientes p ON p.id = c.paciente_id
                ORDER BY pg.id DESC
                LIMIT 300";
    }
}
