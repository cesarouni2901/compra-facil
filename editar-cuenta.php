<?php
require_once __DIR__ . '/app/bootstrap.php';

// El controlador valida cambios y el repositorio lee/escribe el perfil cifrado.
$controller = new AccountController(new UserRepository(applicationSecurity()));
$currentUser = authenticatedUser();
$message = '';
if (!$currentUser) {
    header('Location: login.php');
    exit;
}

try {
    ensurePrivateStorage();
    $message = $controller->updateProfile();
    $profile = $controller->profile();
} catch (RuntimeException $exception) {
    $profile = null;
    $message = 'No se pudieron cargar los datos de la cuenta. ' . $exception->getMessage();
}

$pageTitle = 'Editar mis datos';
$cart = new ShoppingCart();
$cartCount = $cart->getCount();
require __DIR__ . '/app/views/partials/header.php';
?>
<main class="account-page py-5">
  <div class="container"><div class="row justify-content-center"><div class="col-lg-8 col-xl-7">
    <div class="text-center mb-4"><span class="eyebrow">Mi cuenta</span><h1 class="h2 mt-2">Editar mis datos</h1><p class="text-secondary">Actualiza la información de tu cuenta de <?= $currentUser['role'] === 'vendedor' ? 'vendedor' : 'cliente' ?>.</p></div>
    <?php if (!$profile): ?><div class="alert alert-danger" role="status"><?= escapeHtml($message !== '' ? $message : 'No se encontró el perfil.') ?></div>
    <?php else: ?>
    <!-- El formulario envía cambios al método updateProfile del controlador de cuentas. -->
    <form class="account-card card border-0 shadow" method="post"><div class="account-card-heading text-center text-white py-3"><h2 class="h5 mb-0">Información de la cuenta</h2></div><div class="card-body p-4 p-md-5 row g-3">
      <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
      <div class="col-md-6"><label class="form-label fw-semibold" for="nombre">Nombre *</label><input class="form-control" id="nombre" name="nombre" data-filtro="letras" required minlength="2" maxlength="60" pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]+" value="<?= escapeHtml($profile['first_name']) ?>"></div>
      <div class="col-md-6"><label class="form-label fw-semibold" for="apellido">Apellido *</label><input class="form-control" id="apellido" name="apellido" data-filtro="letras" required minlength="2" maxlength="60" pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]+" value="<?= escapeHtml($profile['last_name']) ?>"></div>
      <div class="col-md-6"><label class="form-label fw-semibold" for="usuario">Nombre de usuario *</label><input class="form-control" id="usuario" name="usuario" autocomplete="username" required minlength="3" maxlength="20" pattern="[A-Za-z0-9_.-]{3,20}" value="<?= escapeHtml($profile['username']) ?>"></div>
      <div class="col-md-6"><label class="form-label fw-semibold" for="documento">Cédula *</label><input class="form-control" id="documento" name="documento" data-filtro="numeros" inputmode="numeric" required minlength="5" maxlength="20" pattern="[0-9]{5,20}" value="<?= escapeHtml($profile['document']) ?>"></div>
      <div class="col-md-6"><label class="form-label fw-semibold" for="correo">Correo electrónico *</label><input class="form-control" id="correo" name="correo" type="email" autocomplete="email" required value="<?= escapeHtml($profile['email']) ?>"></div>
      <div class="col-md-6"><label class="form-label fw-semibold" for="telefono">Teléfono<?= $profile['role'] === 'vendedor' ? ' *' : '' ?></label><input class="form-control" id="telefono" name="telefono" data-filtro="numeros" inputmode="numeric" minlength="7" maxlength="15" pattern="[0-9]{7,15}" <?= $profile['role'] === 'vendedor' ? 'required' : '' ?> value="<?= escapeHtml($profile['phone']) ?>"></div>
      <?php if ($profile['role'] === 'vendedor'): ?><div class="col-12"><label class="form-label fw-semibold" for="negocio">Nombre del negocio *</label><input class="form-control" id="negocio" name="negocio" required minlength="2" maxlength="100" value="<?= escapeHtml($profile['business']) ?>"></div><?php endif; ?>
      <div class="col-12 pt-2"><button class="btn btn-primary btn-lg w-100" type="submit">Guardar cambios</button><?php if ($message !== ''): ?><p class="<?= str_starts_with($message, 'Datos actualizados') ? 'text-success' : 'text-danger' ?> small mt-3 mb-0" role="status"><?= escapeHtml($message) ?></p><?php endif; ?></div>
    </div></form><?php endif; ?>
  </div></div></div>
</main>
<script src="js/validacion-registro.js" defer></script>
<?php require __DIR__ . '/app/views/partials/footer.php'; ?>
