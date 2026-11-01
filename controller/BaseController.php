<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/BaseModel.php';

if (session_status() === PHP_SESSION_NONE) {
    // Cookie de sesión más segura: no la puede leer JavaScript.
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

const MARCA = 'Sonrisa Perfecta';

/* ---------- Utilidades generales ---------- */

function db(): PDO
{
    static $db = null;
    return $db ??= (new Database())->conectar();
}

// Escapa texto antes de imprimirlo en HTML (protege contra XSS).
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function csrf(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function validarCsrf(): void
{
    if (!hash_equals(csrf(), (string) ($_POST['csrf'] ?? ''))) {
        throw new RuntimeException('Token de seguridad no válido. Recarga la página e inténtalo otra vez.');
    }
}

function exigirSesion(): void
{
    if (empty($_SESSION['uid'])) {
        header('Location: login.php');
        exit;
    }
}

function redirigir(string $ruta): void
{
    header('Location: ' . $ruta);
    exit;
}

function notificar(string $mensaje): void
{
    $_SESSION['flash'] = $mensaje;
}

/* ---------- Textos bonitos para pantalla ---------- */

function etiqueta(string $campo): string
{
    $mapa = [
        'nombre' => 'Nombre completo',
        'documento' => 'Documento de identidad',
        'fecha_nacimiento' => 'Fecha de nacimiento',
        'telefono' => 'Teléfono',
        'email' => 'Correo electrónico',
        'direccion' => 'Dirección',
        'colegiatura' => 'Colegiatura (COP)',
        'especialidad' => 'Especialidad',
        'descripcion' => 'Descripción',
        'precio' => 'Precio (S/)',
        'duracion_min' => 'Duración (minutos)',
        'paciente_id' => 'Paciente',
        'odontologo_id' => 'Odontólogo',
        'servicio_id' => 'Servicio',
        'paciente' => 'Paciente',
        'odontologo' => 'Odontólogo',
        'servicio' => 'Servicio',
        'fecha' => 'Fecha',
        'hora' => 'Hora',
        'estado' => 'Estado',
        'motivo' => 'Motivo de la cita',
        'cita_id' => 'Cita',
        'observaciones' => 'Observaciones',
        'diagnostico_id' => 'Diagnóstico',
        'procedimiento' => 'Procedimiento',
        'materiales' => 'Medicamentos / materiales',
        'indicaciones' => 'Indicaciones',
        'fecha_inicio' => 'Inicio',
        'fecha_fin' => 'Fin',
        'costo' => 'Costo (S/)',
        'monto' => 'Monto (S/)',
        'metodo' => 'Método de pago',
        'referencia' => 'Referencia / N.º de operación',
        'fecha_pago' => 'Fecha de pago',
    ];

    return $mapa[$campo] ?? ucwords(str_replace('_', ' ', $campo));
}

function nombreMetodo(string $metodo): string
{
    return [
        'efectivo' => 'Efectivo',
        'tarjeta_credito' => 'Tarjeta de crédito',
        'tarjeta_debito' => 'Tarjeta de débito',
        'yape' => 'Yape',
    ][$metodo] ?? ucfirst($metodo);
}

function dinero($valor): string
{
    return 'S/ ' . number_format((float) $valor, 2);
}

function fechaCorta(?string $valor, bool $conHora = false): string
{
    if (!$valor) {
        return '—';
    }
    $marca = strtotime($valor);
    return $marca ? date($conHora ? 'd/m/Y H:i' : 'd/m/Y', $marca) : $valor;
}

// Devuelve HTML ya escapado para mostrar un dato en las tablas.
function formatearCelda(string $campo, $valor): string
{
    if ($valor === null || $valor === '') {
        return '—';
    }

    switch ($campo) {
        case 'estado':
            return '<span class="badge badge-' . e($valor) . '">' . e(ucfirst((string) $valor)) . '</span>';
        case 'metodo':
            return e(nombreMetodo((string) $valor));
        case 'precio':
        case 'costo':
        case 'monto':
            return e(dinero($valor));
        case 'duracion_min':
            return e($valor) . ' min';
        case 'fecha':
        case 'fecha_nacimiento':
        case 'fecha_inicio':
        case 'fecha_fin':
            return e(fechaCorta((string) $valor));
        case 'fecha_pago':
            return e(fechaCorta((string) $valor, true));
        default:
            return e(mb_strimwidth((string) $valor, 0, 70, '…', 'UTF-8'));
    }
}

/* ---------- Listas desplegables (solo consultas fijas, nunca texto del usuario) ---------- */

function opcionesReferencia(string $tipo): array
{
    $consultas = [
        'pacientes' => "SELECT id, CONCAT(nombre, ' · ', documento) AS etiqueta
                        FROM pacientes ORDER BY nombre",
        'odontologos' => "SELECT id, CONCAT(nombre, ' · ', COALESCE(especialidad, 'General')) AS etiqueta
                          FROM odontologos ORDER BY nombre",
        'servicios' => "SELECT id, CONCAT(nombre, ' · S/ ', FORMAT(precio, 2)) AS etiqueta
                        FROM servicios ORDER BY nombre",
        'citas' => "SELECT c.id,
                           CONCAT(DATE_FORMAT(c.fecha, '%d/%m/%Y'), ' ', TIME_FORMAT(c.hora, '%H:%i'),
                                  ' · ', p.nombre, ' · ', s.nombre) AS etiqueta
                    FROM citas c
                    JOIN pacientes p ON p.id = c.paciente_id
                    JOIN servicios s ON s.id = c.servicio_id
                    ORDER BY c.fecha DESC, c.hora DESC LIMIT 500",
        'diagnosticos' => "SELECT d.id,
                                  CONCAT('N.º ', d.id, ' · ', p.nombre, ' · ', LEFT(d.descripcion, 40)) AS etiqueta
                           FROM diagnosticos d
                           JOIN citas c     ON c.id = d.cita_id
                           JOIN pacientes p ON p.id = c.paciente_id
                           ORDER BY d.id DESC LIMIT 500",
    ];

    return isset($consultas[$tipo]) ? db()->query($consultas[$tipo])->fetchAll() : [];
}

function tiposReferencia(): array
{
    return ['pacientes', 'odontologos', 'servicios', 'citas', 'diagnosticos'];
}

function opcionesFijas(string $tipo): array
{
    return [
        'estado_cita' => ['pendiente', 'confirmada', 'atendida', 'cancelada'],
        'estado_pago' => ['pendiente', 'pagado', 'anulado'],
        'metodo_pago' => ['efectivo', 'tarjeta_credito', 'tarjeta_debito', 'yape'],
    ][$tipo] ?? [];
}

/* ---------- Validación de formularios (lado servidor) ---------- */

function validarDatos(array $campos, array $datos, array $obligatorios = []): array
{
    $salida = [];

    foreach ($campos as $campo => $tipo) {
        $valor = trim((string) ($datos[$campo] ?? ''));
        $nombre = etiqueta($campo);

        if ($valor === '' && in_array($campo, $obligatorios, true)) {
            throw new RuntimeException("El campo «{$nombre}» es obligatorio.");
        }

        if ($valor === '') {
            $salida[$campo] = null;
            continue;
        }

        if (in_array($tipo, ['text', 'tel', 'doc', 'email'], true) && mb_strlen($valor) > 255) {
            throw new RuntimeException("«{$nombre}» es demasiado largo.");
        }
        if ($tipo === 'textarea' && mb_strlen($valor) > 2000) {
            throw new RuntimeException("«{$nombre}» admite máximo 2000 caracteres.");
        }

        switch (true) {
            case $tipo === 'email':
                if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                    throw new RuntimeException('El correo electrónico no es válido.');
                }
                break;

            case $tipo === 'tel':
                if (!preg_match('/^[0-9+\s()-]{6,25}$/', $valor)) {
                    throw new RuntimeException('El teléfono solo admite números, +, espacios y guiones (6 a 25 caracteres).');
                }
                break;

            case $tipo === 'doc':
                if (!preg_match('/^[A-Za-z0-9-]{6,20}$/', $valor)) {
                    throw new RuntimeException('El documento debe tener entre 6 y 20 letras o números (sin espacios).');
                }
                break;

            case $tipo === 'number':
                if (!is_numeric($valor) || (float) $valor < 0) {
                    throw new RuntimeException("«{$nombre}» debe ser un número positivo.");
                }
                break;

            case $tipo === 'minutos':
                if (!ctype_digit($valor) || (int) $valor < 1 || (int) $valor > 600) {
                    throw new RuntimeException('La duración debe ser un número entero entre 1 y 600 minutos.');
                }
                break;

            case in_array($tipo, tiposReferencia(), true):
                if (!ctype_digit($valor) || (int) $valor < 1) {
                    throw new RuntimeException("Seleccione una opción válida en «{$nombre}».");
                }
                break;

            case $tipo === 'date':
                $fecha = DateTime::createFromFormat('!Y-m-d', $valor);
                if (!$fecha || $fecha->format('Y-m-d') !== $valor) {
                    throw new RuntimeException("La fecha de «{$nombre}» no es válida.");
                }
                if ($campo === 'fecha_nacimiento' && $fecha > new DateTime('today')) {
                    throw new RuntimeException('La fecha de nacimiento no puede ser futura.');
                }
                break;

            case $tipo === 'time':
                $hora = DateTime::createFromFormat('!H:i', substr($valor, 0, 5));
                if (!$hora || $hora->format('H:i') !== substr($valor, 0, 5)) {
                    throw new RuntimeException('La hora no es válida.');
                }
                $valor = $hora->format('H:i:s');
                break;

            case $tipo === 'datetime-local':
                $fecha = DateTime::createFromFormat('!Y-m-d\TH:i', $valor);
                if (!$fecha || $fecha->format('Y-m-d\TH:i') !== $valor) {
                    throw new RuntimeException('La fecha y hora no son válidas.');
                }
                $valor = $fecha->format('Y-m-d H:i:s');
                break;

            case in_array($tipo, ['estado_cita', 'estado_pago', 'metodo_pago'], true):
                if (!in_array($valor, opcionesFijas($tipo), true)) {
                    throw new RuntimeException("Selección inválida en «{$nombre}».");
                }
                break;
        }

        $salida[$campo] = $valor;
    }

    return $salida;
}

/* ---------- Menú del sistema ---------- */

function modulos(): array
{
    return [
        'pacientes' => [
            'nombre' => 'Pacientes', 'url' => 'pacientes.php', 'icono' => 'users',
            'texto' => 'Fichas con datos personales, contacto y documento de identidad.',
        ],
        'odontologos' => [
            'nombre' => 'Odontólogos', 'url' => 'odontologos.php', 'icono' => 'stethoscope',
            'texto' => 'Equipo clínico con su colegiatura y especialidad.',
        ],
        'servicios' => [
            'nombre' => 'Servicios', 'url' => 'servicios.php', 'icono' => 'tooth',
            'texto' => 'Limpieza, ortodoncia, endodoncia, blanqueamiento y más.',
        ],
        'citas' => [
            'nombre' => 'Citas', 'url' => 'citas.php', 'icono' => 'calendar',
            'texto' => 'Agenda por fecha, hora y estado de cada cita.',
        ],
        'diagnosticos' => [
            'nombre' => 'Diagnósticos', 'url' => 'diagnosticos.php', 'icono' => 'clipboard',
            'texto' => 'Registro clínico y observaciones de cada atención.',
        ],
        'tratamientos' => [
            'nombre' => 'Tratamientos', 'url' => 'tratamientos.php', 'icono' => 'pill',
            'texto' => 'Procedimientos, materiales, indicaciones y costos.',
        ],
        'pagos' => [
            'nombre' => 'Pagos', 'url' => 'pagos.php', 'icono' => 'card',
            'texto' => 'Efectivo, tarjetas y Yape con su referencia.',
        ],
        'reportes' => [
            'nombre' => 'Reportes', 'url' => 'reportes.php', 'icono' => 'chart',
            'texto' => 'Atenciones, ingresos por período y citas pendientes.',
        ],
    ];
}

/* ---------- Iconos (SVG en línea, heredan el color del texto) ---------- */

function icono(string $nombre, int $tam = 20): string
{
    $trazos = [
        'home' => '<path d="m3 10 9-7 9 7v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'stethoscope' => '<path d="M11 2v2"/><path d="M5 2v2"/><path d="M5 3H4a2 2 0 0 0-2 2v4a6 6 0 0 0 12 0V5a2 2 0 0 0-2-2h-1"/><path d="M8 15a6 6 0 0 0 12 0v-3"/><circle cx="20" cy="10" r="2"/>',
        'tooth' => '<path d="M7.5 3C5.3 3 3.5 4.7 3.5 7c0 1.9.8 3.2 1.4 4.8.8 2.1.6 5.4 1.6 8 .4 1 1.3 1.3 2 .7.9-.8 1-3.5 2.1-3.5h2.8c1.1 0 1.2 2.7 2.1 3.5.7.6 1.6.3 2-.7 1-2.6.8-5.9 1.6-8 .6-1.6 1.4-2.9 1.4-4.8 0-2.3-1.8-4-4-4-1.8 0-2.5.8-4.5.8S9.3 3 7.5 3z"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="m9 16 2 2 4-4"/>',
        'clipboard' => '<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4M12 16h4M8 11h.01M8 16h.01"/>',
        'pill' => '<path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/><path d="m8.5 8.5 7 7"/>',
        'card' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
        'chart' => '<path d="M3 3v18h18"/><path d="M18 17V9M13 17V5M8 17v-3"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
        'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
        'printer' => '<path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/>',
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/>',
        'clock' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
    ];

    return '<svg class="ico" width="' . $tam . '" height="' . $tam . '" viewBox="0 0 24 24" fill="none" '
        . 'stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
        . ($trazos[$nombre] ?? '') . '</svg>';
}
