<?php
require_once __DIR__ . '/app/bootstrap.php';
$catalog = new ProductCatalog();
$cart = new ShoppingCart();
$product = $catalog->findById((string) ($_GET['id'] ?? ''));
$pageTitle = $product ? $product->name : 'Producto no encontrado';
$cartCount = $cart->getCount();
$selectedView = max(0, (int) ($_GET['view'] ?? 0));
$selectedImage = $product && isset($product->images[$selectedView]) ? $product->images[$selectedView] : ($product->images[0] ?? null);
require __DIR__ . '/app/views/partials/header.php';
?>
<main class="container-xl py-5">
<?php if (!$product): ?><div class="alert alert-warning">No encontramos ese producto. <a href="catalogo.php">Volver al catálogo</a>.</div>
<?php else: ?><div class="row g-4 g-lg-5 align-items-start">
  <section class="col-lg-6" aria-label="Imágenes del producto">
    <div class="detail-image product-media ratio ratio-4x3 rounded-4 bg-body-tertiary shadow-sm"><?php if ($selectedImage): ?><img src="<?= escapeHtml($selectedImage['path']) ?>" alt="<?= escapeHtml($product->name . ' - ' . ($selectedImage['view_label'] ?? 'Vista del producto')) ?>"><?php else: ?><div class="product-placeholder"><span class="placeholder-mark">＋</span><span>El vendedor aún no sube imágenes</span></div><?php endif; ?></div>
    <?php if (count($product->images) > 1): ?><div class="d-flex gap-2 flex-wrap mt-3" aria-label="Otras vistas"><?php foreach ($product->images as $index => $image): ?><a class="image-view-link" href="producto.php?id=<?= rawurlencode($product->id) ?>&amp;view=<?= $index ?>"><span class="image-thumb product-media"><?php if (!empty($image['path'])): ?><img src="<?= escapeHtml($image['path']) ?>" alt="<?= escapeHtml($image['view_label'] ?? 'Vista del producto') ?>"><?php endif; ?></span><small><?= escapeHtml($image['view_label'] ?? 'Vista') ?></small></a><?php endforeach; ?></div><?php endif; ?>
  </section>
  <section class="col-lg-6"><span class="badge rounded-pill text-bg-light"><?= escapeHtml($product->category) ?></span><h1 class="display-6 fw-bold mt-3"><?= escapeHtml($product->name) ?></h1><p class="text-secondary">Marca: <strong><?= escapeHtml($product->brand) ?></strong></p><p class="lead"><?= escapeHtml($product->description) ?></p><p class="h3 text-primary fw-bold"><?= formatPrice($product->price) ?></p>
    <h2 class="h5 mt-4">Colores y características disponibles</h2><div class="vstack gap-2"><?php foreach ($product->variants as $variant): ?><form class="variant-option border rounded-3 p-3 d-flex align-items-center justify-content-between gap-3 flex-wrap" method="post" action="carrito.php"><div><strong><?= escapeHtml(($variant['color'] ?? '') !== '' ? $variant['color'] : 'Color estándar') ?></strong><?php if (($variant['storage'] ?? '') !== ''): ?><span class="text-secondary"> · <?= escapeHtml($variant['storage']) ?></span><?php endif; ?><div class="text-primary fw-semibold"><?= formatPrice((float) ($variant['price'] ?? $product->price)) ?></div></div><input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>"><input type="hidden" name="action" value="add"><input type="hidden" name="product_id" value="<?= escapeHtml($product->id) ?>"><input type="hidden" name="variant_id" value="<?= escapeHtml((string) $variant['id']) ?>"><button class="btn btn-primary" type="submit">Agregar esta opción</button></form><?php endforeach; ?></div>
    <?php if (!$product->variants): ?><form class="mt-4" method="post" action="carrito.php"><input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>"><input type="hidden" name="action" value="add"><input type="hidden" name="product_id" value="<?= escapeHtml($product->id) ?>"><button class="btn btn-primary" type="submit">Añadir al carrito</button></form><?php endif; ?>
  </section>
</div><?php endif; ?>
</main>
<?php require __DIR__ . '/app/views/partials/footer.php'; ?>
