<?php
require_once __DIR__ . '/app/bootstrap.php';
$catalog = new ProductCatalog();
$cart = new ShoppingCart();
$message = '';
try {
    ensurePrivateStorage();
    $message = (new AccountController(new UserRepository(applicationSecurity())))->register('cliente');
} catch (RuntimeException $exception) {
    $message = 'No se pudo preparar el almacenamiento privado de cuentas. Revisa los permisos de app/ y app/private/. ' . $exception->getMessage();
}
$pageTitle = 'Registro de cliente';
$cartCount = $cart->getCount();
require __DIR__ . '/app/views/partials/header.php';
?>
<main class="account-page py-5"><div class="container"><div class="row justify-content-center"><div class="col-lg-8 col-xl-7"><div class="text-center mb-4"><span class="eyebrow">Tu cuenta</span><h1 class="h2 mt-2">Registro de cliente</h1><p class="text-secondary mb-0">Crea tu cuenta para guardar tus preferencias y agilizar tus próximas compras.</p></div>
  <form class="account-card card border-0 shadow" method="post"><div class="account-card-heading text-center text-white py-3"><h2 class="h5 mb-0">Información de cliente</h2></div><div class="card-body p-4 p-md-5 row g-3">
    <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
    <div class="col-md-6"><label class="form-label fw-semibold" for="nombre">Nombre <span class="text-danger">*</span></label><input class="form-control" id="nombre" name="nombre" autocomplete="given-name" required minlength="2" maxlength="60" pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]+" title="Escribe solo letras y espacios." placeholder="Tu nombre" value="<?= escapeHtml((string) ($_POST['nombre'] ?? '')) ?>"></div>
    <div class="col-md-6"><label class="form-label fw-semibold" for="apellido">Apellido <span class="text-danger">*</span></label><input class="form-control" id="apellido" name="apellido" autocomplete="family-name" required minlength="2" maxlength="60" pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]+" title="Escribe solo letras y espacios." placeholder="Tu apellido" value="<?= escapeHtml((string) ($_POST['apellido'] ?? '')) ?>"></div>
    <div class="col-md-6"><label class="form-label fw-semibold" for="documento">Cédula o documento <span class="text-danger">*</span></label><input class="form-control" id="documento" name="documento" type="text" inputmode="numeric" autocomplete="off" required minlength="5" maxlength="20" pattern="[0-9]{5,20}" title="Escribe solo números (5 a 20 dígitos)." placeholder="Solo números" value="<?= escapeHtml((string) ($_POST['documento'] ?? '')) ?>"></div>
    <div class="col-md-6"><label class="form-label fw-semibold" for="correo">Correo electrónico <span class="text-danger">*</span></label><input class="form-control" id="correo" name="correo" type="email" autocomplete="email" required placeholder="nombre@correo.com" value="<?= escapeHtml((string) ($_POST['correo'] ?? '')) ?>"></div>
    <div class="col-md-6"><label class="form-label fw-semibold" for="fecha_nacimiento">Fecha de nacimiento <span class="text-danger">*</span></label><input class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" type="date" max="<?= date('Y-m-d', strtotime('-18 years')) ?>" required value="<?= escapeHtml((string) ($_POST['fecha_nacimiento'] ?? '')) ?>"><small class="form-text">Debes tener al menos 18 años para comprar.</small></div>
    <div class="col-md-6"><label class="form-label fw-semibold" for="telefono">Teléfono</label><input class="form-control" id="telefono" name="telefono" type="tel" autocomplete="tel" maxlength="30" placeholder="Opcional" value="<?= escapeHtml((string) ($_POST['telefono'] ?? '')) ?>"></div>
    <div class="col-md-6"><label class="form-label fw-semibold" for="contrasena">Clave <span class="text-danger">*</span></label><input class="form-control" id="contrasena" name="contrasena" type="password" autocomplete="new-password" required minlength="8" maxlength="12" pattern="(?=.*[A-ZÁÉÍÓÚÜÑ])(?=.*[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ0-9]).{8,12}" title="De 8 a 12 caracteres, una mayúscula y un carácter especial."><small class="form-text">8–12 caracteres, mínimo una mayúscula y un símbolo.</small></div>
    <div class="col-md-6"><label class="form-label fw-semibold" for="confirmacion">Confirmar clave <span class="text-danger">*</span></label><input class="form-control" id="confirmacion" name="confirmacion" type="password" autocomplete="new-password" required minlength="8" maxlength="12"></div>
    <div class="col-12 pt-2"><button class="btn btn-primary btn-lg w-100" type="submit">Crear cuenta de cliente</button><?php if ($message !== ''): ?><p class="<?= str_starts_with($message, 'Cuenta creada') ? 'text-success' : 'text-danger' ?> small mt-3 mb-0" role="status"><?= escapeHtml($message) ?></p><?php endif; ?></div>
  </div></form><p class="text-center small text-secondary mt-3">¿Tienes una tienda? <a href="registro-vendedor.php">Regístrala aquí</a>. ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>.</p>
</div></div></div></main>
<?php require __DIR__ . '/app/views/partials/footer.php'; ?>
