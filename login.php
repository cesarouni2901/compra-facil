<?php
require_once __DIR__ . '/app/bootstrap.php';
$cart = new ShoppingCart();
$message = '';
try {
    ensurePrivateStorage();
    $message = (new AccountController(new UserRepository(applicationSecurity())))->login();
} catch (RuntimeException $exception) {
    $message = 'No se pudo preparar el almacenamiento privado de cuentas. ' . $exception->getMessage();
}
$pageTitle = 'Iniciar sesión';
$cartCount = $cart->getCount();
require __DIR__ . '/app/views/partials/header.php';
?>
<main class="account-page py-5"><div class="container"><div class="row justify-content-center"><div class="col-md-8 col-lg-6"><div class="text-center mb-4"><span class="eyebrow">Bienvenido</span><h1 class="h2 mt-2">Iniciar sesión</h1><p class="text-secondary">Ingresa con la cuenta que registraste.</p></div>
  <form class="account-card card border-0 shadow" method="post"><div class="account-card-heading text-center text-white py-3"><h2 class="h5 mb-0">Acceso a tu cuenta</h2></div><div class="card-body p-4 p-md-5">
    <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
    <!-- Puede ingresar el usuario nuevo o el correo de una cuenta existente. -->
    <div class="mb-3"><label class="form-label fw-semibold" for="identidad">Usuario o correo electrónico</label><input class="form-control" id="identidad" name="identidad" type="text" autocomplete="username" required value="<?= escapeHtml((string) ($_POST['identidad'] ?? '')) ?>"></div>
    <div class="mb-4"><label class="form-label fw-semibold" for="contrasena">Clave</label><input class="form-control" id="contrasena" name="contrasena" type="password" autocomplete="current-password" required></div>
    <button class="btn btn-primary btn-lg w-100" type="submit">Iniciar sesión</button>
    <?php if ($message !== ''): ?><p class="text-danger small mt-3 mb-0" role="status"><?= escapeHtml($message) ?></p><?php endif; ?>
  </div></form><p class="text-center small text-secondary mt-3">¿No tienes cuenta? <a href="registro-cliente.php">Regístrate como cliente</a> o <a href="registro-vendedor.php">como vendedor</a>.</p>
</div></div></div></main>
<?php require __DIR__ . '/app/views/partials/footer.php'; ?>
