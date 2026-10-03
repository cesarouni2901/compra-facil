<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Explora productos, aplica filtros y encuentra lo que necesitas.">
  <title><?= escapeHtml($pageTitle) ?> | CompraFácil</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<?php $currentUser = authenticatedUser(); ?>
<header class="site-header bg-white border-bottom sticky-top">
  <div class="container-xl py-3 d-flex justify-content-between align-items-center gap-3 flex-wrap">
    <a class="brand fs-4 fw-bold text-decoration-none" href="index.php">Compra<span>Fácil</span></a>
    <nav class="d-flex align-items-center gap-2 flex-wrap" aria-label="Navegación principal">
      <a class="nav-link px-2" href="index.php">Inicio</a>
      <a class="nav-link px-2" href="catalogo.php">Productos</a>
      <?php if ($currentUser && $currentUser['role'] === 'vendedor'): ?><a class="nav-link px-2" href="producto-nuevo.php">Publicar producto</a><?php endif; ?>
      <?php if (!$currentUser): ?><a class="nav-link px-2" href="registro-cliente.php">Registro cliente</a><a class="nav-link px-2" href="registro-vendedor.php">Registro vendedor</a><a class="nav-link px-2" href="login.php">Iniciar sesión</a><?php endif; ?>
      <a class="btn btn-primary btn-sm ms-md-2" href="carrito.php">Carrito <span class="badge text-bg-light ms-1"><?= (int) ($cartCount ?? 0) ?></span></a>
      <?php if ($currentUser): ?>
      <!-- Icono discreto a la derecha; sus opciones conservan el rol y sesión activa. -->
      <details class="account-menu">
        <summary aria-label="Opciones de cuenta" title="Opciones de cuenta"><span class="account-avatar" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm0 1c-3.314 0-6 1.567-6 3.5V14h12v-1.5C14 10.567 11.314 9 8 9Z"/></svg></span></summary>
        <div class="account-menu-panel shadow">
          <strong><?= escapeHtml((string) ($currentUser['username'] ?? $currentUser['display_name'] ?? 'Usuario')) ?></strong>
          <span class="small text-secondary d-block mb-2"><?= $currentUser['role'] === 'vendedor' ? 'Cuenta de vendedor' : 'Cuenta de cliente' ?></span>
          <a class="btn btn-outline-primary btn-sm w-100 mb-2" href="editar-cuenta.php">Editar mis datos</a>
          <form method="post" action="logout.php"><input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>"><button class="btn btn-outline-secondary btn-sm w-100" type="submit">Cerrar sesión</button></form>
        </div>
      </details>
      <?php endif; ?>
    </nav>
  </div>
</header>
