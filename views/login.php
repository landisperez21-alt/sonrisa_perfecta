<?php
require_once __DIR__ . '/../controller/AuthController.php';

$mensaje = '';
$esError = false;
$pestana = 'login';   // pestaña que se muestra al cargar
$emailPrevio = '';
$auth = new AuthController();

// Si ya tiene sesión, directo al panel.
if (!empty($_SESSION['uid'])) {
    redirigir('home.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $emailPrevio = trim($_POST['email'] ?? '');
    try {
        validarCsrf();

        if ($accion === 'registro') {
            $pestana = 'registro';
            $auth->registrar(
                trim($_POST['nombre'] ?? ''),
                $emailPrevio,
                $_POST['password'] ?? '',
                $_POST['password2'] ?? ''
            );
            $pestana = 'login';
            $mensaje = 'Cuenta creada con éxito. Ya puedes iniciar sesión.';
        } elseif ($accion === 'login') {
            if ($auth->ingresar($emailPrevio, $_POST['password'] ?? '')) {
                redirigir('home.php');
            }
            $esError = true;
            $mensaje = 'Correo o contraseña incorrectos.';
        }
    } catch (Throwable $ex) {
        $esError = true;
        $mensaje = $ex->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso | <?= e(MARCA) ?></title>
    <link rel="icon" type="image/svg+xml" href="../imagenes/logo.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assetes/css/odonto.css">
</head>
<body class="auth-page">
<main class="auth-wrap">

    <!-- Panel decorativo -->
    <section class="auth-hero" aria-hidden="true">
        <div class="auth-hero-inner">
            <img src="../imagenes/logo.svg" alt="" width="64" height="64">
            <h1><?= e(MARCA) ?></h1>
            <p class="auth-tag">Clínica odontológica</p>
            <p class="auth-lema">Sonrisas sanas, atención con calidez y una agenda siempre ordenada.</p>
            <ul class="auth-puntos">
                <li>Citas y pacientes en un solo lugar</li>
                <li>Diagnósticos y tratamientos al día</li>
                <li>Pagos con efectivo, tarjeta o Yape</li>
            </ul>
        </div>
    </section>

    <!-- Formularios -->
    <section class="auth-card">
        <div class="auth-logo-mobile">
            <img src="../imagenes/logo.svg" alt="" width="44" height="44">
            <strong><?= e(MARCA) ?></strong>
        </div>

        <h2 id="auth-titulo">Bienvenido de nuevo</h2>
        <p class="auth-sub" id="auth-sub">Ingresa con tu cuenta para continuar.</p>

        <div class="tabs" role="tablist">
            <button type="button" role="tab" class="tab <?= $pestana === 'login' ? 'activo' : '' ?>" data-tab="login" aria-selected="<?= $pestana === 'login' ? 'true' : 'false' ?>">Ingresar</button>
            <button type="button" role="tab" class="tab <?= $pestana === 'registro' ? 'activo' : '' ?>" data-tab="registro" aria-selected="<?= $pestana === 'registro' ? 'true' : 'false' ?>">Registrarme</button>
        </div>

        <?php if ($mensaje !== ''): ?>
            <p class="<?= $esError ? 'error' : 'notice' ?>" role="status"><?= e($mensaje) ?></p>
        <?php endif; ?>

        <!-- INGRESAR -->
        <form method="post" class="auth-form" data-panel="login" <?= $pestana === 'login' ? '' : 'hidden' ?>>
            <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">

            <label class="field">
                <span class="field-label">Correo electrónico</span>
                <input name="email" type="email" autocomplete="email" placeholder="tucorreo@ejemplo.com"
                       value="<?= e($emailPrevio) ?>" required>
            </label>

            <label class="field">
                <span class="field-label">Contraseña</span>
                <span class="pass-wrap">
                    <input name="password" type="password" autocomplete="current-password" placeholder="Tu contraseña" required>
                    <button type="button" class="pass-toggle" data-ver-pass>Ver</button>
                </span>
            </label>

            <div class="actions">
                <button type="submit" name="accion" value="login" class="wide">Ingresar</button>
                <button type="reset" class="secondary">Cancelar</button>
            </div>
        </form>

        <!-- REGISTRO -->
        <form method="post" class="auth-form" data-panel="registro" <?= $pestana === 'registro' ? '' : 'hidden' ?>>
            <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">

            <label class="field">
                <span class="field-label">Nombre completo</span>
                <input name="nombre" type="text" autocomplete="name" placeholder="Ej. María Pérez" maxlength="120"
                       value="<?= e($pestana === 'registro' ? trim($_POST['nombre'] ?? '') : '') ?>" required>
            </label>

            <label class="field">
                <span class="field-label">Correo electrónico</span>
                <input name="email" type="email" autocomplete="email" placeholder="tucorreo@ejemplo.com"
                       value="<?= e($pestana === 'registro' ? $emailPrevio : '') ?>" required>
            </label>

            <label class="field">
                <span class="field-label">Contraseña (mínimo 10 caracteres)</span>
                <span class="pass-wrap">
                    <input name="password" type="password" autocomplete="new-password" minlength="10" placeholder="Crea una contraseña segura" required>
                    <button type="button" class="pass-toggle" data-ver-pass>Ver</button>
                </span>
            </label>

            <label class="field">
                <span class="field-label">Repite la contraseña</span>
                <input name="password2" type="password" autocomplete="new-password" minlength="10" placeholder="Vuelve a escribirla" required>
            </label>

            <div class="actions">
                <button type="submit" name="accion" value="registro" class="wide">Crear mi cuenta</button>
                <button type="reset" class="secondary">Cancelar</button>
            </div>
            <p class="auth-note">Las cuentas nuevas se crean con el rol <strong>recepción</strong>.</p>
        </form>
    </section>
</main>
<script src="../assetes/js/app.js"></script>
</body>
</html>
