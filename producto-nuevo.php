<?php
require_once __DIR__ . '/app/bootstrap.php';
$catalog = new ProductCatalog();
$cart = new ShoppingCart();
$currentUser = authenticatedUser();
$message = '';
if ($currentUser && $currentUser['role'] === 'vendedor') {
    $message = (new ProductController($catalog))->create();
} else {
    http_response_code(403);
    $message = 'Debes iniciar sesión con una cuenta de vendedor adulta para publicar productos.';
}
$pageTitle = 'Publicar producto';
$cartCount = $cart->getCount();
require __DIR__ . '/app/views/partials/header.php';
?>
<main class="account-page py-5"><div class="container"><div class="row justify-content-center"><div class="col-xl-10"><div class="text-center mb-4"><span class="eyebrow">Espacio de vendedores</span><h1 class="h2 mt-2">Publicar un producto</h1><p class="text-secondary">Completa la información, las opciones y las diferentes vistas del producto.</p></div>
<?php if (!$currentUser || $currentUser['role'] !== 'vendedor'): ?><div class="alert alert-warning text-center"><?= escapeHtml($message) ?> <a href="login.php">Iniciar sesión</a></div>
<?php else: ?><form class="account-card card border-0 shadow" method="post" enctype="multipart/form-data"><div class="account-card-heading text-center text-white py-3"><h2 class="h5 mb-0">Detalles del producto</h2></div><div class="card-body p-4 p-md-5 row g-3">
  <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
  <?php if ($message !== ''): ?><div class="col-12"><div class="alert alert-danger mb-0" role="status"><?= escapeHtml($message) ?></div></div><?php endif; ?>
  <div class="col-md-6"><label class="form-label fw-semibold" for="name">Nombre del producto *</label><input class="form-control" id="name" name="name" maxlength="140" required value="<?= escapeHtml((string) ($_POST['name'] ?? '')) ?>"></div>
  <div class="col-md-6"><label class="form-label fw-semibold" for="brand">Marca *</label><input class="form-control" id="brand" name="brand" maxlength="100" required value="<?= escapeHtml((string) ($_POST['brand'] ?? '')) ?>"></div>
  <div class="col-md-6"><label class="form-label fw-semibold" for="category">Categoría *</label><input class="form-control" id="category" name="category" maxlength="80" required placeholder="Ej. Telefonía"></div>
  <div class="col-md-6"><label class="form-label fw-semibold" for="price">Precio base (USD) *</label><input class="form-control" id="price" name="price" type="number" min="0.01" step="0.01" required></div>
  <div class="col-12"><label class="form-label fw-semibold" for="description">Descripción *</label><textarea class="form-control" id="description" name="description" rows="3" maxlength="2000" required></textarea></div>
  <div class="col-12"><h3 class="h5 mt-2">Opciones de color y almacenamiento</h3><p class="small text-secondary">Agrega las combinaciones disponibles; puedes dejar filas vacías.</p></div>
  <?php for ($index = 0; $index < 6; $index++): ?><div class="col-md-4"><label class="form-label small" for="color-<?= $index ?>">Color <?= $index + 1 ?></label><input class="form-control" id="color-<?= $index ?>" name="colors[]" maxlength="60" placeholder="Ej. Azul"></div><div class="col-md-4"><label class="form-label small" for="storage-<?= $index ?>">Capacidad / característica <?= $index + 1 ?></label><input class="form-control" id="storage-<?= $index ?>" name="storages[]" maxlength="60" placeholder="Ej. 256 GB"></div><div class="col-md-4"><label class="form-label small" for="variant-price-<?= $index ?>">Precio de esta opción (opcional)</label><input class="form-control" id="variant-price-<?= $index ?>" name="variant_prices[]" type="number" min="0.01" step="0.01" placeholder="Usar precio base"></div><?php endfor; ?>
  <div class="col-12"><h3 class="h5 mt-2">Fotos y vistas del producto</h3><p class="small text-secondary">Sube hasta 6 imágenes JPG, PNG o WebP, máximo 1 MB cada una. Puedes elegir una foto frontal, lateral, posterior, de la laptop abierta o de un detalle.</p></div>
  <?php $viewOptions = ['Frontal', 'Lateral', 'Posterior', 'Abierta', 'Detalle', 'Otra vista']; for ($index = 0; $index < 6; $index++): ?><div class="col-md-8"><label class="form-label small" for="image-<?= $index ?>">Imagen <?= $index + 1 ?></label><input class="form-control" id="image-<?= $index ?>" name="images[]" type="file" accept="image/jpeg,image/png,image/webp" <?= $index === 0 ? 'required' : '' ?>></div><div class="col-md-4"><label class="form-label small" for="image-view-<?= $index ?>">Vista</label><select class="form-select" id="image-view-<?= $index ?>" name="image_views[]"><?php foreach ($viewOptions as $view): ?><option value="<?= escapeHtml($view) ?>"><?= escapeHtml($view) ?></option><?php endforeach; ?></select></div><?php endfor; ?>
  <div class="col-12 pt-2"><button class="btn btn-primary btn-lg w-100" type="submit">Publicar producto</button></div>
</div></form><?php endif; ?>
</div></div></div></main>
<?php require __DIR__ . '/app/views/partials/footer.php'; ?>
