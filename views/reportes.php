<?php
require_once __DIR__ . '/../controller/ReporteController.php';
require_once __DIR__ . '/layout.php';

exigirSesion();
$reportes = new ReporteController();
$r = $reportes->generar($_GET);

layoutStart('Reportes', 'Atenciones realizadas, ingresos obtenidos y citas pendientes por período.');
?>

<section class="panel no-print">
    <form method="get" class="filtros">
        <label class="field">
            <span class="field-label">Desde</span>
            <input type="date" name="desde" value="<?= e($r['desde']) ?>" required>
        </label>
        <label class="field">
            <span class="field-label">Hasta</span>
            <input type="date" name="hasta" value="<?= e($r['hasta']) ?>" required>
        </label>
        <div class="actions">
            <button type="submit">Generar reporte</button>
            <button type="button" class="secondary" data-imprimir><?= icono('printer', 16) ?> Imprimir</button>
        </div>
    </form>
</section>

<p class="periodo">Período: <strong><?= e(fechaCorta($r['desde'])) ?></strong> al <strong><?= e(fechaCorta($r['hasta'])) ?></strong></p>

<div class="stats">
    <div class="stat">
        <span class="stat-num"><?= count($r['atenciones']) ?></span>
        <span class="stat-label">Atenciones realizadas</span>
    </div>
    <div class="stat stat-accent">
        <span class="stat-num"><?= e(dinero($r['ingresos'])) ?></span>
        <span class="stat-label">Ingresos obtenidos</span>
    </div>
    <div class="stat">
        <span class="stat-num"><?= count($r['pendientes']) ?></span>
        <span class="stat-label">Citas pendientes</span>
    </div>
    <div class="stat">
        <span class="stat-num"><?= (int) $r['estados']['cancelada'] ?></span>
        <span class="stat-label">Citas canceladas</span>
    </div>
</div>

<div class="two-col">
    <section class="panel">
        <h2>Ingresos por método de pago</h2>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Método</th><th>Pagos</th><th>Total</th></tr></thead>
                <tbody>
                    <?php foreach ($r['porMetodo'] as $fila): ?>
                        <tr>
                            <td data-label="Método"><?= e(nombreMetodo($fila['metodo'])) ?></td>
                            <td data-label="Pagos"><?= e($fila['cantidad']) ?></td>
                            <td data-label="Total"><?= e(dinero($fila['total'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$r['porMetodo']): ?>
                        <tr><td class="vacio" colspan="3">Sin pagos registrados en este período.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <h2>Servicios más solicitados</h2>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Servicio</th><th>Citas</th></tr></thead>
                <tbody>
                    <?php foreach ($r['ranking'] as $fila): ?>
                        <tr>
                            <td data-label="Servicio"><?= e($fila['nombre']) ?></td>
                            <td data-label="Citas"><?= e($fila['cantidad']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$r['ranking']): ?>
                        <tr><td class="vacio" colspan="2">Sin citas en este período.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<section class="panel">
    <h2>Atenciones realizadas <span class="count"><?= count($r['atenciones']) ?></span></h2>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Fecha</th><th>Hora</th><th>Paciente</th><th>Odontólogo</th><th>Servicio</th></tr></thead>
            <tbody>
                <?php foreach ($r['atenciones'] as $fila): ?>
                    <tr>
                        <td data-label="Fecha"><?= e(fechaCorta($fila['fecha'])) ?></td>
                        <td data-label="Hora"><?= e($fila['hora']) ?></td>
                        <td data-label="Paciente"><?= e($fila['paciente']) ?></td>
                        <td data-label="Odontólogo"><?= e($fila['odontologo']) ?></td>
                        <td data-label="Servicio"><?= e($fila['servicio']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$r['atenciones']): ?>
                    <tr><td class="vacio" colspan="5">No hay atenciones realizadas en este período.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <h2>Citas pendientes <span class="count"><?= count($r['pendientes']) ?></span></h2>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Fecha</th><th>Hora</th><th>Paciente</th><th>Teléfono</th><th>Odontólogo</th><th>Servicio</th></tr></thead>
            <tbody>
                <?php foreach ($r['pendientes'] as $fila): ?>
                    <tr>
                        <td data-label="Fecha"><?= e(fechaCorta($fila['fecha'])) ?></td>
                        <td data-label="Hora"><?= e($fila['hora']) ?></td>
                        <td data-label="Paciente"><?= e($fila['paciente']) ?></td>
                        <td data-label="Teléfono"><?= e($fila['telefono'] ?: '—') ?></td>
                        <td data-label="Odontólogo"><?= e($fila['odontologo']) ?></td>
                        <td data-label="Servicio"><?= e($fila['servicio']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$r['pendientes']): ?>
                    <tr><td class="vacio" colspan="6">No hay citas pendientes en este período.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php layoutEnd(); ?>
