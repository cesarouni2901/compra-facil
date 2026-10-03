/*
 * Funciones específicas de los formularios de registro:
 * 1. Filtra nombre, apellido, cédula y teléfono mediante data-filtro.
 * 2. Muestra u oculta cada clave desde su botón de ojo.
 * 3. Avisa en vivo cuando la clave y su confirmación no coinciden.
 * Se conecta con los controles de registro-cliente.php y
 * registro-vendedor.php. El servidor PHP mantiene la validación final.
 */
document.addEventListener('DOMContentLoaded', function () {
    // Obtiene únicamente los campos autorizados en los formularios de registro.
    const camposFiltrados = document.querySelectorAll('[data-filtro]');

    // Indica si un carácter corresponde al tipo permitido para cada campo.
    function caracterPermitido(caracter, tipo) {
        if (tipo === 'letras') {
            // Acepta letras Unicode (incluidas tildes), marcas diacríticas y espacios.
            return /^[\p{L}\p{M} ]$/u.test(caracter);
        }

        if (tipo === 'numeros') {
            // Cédula y teléfono solo aceptan los dígitos del 0 al 9.
            return /^[0-9]$/.test(caracter);
        }

        return true;
    }

    // Limpia entradas pegadas o insertadas por métodos distintos al teclado.
    function limpiarValor(campo) {
        const tipo = campo.dataset.filtro;
        const posicionCursor = campo.selectionStart;
        const textoAntesDelCursor = campo.value.slice(0, posicionCursor);
        const expresion = tipo === 'letras' ? /[^\p{L}\p{M} ]/gu : /[^0-9]/g;

        // Cuenta los caracteres válidos antes del cursor para conservar su posición.
        const cursorLimpio = textoAntesDelCursor.replace(expresion, '').length;
        campo.value = campo.value.replace(expresion, '');
        campo.setSelectionRange(cursorLimpio, cursorLimpio);
    }

    camposFiltrados.forEach(function (campo) {
        // Impide que una tecla inválida llegue a mostrarse en el campo.
        campo.addEventListener('keydown', function (evento) {
            const esTeclaDeTexto = evento.key.length === 1;
            if (esTeclaDeTexto && !caracterPermitido(evento.key, campo.dataset.filtro)) {
                evento.preventDefault();
            }
        });

        // Revisa el valor tras pegar texto, autocompletar o usar entrada por voz.
        campo.addEventListener('input', function () {
            limpiarValor(campo);
        });
    });

    // Conecta cada botón con el campo de clave indicado en data-mostrar-clave.
    document.querySelectorAll('[data-mostrar-clave]').forEach(function (boton) {
        boton.addEventListener('click', function () {
            const campo = document.getElementById(boton.dataset.mostrarClave);
            const iconoOjo = boton.querySelector('[data-icono-ojo]');
            const iconoOjoCerrado = boton.querySelector('[data-icono-ojo-cerrado]');
            if (!campo) return;

            // Cambia el tipo del input y actualiza texto accesible e icono.
            const mostrarClave = campo.type === 'password';
            campo.type = mostrarClave ? 'text' : 'password';
            boton.setAttribute('aria-label', mostrarClave ? 'Ocultar clave' : 'Mostrar clave');
            boton.setAttribute('title', mostrarClave ? 'Ocultar clave' : 'Mostrar clave');
            if (iconoOjo) iconoOjo.hidden = mostrarClave;
            if (iconoOjoCerrado) iconoOjoCerrado.hidden = !mostrarClave;
        });
    });

    // Busca el par de claves y el mensaje que existen solo en páginas de registro.
    const campoClave = document.getElementById('contrasena');
    const campoConfirmacion = document.getElementById('confirmacion');
    const mensajeClaves = document.getElementById('mensaje-claves');

    // Configura aviso inmediato y evita enviar el formulario si las claves difieren.
    if (campoClave && campoConfirmacion && mensajeClaves) {
        const formulario = campoConfirmacion.form;

        function validarCoincidencia() {
            const confirmacionEscrita = campoConfirmacion.value.length > 0;
            const clavesNoCoinciden = confirmacionEscrita
                && campoClave.value !== campoConfirmacion.value;

            // Actualiza la validación nativa del navegador y el mensaje visible.
            campoConfirmacion.setCustomValidity(
                clavesNoCoinciden ? 'Las claves no coinciden.' : ''
            );
            mensajeClaves.textContent = clavesNoCoinciden
                ? 'Las claves no coinciden.'
                : (confirmacionEscrita ? 'Las claves coinciden.' : '');
            mensajeClaves.classList.toggle('text-danger', clavesNoCoinciden);
            mensajeClaves.classList.toggle('text-success', confirmacionEscrita && !clavesNoCoinciden);

            return !clavesNoCoinciden;
        }

        // Revisa ambos campos conforme se escribe, sin esperar al envío.
        campoClave.addEventListener('input', validarCoincidencia);
        campoConfirmacion.addEventListener('input', validarCoincidencia);

        // Si no coinciden al intentar enviar, conserva el formulario y enfoca confirmación.
        if (formulario) {
            formulario.addEventListener('submit', function (evento) {
                if (!validarCoincidencia()) {
                    evento.preventDefault();
                    campoConfirmacion.focus();
                    campoConfirmacion.reportValidity();
                }
            });
        }
    }
});
