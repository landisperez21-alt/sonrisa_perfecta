<?php
require_once __DIR__ . '/CrudController.php';
require_once __DIR__ . '/../model/Servicio.php';

class ServicioController extends CrudController
{
    public string $titulo = 'Servicios odontológicos';
    public string $resumen = 'Catálogo de tratamientos con su precio y duración.';
    public string $url = 'servicios.php';

    public array $campos = [
        'nombre' => 'text',
        'descripcion' => 'textarea',
        'precio' => 'number',
        'duracion_min' => 'minutos',
    ];

    public array $obligatorios = ['nombre', 'precio', 'duracion_min'];

    public array $columnasLista = ['nombre', 'descripcion', 'precio', 'duracion_min'];

    protected function crearModelo(): BaseModel
    {
        return new Servicio(db());
    }
}
