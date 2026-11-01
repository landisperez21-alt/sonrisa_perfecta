// JavaScript de Sonrisa Perfecta: pequeñas mejoras de la interfaz.
document.addEventListener('DOMContentLoaded', function () {

    // 1) Menú desplegable en celulares
    var botonMenu = document.querySelector('.nav-toggle');
    var enlaces = document.getElementById('nav-links');
    if (botonMenu && enlaces) {
        botonMenu.addEventListener('click', function () {
            var abierto = enlaces.classList.toggle('abierto');
            botonMenu.setAttribute('aria-expanded', abierto ? 'true' : 'false');
        });
    }

    // 2) Buscador de tablas (filtra mientras escribes)
    document.querySelectorAll('[data-filtro-tabla]').forEach(function (campo) {
        var tabla = document.getElementById(campo.getAttribute('data-filtro-tabla'));
        if (!tabla) { return; }
        campo.addEventListener('input', function () {
            var texto = campo.value.trim().toLowerCase();
            tabla.querySelectorAll('tbody tr').forEach(function (fila) {
                if (fila.querySelector('.vacio')) { return; }
                fila.style.display = fila.textContent.toLowerCase().indexOf(texto) === -1 ? 'none' : '';
            });
        });
    });

    // 3) Confirmación antes de eliminar
    document.querySelectorAll('form[data-confirm]').forEach(function (formulario) {
        formulario.addEventListener('submit', function (evento) {
            if (!window.confirm(formulario.getAttribute('data-confirm'))) {
                evento.preventDefault();
            }
        });
    });

    // 4) Pestañas Ingresar / Registrarme
    var pestanas = document.querySelectorAll('[data-tab]');
    var paneles = document.querySelectorAll('[data-panel]');
    var titulo = document.getElementById('auth-titulo');
    var subtitulo = document.getElementById('auth-sub');
    var textos = {
        login: ['Bienvenido de nuevo', 'Ingresa con tu cuenta para continuar.'],
        registro: ['Crea tu cuenta', 'Regístrate para empezar a gestionar la clínica.']
    };
    function mostrar(nombre) {
        pestanas.forEach(function (p) {
            var activa = p.getAttribute('data-tab') === nombre;
            p.classList.toggle('activo', activa);
            p.setAttribute('aria-selected', activa ? 'true' : 'false');
        });
        paneles.forEach(function (panel) {
            panel.hidden = panel.getAttribute('data-panel') !== nombre;
        });
        if (titulo && textos[nombre]) {
            titulo.textContent = textos[nombre][0];
            subtitulo.textContent = textos[nombre][1];
        }
    }
    pestanas.forEach(function (p) {
        p.addEventListener('click', function () { mostrar(p.getAttribute('data-tab')); });
    });
    var activa = document.querySelector('[data-tab].activo');
    if (activa) { mostrar(activa.getAttribute('data-tab')); }

    // 5) Mostrar / ocultar contraseña
    document.querySelectorAll('[data-ver-pass]').forEach(function (boton) {
        boton.addEventListener('click', function () {
            var campo = boton.parentElement.querySelector('input');
            var oculto = campo.type === 'password';
            campo.type = oculto ? 'text' : 'password';
            boton.textContent = oculto ? 'Ocultar' : 'Ver';
        });
    });

    // 6) Botón imprimir (reportes)
    document.querySelectorAll('[data-imprimir]').forEach(function (boton) {
        boton.addEventListener('click', function () { window.print(); });
    });

    // 7) El aviso verde desaparece solo
    var aviso = document.querySelector('.notice');
    if (aviso && !aviso.closest('.auth-card')) {
        setTimeout(function () { aviso.style.transition = 'opacity .6s'; aviso.style.opacity = '0'; }, 4000);
    }
});
