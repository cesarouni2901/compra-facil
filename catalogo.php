<?php
// Punto de entrada PHP del catálogo y sus filtros enviados por formulario.
require_once __DIR__ . '/app/bootstrap.php';
$catalog = new ProductCatalog();
$cart = new ShoppingCart();
$controller = new CatalogController($catalog);
extract($controller->index(), EXTR_SKIP);
require __DIR__ . '/app/views/catalog.php';
