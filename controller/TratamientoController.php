<?php
require_once __DIR__ . '/CrudController.php';
require_once __DIR__ . '/../model/Tratamiento.php';

class TratamientoController extends CrudController
{
    public string $titulo = 'Tratamientos';
    public string $resumen = 'Procedimientos con materiales, indicaciones y costo.';
    public string $url = 'tratamientos.php';

    public array $campos = [
        'diagnostico_id' => 'diagnosticos',
        'procedimiento' => 'text',
        'materiales' => 'text',
        'indicaciones' => 'textarea',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'costo' => 'number',
    ];

    public array $obligatorios = ['diagnostico_id', 'procedimiento', 'costo'];

    public array $columnasLista = ['paciente', 'procedimiento', 'materiales', 'fecha_inicio', 'fecha_fin', 'costo'];

    protected function crearModelo(): BaseModel
    {
        return new Tratamiento(db());
    }

    protected function prepararDatos(array $datos): array
    {
        if ($datos['fecha_inicio'] && $datos['fecha_fin'] && $datos['fecha_fin'] < $datos['fecha_inicio']) {
            throw new RuntimeException('La fecha de fin no puede ser anterior a la fecha de inicio.');
        }
        return $datos;
    }
}
