<?php
/** Controlador de registro, login y cierre de sesión. */
class AccountController
{
    public function __construct(private UserRepository $users) {}

    /** Valida y guarda una cuenta cliente o vendedor. */
    public function register(string $role): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return '';
        if (!validateCsrfToken((string) ($_POST['csrf_token'] ?? ''))) return 'La sesión del formulario venció. Recarga la página e inténtalo otra vez.';

        $user = [
            'role' => $role,
            'first_name' => trim((string) ($_POST['nombre'] ?? '')),
            'last_name' => trim((string) ($_POST['apellido'] ?? '')),
            'document' => trim((string) ($_POST['documento'] ?? '')),
            'email' => trim((string) ($_POST['correo'] ?? '')),
            'birth_date' => (string) ($_POST['fecha_nacimiento'] ?? ''),
            'phone' => trim((string) ($_POST['telefono'] ?? '')),
            'business' => trim((string) ($_POST['negocio'] ?? '')),
            'password' => (string) ($_POST['contrasena'] ?? ''),
        ];
        if (!AccountRules::validName($user['first_name']) || !AccountRules::validName($user['last_name'])) return 'Nombre y apellido solo pueden contener letras y espacios.';
        if (!AccountRules::validDocument($user['document'])) return 'El documento debe contener únicamente números (5 a 20 dígitos).';
        if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) return 'Escribe un correo electrónico válido.';
        if (!AccountRules::isAdult($user['birth_date'])) return 'Debes tener al menos 18 años para crear una cuenta.';
        if ($role === 'vendedor' && ($user['business'] === '' || $user['phone'] === '')) return 'Completa el nombre del negocio y el teléfono.';
        if (!AccountRules::validPassword($user['password'])) return 'La clave debe tener de 8 a 12 caracteres, al menos una mayúscula y un carácter especial.';
        if ($user['password'] !== (string) ($_POST['confirmacion'] ?? '')) return 'Las claves no coinciden.';

        try {
            $id = $this->users->create($user);
            $_SESSION['auth'] = ['id' => $id, 'role' => $role, 'is_adult' => true];
            session_regenerate_id(true);
            return 'Cuenta creada y sesión iniciada correctamente.';
        } catch (DomainException $exception) {
            return $exception->getMessage();
        }
    }

    /** Verifica credenciales y regenera el identificador de sesión. */
    public function login(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return '';
        if (!validateCsrfToken((string) ($_POST['csrf_token'] ?? ''))) return 'La sesión del formulario venció. Recarga la página e inténtalo otra vez.';
        $user = $this->users->authenticate(trim((string) ($_POST['correo'] ?? '')), (string) ($_POST['contrasena'] ?? ''));
        if (!$user) return 'Correo o contraseña incorrectos.';
        if (!$user['is_adult']) return 'La cuenta no cumple con el requisito de edad para comprar o vender.';
        session_regenerate_id(true);
        $_SESSION['auth'] = $user;
        header('Location: catalogo.php');
        exit;
    }
}
