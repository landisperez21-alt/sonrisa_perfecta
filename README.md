# Sonrisa Perfecta · Sistema de Gestión para una Clínica Odontológica (Caso 0A · SENATI)

Sistema web MVC en **PHP + PDO + MySQL**, basado en la estructura de `sistema-veterinaria-26-2-master`.

## Estructura
- `config/database.php`: única configuración; conexión MySQL con PDO (utf8mb4, excepciones).
- `database/odontologia.sql`: base `odontologia_db` con claves foráneas, restricciones y datos iniciales.
- `model/`: `BaseModel` + un modelo por tabla (`Usuario`, `Paciente`, `Odontologo`, `Servicio`, `Cita`, `Diagnostico`, `Tratamiento`, `Pago`) y `Reporte` (solo lecturas).
- `controller/`: `BaseController` (funciones comunes), `CrudController` (crear/editar/eliminar genérico), `AuthController` y un controlador por módulo.
- `views/`: `login.php`, `home.php`, `layout.php`, `logout.php`, una página por módulo y la carpeta `crud/` con el formulario y la tabla reutilizables.
- `assetes/`: `css/odonto.css` y `js/app.js` (la carpeta conserva el nombre de tu plantilla).
- `imagenes/`: logo y las ilustraciones de las tarjetas.

## Instalación con XAMPP
1. Copia la carpeta `sonrisa-perfecta` dentro de `htdocs`.
2. Inicia **Apache** y **MySQL** en XAMPP.
3. En phpMyAdmin importa `database/odontologia.sql`.
4. Si tu MySQL tiene otra clave, ajusta `config/database.php`.
5. Abre `http://localhost/sonrisa-perfecta/`.

## ¿Cómo ingreso?
No hay usuarios predeterminados. En la pantalla de acceso abre la pestaña **Registrarme**, completa nombre, correo y contraseña (mínimo 10 caracteres) y luego **Ingresar** con esos datos. El registro público siempre crea el rol `recepcion`.

## Seguridad incluida
Consultas preparadas PDO, `password_hash()`/`password_verify()`, `session_regenerate_id()` al ingresar, cierre de sesión que destruye la cookie, token CSRF en todos los formularios, escape de salida con `e()` y validación en el servidor.

## Notas
- Los pagos con tarjeta y Yape son registros internos; no ejecutan cobros reales.
- Un paciente, cita o diagnóstico no se puede eliminar mientras otros registros dependan de él (restricción de clave foránea).
- Los odontólogos y servicios iniciales son datos de ejemplo: puedes editarlos o eliminarlos.
