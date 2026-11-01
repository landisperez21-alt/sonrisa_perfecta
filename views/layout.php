<?php
require_once __DIR__ . '/../controller/BaseController.php';

// Cabecera común de todas las páginas internas: encabezado de DOS niveles + navegación + menú derecho.
function layoutStart(string $titulo, string $subtitulo = ''): void
{
    $modulos = modulos();
    $actual = basename($_SERVER['SCRIPT_NAME']);
    $nombre = $_SESSION['nombre'] ?? '';
    $inicial = mb_strtoupper(mb_substr($nombre, 0, 1, 'UTF-8'), 'UTF-8');
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> | <?= e(MARCA) ?></title>
    <link rel="icon" type="image/svg+xml" href="../imagenes/logo.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assetes/css/odonto.css">
</head>
<body>
<header class="site-header">
    <!-- NIVEL 1: marca + usuario -->
    <div class="header-top">
        <a class="brand" href="home.php">
            <img src="../imagenes/logo.svg" alt="" width="40" height="40">
            <span class="brand-text">
                <strong><?= e(MARCA) ?></strong>
                <small>Clínica odontológica</small>
            </span>
        </a>
        <div class="header-user">
            <span class="avatar" aria-hidden="true"><?= e($inicial) ?></span>
            <span class="user-name"><?= e($nombre) ?></span>
            <a class="btn-ghost" href="logout.php"><?= icono('logout', 16) ?> Salir</a>
        </div>
    </div>

    <!-- NIVEL 2: barra de navegación horizontal -->
    <nav class="nav" aria-label="Menú principal">
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav-links">
            <?= icono('menu', 20) ?> Menú
        </button>
        <div class="nav-links" id="nav-links">
            <a href="home.php" class="<?= $actual === 'home.php' ? 'activo' : '' ?>">Inicio</a>
            <?php foreach ($modulos as $modulo): ?>
                <a href="<?= e($modulo['url']) ?>" class="<?= $actual === $modulo['url'] ? 'activo' : '' ?>">
                    <?= e($modulo['nombre']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>
</header>

<div class="shell">
    <main class="main">
        <div class="page-head">
            <h1><?= e($titulo) ?></h1>
            <?php if ($subtitulo !== ''): ?>
                <p><?= e($subtitulo) ?></p>
            <?php endif; ?>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <p class="notice" role="status"><?= e($_SESSION['flash']) ?></p>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
    <?php
}

function layoutEnd(): void
{
    $modulos = modulos();
    $actual = basename($_SERVER['SCRIPT_NAME']);
    ?>
    </main>

    <!-- MENÚ VERTICAL A LA DERECHA -->
    <aside class="side" aria-label="Menú lateral">
        <h3>Acceso rápido</h3>
        <a class="side-cta" href="citas.php"><?= icono('plus', 18) ?> Nueva cita</a>
        <a href="home.php" class="<?= $actual === 'home.php' ? 'activo' : '' ?>"><?= icono('home', 18) ?> Inicio</a>
        <?php foreach ($modulos as $modulo): ?>
            <a href="<?= e($modulo['url']) ?>" class="<?= $actual === $modulo['url'] ? 'activo' : '' ?>">
                <?= icono($modulo['icono'], 18) ?> <?= e($modulo['nombre']) ?>
            </a>
        <?php endforeach; ?>
    </aside>
</div>

<!-- PIE DE PÁGINA -->
<footer class="site-footer">
    <div class="footer-grid">
        <div>
            <div class="brand brand-footer">
                <img src="../imagenes/logo.svg" alt="" width="36" height="36">
                <strong><?= e(MARCA) ?></strong>
            </div>
            <p>Cuidamos cada detalle de tu sonrisa con tecnología, calidez y profesionalismo.</p>
        </div>
        <div>
            <h4>Horario de atención</h4>
            <p><?= icono('clock', 16) ?> Lunes a sábado</p>
            <p>9:00 a. m. – 7:00 p. m.</p>
        </div>
        <div>
            <h4>Contacto</h4>
            <p><?= icono('phone', 16) ?> (01) 555 0123</p>
            <p>contacto@sonrisaperfecta.pe</p>
        </div>
    </div>
    <div class="footer-bottom">© <?= date('Y') ?> <?= e(MARCA) ?> · Sistema de gestión odontológica · Proyecto SENATI</div>
</footer>
<script src="../assetes/js/app.js"></script>
</body>
</html>
    <?php
}
