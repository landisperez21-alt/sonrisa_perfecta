<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/Reporte.php';

// Los reportes no son un CRUD: solo leen datos y los resumen.
class ReporteController
{
    private Reporte $modelo;

    public function __construct()
    {
        $this->modelo = new Reporte(db());
    }

    // Valida una fecha de la URL; si viene mal, usa la de reserva.
    private function fechaValida(string $valor, string $reserva): string
    {
        $fecha = DateTime::createFromFormat('!Y-m-d', $valor);
        return ($fecha && $fecha->format('Y-m-d') === $valor) ? $valor : $reserva;
    }

    public function generar(array $entrada): array
    {
        $desde = $this->fechaValida((string) ($entrada['desde'] ?? ''), date('Y-m-01'));
        $hasta = $this->fechaValida((string) ($entrada['hasta'] ?? ''), date('Y-m-t'));

        if ($desde > $hasta) {
            [$desde, $hasta] = [$hasta, $desde];
        }

        $porMetodo = $this->modelo->ingresosPorMetodo($desde, $hasta);

        return [
            'desde' => $desde,
            'hasta' => $hasta,
            'atenciones' => $this->modelo->atenciones($desde, $hasta),
            'pendientes' => $this->modelo->pendientes($desde, $hasta),
            'estados' => $this->modelo->citasPorEstado($desde, $hasta),
            'porMetodo' => $porMetodo,
            'ingresos' => array_sum(array_map(fn($fila) => (float) $fila['total'], $porMetodo)),
            'ranking' => $this->modelo->serviciosMasSolicitados($desde, $hasta),
        ];
    }

    public function resumenHome(): array
    {
        return $this->modelo->resumenHome();
    }

    public function proximasCitas(): array
    {
        return $this->modelo->proximasCitas(5);
    }
}
