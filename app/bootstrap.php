<?php
// Inicia la sesión para compartir el carrito entre las páginas PHP.
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Carga explícitamente los modelos y controladores de la aplicación MVC.
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/ProductCatalog.php';
require_once __DIR__ . '/models/ShoppingCart.php';
require_once __DIR__ . '/controllers/CatalogController.php';
require_once __DIR__ . '/controllers/CartController.php';
require_once __DIR__ . '/controllers/RegistrationController.php';

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
