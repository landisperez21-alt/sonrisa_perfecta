<?php
require_once __DIR__ . '/CrudController.php';
require_once __DIR__ . '/../model/Cita.php';

class CitaController extends CrudController
{
    public string $titulo = 'Citas odontológicas';
    public string $resumen = 'Programa la agenda indicando fecha, hora y estado.';
    public string $url = 'citas.php';

    public array $campos = [
        'paciente_id' => 'pacientes',
        'odontologo_id' => 'odontologos',
        'servicio_id' => 'servicios',
        'fecha' => 'date',
        'hora' => 'time',
        'estado' => 'estado_cita',
        'motivo' => 'textarea',
    ];

    public array $obligatorios = ['paciente_id', 'odontologo_id', 'servicio_id', 'fecha', 'hora', 'estado'];

    public array $columnasLista = ['fecha', 'hora', 'paciente', 'odontologo', 'servicio', 'estado', 'motivo'];

    protected function crearModelo(): BaseModel
    {
        return new Cita(db());
    }
}
