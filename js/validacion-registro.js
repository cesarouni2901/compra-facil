/*
 * Filtra los caracteres de nombre, apellido, cédula y teléfono en los
 * formularios de registro. Se conecta con los campos que declaran
 * data-filtro="letras" o data-filtro="numeros" en registro-cliente.php
 * y registro-vendedor.php. El servidor PHP mantiene la validación final.
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
});
