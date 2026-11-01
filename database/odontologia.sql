-- =====================================================================
--  Sonrisa Perfecta · Clínica Odontológica
--  Base de datos: odontologia_db  (MySQL 5.7+ / MariaDB 10.3+ / XAMPP)
-- =====================================================================
CREATE DATABASE IF NOT EXISTS odontologia_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE odontologia_db;

-- 1. USUARIOS DEL SISTEMA (login). El registro público crea 'recepcion'.
CREATE TABLE IF NOT EXISTS usuarios (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(120) NOT NULL,
    email         VARCHAR(180) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol           ENUM('admin', 'odontologo', 'recepcion') NOT NULL DEFAULT 'recepcion',
    activo        TINYINT(1)   NOT NULL DEFAULT 1,
    creado        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- 2. PACIENTES
CREATE TABLE IF NOT EXISTS pacientes (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre           VARCHAR(120) NOT NULL,
    documento        VARCHAR(20)  NOT NULL UNIQUE,
    fecha_nacimiento DATE,
    telefono         VARCHAR(25),
    email            VARCHAR(150),
    direccion        VARCHAR(255),
    creado           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- 3. ODONTÓLOGOS
CREATE TABLE IF NOT EXISTS odontologos (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre       VARCHAR(120) NOT NULL,
    colegiatura  VARCHAR(30)  NOT NULL UNIQUE,
    especialidad VARCHAR(120),
    telefono     VARCHAR(25),
    email        VARCHAR(150)
) ENGINE = InnoDB;

-- 4. SERVICIOS ODONTOLÓGICOS
CREATE TABLE IF NOT EXISTS servicios (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre       VARCHAR(120) NOT NULL UNIQUE,
    descripcion  TEXT,
    precio       DECIMAL(10, 2) NOT NULL DEFAULT 0,
    duracion_min INT UNSIGNED   NOT NULL DEFAULT 30,
    CONSTRAINT chk_servicio_precio   CHECK (precio >= 0),
    CONSTRAINT chk_servicio_duracion CHECK (duracion_min > 0)
) ENGINE = InnoDB;

-- 5. CITAS (fecha, hora y estado)
--    El UNIQUE evita que un odontólogo tenga dos citas a la misma hora.
CREATE TABLE IF NOT EXISTS citas (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    paciente_id   INT UNSIGNED NOT NULL,
    odontologo_id INT UNSIGNED NOT NULL,
    servicio_id   INT UNSIGNED NOT NULL,
    fecha         DATE NOT NULL,
    hora          TIME NOT NULL,
    estado        ENUM('pendiente', 'confirmada', 'atendida', 'cancelada') NOT NULL DEFAULT 'pendiente',
    motivo        TEXT,
    CONSTRAINT fk_cita_paciente   FOREIGN KEY (paciente_id)   REFERENCES pacientes (id)   ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_cita_odontologo FOREIGN KEY (odontologo_id) REFERENCES odontologos (id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_cita_servicio   FOREIGN KEY (servicio_id)   REFERENCES servicios (id)   ON UPDATE CASCADE ON DELETE RESTRICT,
    UNIQUE KEY uq_odontologo_horario (odontologo_id, fecha, hora),
    INDEX idx_cita_fecha (fecha),
    INDEX idx_cita_estado (estado)
) ENGINE = InnoDB;

-- 6. DIAGNÓSTICOS (cada diagnóstico pertenece a una cita)
CREATE TABLE IF NOT EXISTS diagnosticos (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cita_id       INT UNSIGNED NOT NULL,
    descripcion   TEXT NOT NULL,
    observaciones TEXT,
    fecha         DATE NOT NULL,
    CONSTRAINT fk_diag_cita FOREIGN KEY (cita_id) REFERENCES citas (id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;

-- 7. TRATAMIENTOS (cada tratamiento pertenece a un diagnóstico)
CREATE TABLE IF NOT EXISTS tratamientos (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    diagnostico_id INT UNSIGNED NOT NULL,
    procedimiento  VARCHAR(200) NOT NULL,
    materiales     VARCHAR(255),
    indicaciones   TEXT,
    fecha_inicio   DATE,
    fecha_fin      DATE,
    costo          DECIMAL(10, 2) NOT NULL DEFAULT 0,
    CONSTRAINT fk_trat_diag FOREIGN KEY (diagnostico_id) REFERENCES diagnosticos (id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_trat_costo CHECK (costo >= 0)
) ENGINE = InnoDB;

-- 8. PAGOS (efectivo, tarjeta de crédito, tarjeta de débito o Yape)
CREATE TABLE IF NOT EXISTS pagos (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cita_id    INT UNSIGNED NOT NULL,
    monto      DECIMAL(10, 2) NOT NULL,
    metodo     ENUM('efectivo', 'tarjeta_credito', 'tarjeta_debito', 'yape') NOT NULL,
    estado     ENUM('pendiente', 'pagado', 'anulado') NOT NULL DEFAULT 'pendiente',
    referencia VARCHAR(120),
    fecha_pago DATETIME,
    CONSTRAINT fk_pago_cita FOREIGN KEY (cita_id) REFERENCES citas (id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_pago_monto CHECK (monto > 0),
    INDEX idx_pago_fecha (fecha_pago)
) ENGINE = InnoDB;

-- DATOS INICIALES (INSERT IGNORE: si ya existen, no se duplican)
INSERT IGNORE INTO servicios (nombre, descripcion, precio, duracion_min) VALUES
    ('Consulta y evaluación', 'Revisión completa, odontograma y plan de tratamiento.', 50.00, 30),
    ('Limpieza dental', 'Profilaxis profesional para remover sarro y placa.', 90.00, 45),
    ('Blanqueamiento dental', 'Aclara el tono de tus dientes con técnica segura.', 350.00, 60),
    ('Ortodoncia', 'Brackets y controles mensuales para alinear tu sonrisa.', 150.00, 30),
    ('Endodoncia', 'Tratamiento de conducto para salvar la pieza dental.', 450.00, 90),
    ('Extracción dental', 'Extracción simple con anestesia local.', 120.00, 40),
    ('Restauración con resina', 'Reparación estética de caries y fracturas.', 130.00, 45),
    ('Odontopediatría', 'Atención amable y preventiva para los más pequeños.', 80.00, 30);

INSERT IGNORE INTO odontologos (nombre, colegiatura, especialidad, telefono, email) VALUES
    ('Dra. Camila Rojas Vega',      'COP-12345', 'Ortodoncia',                     '987654321', 'camila.rojas@sonrisaperfecta.pe'),
    ('Dr. Andrés Salazar Peña',     'COP-23456', 'Endodoncia',                     '987654322', 'andres.salazar@sonrisaperfecta.pe'),
    ('Dra. Lucía Fernández Torres', 'COP-34567', 'Odontología general y estética', '987654323', 'lucia.fernandez@sonrisaperfecta.pe');
