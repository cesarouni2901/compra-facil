<?php
// Protege la cookie de sesión contra acceso JavaScript y envíos entre sitios.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

// Carga explícitamente los modelos y controladores de la aplicación MVC.
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/ProductCatalog.php';
require_once __DIR__ . '/models/ShoppingCart.php';
require_once __DIR__ . '/models/AccountRules.php';
require_once __DIR__ . '/models/UserRepository.php';
require_once __DIR__ . '/Security.php';
require_once __DIR__ . '/controllers/CatalogController.php';
require_once __DIR__ . '/controllers/CartController.php';
require_once __DIR__ . '/controllers/AccountController.php';
require_once __DIR__ . '/controllers/ProductController.php';

/** Escapa texto antes de imprimirlo en una página HTML. */
function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/** Presenta precios de ejemplo en dólares estadounidenses. */
function formatPrice(float $amount): string
{
    return '$' . number_format($amount, 2, '.', ',');
}

/** Crea un token anti-CSRF reutilizable mientras dure la sesión. */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

/** Compara el token enviado con el valor de la sesión de forma constante. */
function validateCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/** Carga la clave privada que no se incluye en Git. */
function applicationSecurity(): Security
{
    static $security = null;
    if ($security instanceof Security) return $security;
    $configPath = __DIR__ . '/config.local.php';
    // La clave se genera una sola vez, queda fuera de Git y la carpeta app está protegida por .htaccess.
    if (!is_file($configPath)) {
        $key = bin2hex(random_bytes(32));
        $phpConfig = "<?php\n// Clave local privada, autogenerada. No compartir ni subir a Git.\nreturn ['encryption_key' => '" . $key . "'];\n";
        if (file_put_contents($configPath, $phpConfig, LOCK_EX) === false) {
            throw new RuntimeException('No se pudo crear app/config.local.php. Revisa los permisos de escritura de app/.');
        }
        @chmod($configPath, 0600);
    }
    $config = require $configPath;
    return $security = new Security((string) ($config['encryption_key'] ?? ''));
}

/** Devuelve el resumen de cuenta guardado en la sesión, si hay login. */
function authenticatedUser(): ?array
{
    return isset($_SESSION['auth']) && is_array($_SESSION['auth']) ? $_SESSION['auth'] : null;
}

/** Asegura que la carpeta de almacenamiento privado esté preparada. */
function ensurePrivateStorage(): void
{
    $storagePath = __DIR__ . '/private';
    if (!is_dir($storagePath) && !mkdir($storagePath, 0700, true) && !is_dir($storagePath)) {
        throw new RuntimeException('No se pudo crear la carpeta privada de almacenamiento.');
    }
    @chmod($storagePath, 0700);
}
