<?php
// Página común de los módulos CRUD. Espera la variable $controller y opcionalmente $error.
require_once __DIR__ . '/../layout.php';

$error = $error ?? '';
$edit = false;
if (isset($_GET['editar'])) {
    $edit = $controller->modelo->obtener((int) $_GET['editar']);
}
$filas = $controller->modelo->listar();

layoutStart($controller->titulo, $controller->resumen);
?>

<?php if ($error !== ''): ?>
    <p class="error" role="alert"><?= e($error) ?></p>
<?php endif; ?>

<section class="panel">
    <div class="panel-head">
        <h2><?= $edit ? 'Editar registro' : 'Nuevo registro' ?></h2>
        <?php if ($edit): ?>
            <a class="btn secondary small" href="<?= e($controller->url) ?>">Cancelar edición</a>
        <?php endif; ?>
    </div>
    <?php require __DIR__ . '/form.php'; ?>
</section>

<section class="panel">
    <div class="panel-head">
        <h2>Registros <span class="count"><?= count($filas) ?></span></h2>
        <label class="search">
            <?= icono('search', 16) ?>
            <input type="search" placeholder="Buscar en la tabla…" data-filtro-tabla="tabla-datos" aria-label="Buscar">
        </label>
    </div>
    <?php require __DIR__ . '/lista.php'; ?>
</section>

<?php layoutEnd(); ?>
