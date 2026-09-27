<?php
// El controlador procesa cambios del carrito antes de renderizar su página.
require_once __DIR__ . '/app/bootstrap.php';
$catalog = new ProductCatalog();
$cart = new ShoppingCart();
$controller = new CartController($cart, $catalog);
extract($controller->index(), EXTR_SKIP);
$pageTitle = 'Carrito';
$cartCount = $cart->getCount();
require __DIR__ . '/app/views/partials/header.php';
?>
<main class="container-xl py-5"><span class="eyebrow">Revisa tu pedido</span><h1 class="h2 mt-2">Carrito de compras</h1><p class="text-secondary mb-4">Verifica los productos y sus cantidades antes de continuar al pago.</p>
  <div class="row g-4 align-items-start"><section class="col-lg-7" aria-label="Productos del carrito">
    <?php if (!$items): ?><div class="empty-state alert border-0 p-4">Tu carrito está vacío. <a href="catalogo.php">Explora el catálogo para añadir productos.</a></div>
    <?php else: ?><div class="vstack gap-3"><?php foreach ($items as $item): $product = $item['product']; $quantity = $item['quantity']; ?><article class="card border-0 shadow-sm"><div class="card-body"><div class="row g-3 align-items-center"><div class="col-4 col-md-3"><div class="product-media ratio ratio-1x1 rounded bg-body-tertiary"><?php if ($product->image !== ''): ?><img src="<?= escapeHtml($product->image) ?>" alt="<?= escapeHtml($product->name) ?>"><?php else: ?><div class="product-placeholder"><span class="placeholder-mark">＋</span></div><?php endif; ?></div></div><div class="col"><p class="small text-secondary mb-1"><?= escapeHtml($product->category) ?></p><h2 class="h5"><?= escapeHtml($product->name) ?></h2><strong class="text-primary"><?= formatPrice($product->price * $quantity) ?></strong><div class="d-flex align-items-center gap-2 mt-2"><span class="small text-secondary">Cantidad</span><?php foreach (['decrease' => '−', 'increase' => '+'] as $action => $label): ?><form method="post"><input type="hidden" name="action" value="<?= $action ?>"><input type="hidden" name="product_id" value="<?= escapeHtml($product->id) ?>"><button class="btn btn-outline-secondary btn-sm" type="submit" aria-label="<?= $action === 'increase' ? 'Añadir' : 'Quitar' ?> una unidad"><?= $label ?></button></form><?php endforeach; ?><span><?= $quantity ?></span><form class="ms-auto" method="post"><input type="hidden" name="action" value="remove"><input type="hidden" name="product_id" value="<?= escapeHtml($product->id) ?>"><button class="btn btn-link btn-sm" type="submit">Quitar</button></form></div></div></div></div></article><?php endforeach; ?></div><?php endif; ?>
  </section>
  <aside class="col-lg-5"><div class="card border-0 shadow-sm"><div class="card-body p-4"><h2 class="h5">Resumen y pago</h2><div class="d-flex justify-content-between py-3 border-top border-bottom my-3"><span>Subtotal</span><strong class="fs-5"><?= formatPrice($total) ?></strong></div><p class="form-text">El costo de envío y el monto final se confirman al completar el pedido.</p><form method="post"><input type="hidden" name="action" value="checkout"><fieldset class="mb-3"><legend class="fs-6 fw-semibold">Elige tu método de pago</legend>
    <label class="payment-option form-check border rounded p-3 mb-2"><input class="form-check-input" type="radio" name="payment" value="pago-movil" required><span><strong>Pago Móvil</strong><small class="d-block text-secondary">Desde bancos nacionales de Venezuela</small></span></label>
    <label class="payment-option form-check border rounded p-3 mb-2"><input class="form-check-input" type="radio" name="payment" value="tarjeta"><span><strong>Tarjeta internacional</strong><small class="d-block text-secondary">Visa o Mastercard</small></span></label>
    <label class="payment-option form-check border rounded p-3"><input class="form-check-input" type="radio" name="payment" value="paypal"><span><strong>PayPal</strong><small class="d-block text-secondary">Pago desde una cuenta PayPal</small></span></label>
  </fieldset><button class="btn btn-primary btn-lg w-100" type="submit" <?= !$items ? 'disabled' : '' ?>>Continuar con el pedido</button><?php if ($message !== ''): ?><p class="text-success small mt-3 mb-0" role="status"><?= escapeHtml($message) ?></p><?php endif; ?></form></div></div><p class="form-text mt-3">Los pagos reales requieren una pasarela y afiliación del comercio. Esta versión no procesa cobros.</p></aside></div>
</main>
<?php require __DIR__ . '/app/views/partials/footer.php'; ?>
