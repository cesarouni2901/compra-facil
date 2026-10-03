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
    <a class="brand fs-4 fw-bold text-decoration-none" href="index.html">Compra<span>Fácil</span></a>
    <nav class="d-flex align-items-center gap-2 flex-wrap" aria-label="Navegación principal">
      <a class="nav-link px-2" href="index.html">Inicio</a>
      <a class="nav-link px-2" href="catalogo.php">Productos</a>
      <?php if ($currentUser && $currentUser['role'] === 'vendedor'): ?><a class="nav-link px-2" href="producto-nuevo.php">Publicar producto</a><?php endif; ?>
      <a class="nav-link px-2" href="registro-cliente.php">Registro cliente</a>
      <a class="nav-link px-2" href="registro-vendedor.php">Registro vendedor</a>
      <?php if ($currentUser): ?><form class="d-inline" method="post" action="logout.php"><input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>"><button class="btn btn-outline-primary btn-sm" type="submit">Salir</button></form><?php else: ?><a class="nav-link px-2" href="login.php">Iniciar sesión</a><?php endif; ?>
      <a class="btn btn-primary btn-sm ms-md-2" href="carrito.php">Carrito <span class="badge text-bg-light ms-1"><?= (int) ($cartCount ?? 0) ?></span></a>
    </nav>
  </div>
</header>
