<?php
require_once __DIR__ . '/CrudController.php';
require_once __DIR__ . '/../model/Odontologo.php';

class OdontologoController extends CrudController
{
    public string $titulo = 'Odontólogos';
    public string $resumen = 'Administra al equipo clínico según su especialidad.';
    public string $url = 'odontologos.php';

    public array $campos = [
        'nombre' => 'text',
        'colegiatura' => 'doc',
        'especialidad' => 'text',
        'telefono' => 'tel',
        'email' => 'email',
    ];

    public array $obligatorios = ['nombre', 'colegiatura'];

    public array $columnasLista = ['nombre', 'colegiatura', 'especialidad', 'telefono', 'email'];

    protected function crearModelo(): BaseModel
    {
        return new Odontologo(db());
    }
}
