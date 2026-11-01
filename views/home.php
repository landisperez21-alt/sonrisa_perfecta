<?php
require_once __DIR__ . '/../controller/ReporteController.php';
require_once __DIR__ . '/layout.php';

exigirSesion();
$reportes = new ReporteController();
$resumen = $reportes->resumenHome();
$proximas = $reportes->proximasCitas();

// Saludo según la hora y fecha en español.
$hora = (int) date('G');
$saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
$primerNombre = explode(' ', trim($_SESSION['nombre'] ?? ''))[0];
$dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
$meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
$fechaHoy = ucfirst($dias[(int) date('w')]) . ', ' . date('j') . ' de ' . $meses[(int) date('n') - 1] . ' de ' . date('Y');

layoutStart('Panel de control', $saludo . ', ' . $primerNombre . ' · ' . $fechaHoy);
?>

<div class="stats">
    <div class="stat">
        <span class="stat-num"><?= $resumen['pacientes'] ?></span>
        <span class="stat-label">Pacientes registrados</span>
    </div>
    <div class="stat">
        <span class="stat-num"><?= $resumen['citas_hoy'] ?></span>
        <span class="stat-label">Citas para hoy</span>
    </div>
    <div class="stat">
        <span class="stat-num"><?= $resumen['pendientes'] ?></span>
        <span class="stat-label">Citas pendientes</span>
    </div>
    <div class="stat stat-accent">
        <span class="stat-num"><?= e(dinero($resumen['ingresos_mes'])) ?></span>
        <span class="stat-label">Ingresos del mes</span>
    </div>
</div>

<h2 class="section-title">Módulos de la clínica</h2>
<div class="cards">
    <?php foreach (modulos() as $id => $modulo): ?>
        <a class="card" href="<?= e($modulo['url']) ?>">
            <img loading="lazy" src="../imagenes/cards/<?= e($id) ?>.jpg" alt="Ilustración de <?= e($modulo['nombre']) ?>">
            <div class="card-body">
                <h3><?= e($modulo['nombre']) ?></h3>
                <p><?= e($modulo['texto']) ?></p>
                <span class="card-link">Administrar →</span>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<section class="panel">
    <div class="panel-head">
        <h2>Próximas citas</h2>
        <a class="btn secondary small" href="citas.php">Ver agenda</a>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Fecha</th><th>Hora</th><th>Paciente</th><th>Servicio</th><th>Odontólogo</th><th>Estado</th></tr></thead>
            <tbody>
                <?php foreach ($proximas as $fila): ?>
                    <tr>
                        <td data-label="Fecha"><?= e(fechaCorta($fila['fecha'])) ?></td>
                        <td data-label="Hora"><?= e($fila['hora']) ?></td>
                        <td data-label="Paciente"><?= e($fila['paciente']) ?></td>
                        <td data-label="Servicio"><?= e($fila['servicio']) ?></td>
                        <td data-label="Odontólogo"><?= e($fila['odontologo']) ?></td>
                        <td data-label="Estado"><?= formatearCelda('estado', $fila['estado']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$proximas): ?>
                    <tr><td class="vacio" colspan="6">No hay citas próximas. Programa la primera desde el módulo Citas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php layoutEnd(); ?>
