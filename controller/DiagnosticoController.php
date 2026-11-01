<?php
require_once __DIR__ . '/CrudController.php';
require_once __DIR__ . '/../model/Diagnostico.php';

class DiagnosticoController extends CrudController
{
    public string $titulo = 'Diagnósticos';
    public string $resumen = 'Registro clínico de cada atención realizada.';
    public string $url = 'diagnosticos.php';

    public array $campos = [
        'cita_id' => 'citas',
        'descripcion' => 'textarea',
        'observaciones' => 'textarea',
        'fecha' => 'date',
    ];

    public array $obligatorios = ['cita_id', 'descripcion', 'fecha'];

    public array $columnasLista = ['fecha', 'paciente', 'odontologo', 'descripcion', 'observaciones'];

    protected function crearModelo(): BaseModel
    {
        return new Diagnostico(db());
    }
}
