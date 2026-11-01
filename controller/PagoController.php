<?php
require_once __DIR__ . '/CrudController.php';
require_once __DIR__ . '/../model/Pago.php';

class PagoController extends CrudController
{
    public string $titulo = 'Control de pagos';
    public string $resumen = 'Efectivo, tarjeta de crédito, tarjeta de débito o Yape.';
    public string $url = 'pagos.php';

    public array $campos = [
        'cita_id' => 'citas',
        'monto' => 'number',
        'metodo' => 'metodo_pago',
        'estado' => 'estado_pago',
        'referencia' => 'text',
        'fecha_pago' => 'datetime-local',
    ];

    public array $obligatorios = ['cita_id', 'monto', 'metodo', 'estado'];

    public array $columnasLista = ['paciente', 'monto', 'metodo', 'estado', 'referencia', 'fecha_pago'];

    protected function crearModelo(): BaseModel
    {
        return new Pago(db());
    }

    protected function prepararDatos(array $datos): array
    {
        if ((float) $datos['monto'] <= 0) {
            throw new RuntimeException('El monto debe ser mayor que cero.');
        }
        // Un pago "pagado" sin fecha toma la fecha y hora actuales.
        if ($datos['estado'] === 'pagado' && !$datos['fecha_pago']) {
            $datos['fecha_pago'] = date('Y-m-d H:i:s');
        }
        return $datos;
    }
}
