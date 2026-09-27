<?php
require_once __DIR__ . '/app/bootstrap.php';
$catalog = new ProductCatalog();
$cart = new ShoppingCart();
$message = (new RegistrationController())->submit('vendedor');
$pageTitle = 'Registro de vendedor';
$cartCount = $cart->getCount();
require __DIR__ . '/app/views/partials/header.php';
?>
<main class="account-page py-5"><div class="container"><div class="row justify-content-center"><div class="col-lg-8 col-xl-7"><div class="text-center mb-4"><span class="eyebrow">Vende con nosotros</span><h1 class="h2 mt-2">Registro de vendedor</h1><p class="text-secondary mb-0">Registra los datos básicos de tu negocio para comenzar a ofrecer tus productos.</p></div>
  <form class="account-card card border-0 shadow" method="post"><div class="account-card-heading text-center text-white py-3"><h2 class="h5 mb-0">Información del negocio</h2></div><div class="card-body p-4 p-md-5 row g-3">
    <div class="col-md-6"><label class="form-label fw-semibold" for="responsable">Nombre del responsable <span class="text-danger">*</span></label><input class="form-control" id="responsable" name="responsable" autocomplete="name" required minlength="2" placeholder="Tu nombre" value="<?= escapeHtml((string) ($_POST['responsable'] ?? '')) ?>"></div>
    <div class="col-md-6"><label class="form-label fw-semibold" for="negocio">Nombre del negocio <span class="text-danger">*</span></label><input class="form-control" id="negocio" name="negocio" required minlength="2" placeholder="Nombre de tu tienda" value="<?= escapeHtml((string) ($_POST['negocio'] ?? '')) ?>"></div>
    <div class="col-md-6"><label class="form-label fw-semibold" for="correo">Correo de contacto <span class="text-danger">*</span></label><input class="form-control" id="correo" name="correo" type="email" autocomplete="email" required placeholder="tienda@correo.com" value="<?= escapeHtml((string) ($_POST['correo'] ?? '')) ?>"></div>
    <div class="col-md-6"><label class="form-label fw-semibold" for="telefono">Teléfono <span class="text-danger">*</span></label><input class="form-control" id="telefono" name="telefono" type="tel" autocomplete="tel" required placeholder="Número de contacto" value="<?= escapeHtml((string) ($_POST['telefono'] ?? '')) ?>"></div>
    <div class="col-12"><label class="form-label fw-semibold" for="contrasena">Contraseña <span class="text-danger">*</span></label><input class="form-control" id="contrasena" name="contrasena" type="password" autocomplete="new-password" required minlength="6" placeholder="Mínimo 6 caracteres"></div>
    <div class="col-12 pt-2"><button class="btn btn-primary btn-lg w-100" type="submit">Crear cuenta de vendedor</button><?php if ($message !== ''): ?><p class="<?= str_starts_with($message, 'Formulario') ? 'text-success' : 'text-danger' ?> small mt-3 mb-0" role="status"><?= escapeHtml($message) ?></p><?php endif; ?></div>
  </div></form><p class="text-center small text-secondary mt-3">¿Quieres comprar? <a href="registro-cliente.php">Crea una cuenta de cliente</a>.</p>
</div></div></div></main>
<?php require __DIR__ . '/app/views/partials/footer.php'; ?>
