<?php
require_once __DIR__ . '/CrudController.php';
require_once __DIR__ . '/../model/Paciente.php';

class PacienteController extends CrudController
{
    public string $titulo = 'Pacientes';
    public string $resumen = 'Registra y administra las fichas de tus pacientes.';
    public string $url = 'pacientes.php';

    public array $campos = [
        'nombre' => 'text',
        'documento' => 'doc',
        'fecha_nacimiento' => 'date',
        'telefono' => 'tel',
        'email' => 'email',
        'direccion' => 'text',
    ];

    public array $obligatorios = ['nombre', 'documento'];

    public array $columnasLista = ['nombre', 'documento', 'fecha_nacimiento', 'telefono', 'email', 'direccion'];

    protected function crearModelo(): BaseModel
    {
        return new Paciente(db());
    }
}
