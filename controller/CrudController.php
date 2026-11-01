<?php
require_once __DIR__ . '/BaseController.php';

// Controlador genérico: crea, edita y elimina. Cada módulo solo define sus campos.
abstract class CrudController
{
    public BaseModel $modelo;
    public string $titulo = '';
    public string $resumen = '';
    public string $url = '';
    public array $campos = [];         // campo => tipo (para el formulario y la validación)
    public array $obligatorios = [];   // campos que no pueden ir vacíos
    public array $columnasLista = [];  // columnas que se muestran en la tabla

    abstract protected function crearModelo(): BaseModel;

    public function __construct()
    {
        $this->modelo = $this->crearModelo();
    }

    // Los módulos pueden ajustar o validar datos extra antes de guardar.
    protected function prepararDatos(array $datos): array
    {
        return $datos;
    }

    public function procesar(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return '';
        }

        try {
            validarCsrf();
            $id = filter_var(
                $_POST['id'] ?? 0,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            ) ?: 0;

            if (($_POST['accion'] ?? '') === 'eliminar') {
                $this->modelo->eliminar($id);
                notificar('Registro eliminado correctamente.');
            } else {
                $datos = validarDatos($this->campos, $_POST, $this->obligatorios);
                $this->modelo->guardar($this->prepararDatos($datos), $id);
                notificar($id > 0 ? 'Cambios guardados correctamente.' : 'Registro creado correctamente.');
            }

            redirigir($this->url);
        } catch (PDOException $ex) {
            error_log($ex->getMessage());
            return 'No se pudo completar la operación. Verifica que no haya duplicados (documento, colegiatura o un horario ya ocupado) '
                . 'y que el registro no esté siendo usado en otro módulo.';
        } catch (Throwable $ex) {
            return $ex->getMessage();
        }
    }
}
