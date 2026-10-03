<?php
require_once __DIR__ . '/app/bootstrap.php';

// La página principal consulta la sesión y muestra la misma navegación en todo el sitio.
$currentUser = authenticatedUser();
$pageTitle = 'Inicio';
$cart = new ShoppingCart();
$cartCount = $cart->getCount();
require __DIR__ . '/app/views/partials/header.php';
?>
<main>
  <section class="hero-section">
    <div class="container-xl py-5 py-lg-6">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <span class="eyebrow">Compra fácil, elige mejor</span>
          <?php if ($currentUser): ?>
          <h1 class="display-4 fw-bold mt-3">Hola, <?= escapeHtml((string) ($currentUser['username'] ?? $currentUser['display_name'] ?? 'usuario')) ?>.</h1>
          <p class="lead text-secondary mt-3 mb-4">Has iniciado sesión como <?= $currentUser['role'] === 'vendedor' ? 'vendedor' : 'cliente' ?>. Explora productos y continúa con tu cuenta activa.</p>
          <?php else: ?>
          <h1 class="display-4 fw-bold mt-3">Encuentra lo que necesitas, a tu manera.</h1>
          <p class="lead text-secondary mt-3 mb-4">Explora productos de distintas categorías y encuentra una opción que se adapte a ti.</p>
          <?php endif; ?>
          <a class="btn btn-primary btn-lg" href="catalogo.php">Explorar productos</a>
        </div>
        <div class="col-lg-5">
          <div class="hero-card p-4 p-lg-5">
            <span class="hero-card-icon" aria-hidden="true">✦</span>
            <h2 class="h4 mt-4"><?= $currentUser ? 'Tu cuenta está activa' : 'Una experiencia sencilla' ?></h2>
            <p class="text-secondary mb-0"><?= $currentUser ? 'Usa el icono de cuenta en la esquina superior derecha para editar tus datos o cerrar sesión.' : 'Busca productos, revisa sus características y opiniones, y agrégalos al carrito.' ?></p>
            <div class="hero-decoration" aria-hidden="true">✓</div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="container-xl py-5">
    <div class="row row-cols-1 row-cols-md-3 g-3" aria-label="Cómo comprar">
      <div class="col"><article class="card h-100 border-0 shadow-sm"><div class="card-body"><span class="badge rounded-pill text-bg-primary mb-2">1</span><h2 class="h5">Explora</h2><p class="text-secondary mb-0">Revisa los productos disponibles en el catálogo.</p></div></article></div>
      <div class="col"><article class="card h-100 border-0 shadow-sm"><div class="card-body"><span class="badge rounded-pill text-bg-primary mb-2">2</span><h2 class="h5">Elige</h2><p class="text-secondary mb-0">Encuentra una opción que se adapte a ti.</p></div></article></div>
      <div class="col"><article class="card h-100 border-0 shadow-sm"><div class="card-body"><span class="badge rounded-pill text-bg-primary mb-2">3</span><h2 class="h5">Compra</h2><p class="text-secondary mb-0">Agrega productos al carrito y revisa tu pedido.</p></div></article></div>
    </div>
  </section>
</main>
<?php require __DIR__ . '/app/views/partials/footer.php'; ?>
