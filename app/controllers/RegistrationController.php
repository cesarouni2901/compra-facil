<?php
/** Controlador de validación para los formularios de demostración. */
class RegistrationController
{
    /** Revisa los campos sin guardar cuentas porque aún no hay base de datos. */
    public function submit(string $type): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return '';
        $name = trim((string) ($_POST['nombre'] ?? $_POST['responsable'] ?? ''));
        $email = trim((string) ($_POST['correo'] ?? ''));
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Revisa el nombre y escribe un correo electrónico válido.';
        }
        if ($type === 'vendedor' && (trim((string) ($_POST['negocio'] ?? '')) === '' || trim((string) ($_POST['telefono'] ?? '')) === '')) {
            return 'Completa el nombre del negocio y el teléfono de contacto.';
        }
        $password = (string) ($_POST['contrasena'] ?? '');
        $confirmation = (string) ($_POST['confirmacion'] ?? $password);
        if (strlen($password) < 6 || $password !== $confirmation) {
            return 'La contraseña debe tener al menos 6 caracteres y coincidir con su confirmación.';
        }
        return 'Formulario revisado. El registro real se habilitará al conectar una base de datos.';
    }
}
