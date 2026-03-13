<?php
declare(strict_types=1);
session_start();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Clínica de Fisioterapia Plenium</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Estilos propios -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<a class="skip-link" href="#contenido">Saltar al contenido principal</a>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary" role="navigation" aria-label="Barra de navegación principal">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php" aria-label="Inicio Clínica de Fisioterapia Plenium">
      Plenium
    </a>
    <img src="img/logo.png" alt="Logotipo de Clínica de Fisioterapia Plenium"
           class="logo-hero">
    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#navMain"
            aria-controls="navMain" aria-expanded="false"
            aria-label="Abrir menú de navegación">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link" href="index.php" aria-current="page">Inicio</a></li>
        <li class="nav-item">
          <button class="btn btn-outline-light btn-sm ms-lg-3"
                  data-bs-toggle="offcanvas" data-bs-target="#offCart"
                  aria-controls="offCart" aria-label="Abrir carrito">
            Carrito <span class="badge bg-light text-dark ms-1" id="cart-count">0</span>
          </button>
        </li>
        <li class="nav-item ms-lg-2"><a class="nav-link" href="reserva.php">Reservar</a></li>

       <?php if (isset($_SESSION['usuario'])): ?>
  <?php if (($_SESSION['usuario']['rol'] ?? 'paciente') === 'admin'): ?>
    <li class="nav-item ms-lg-2">
      <a class="nav-link" href="admin_pedidos.php">Panel</a>
    </li>
  <?php endif; ?>
  <li class="nav-item ms-lg-3">
    <span class="navbar-text text-light small">
      Hola, <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?>
    </span>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="logout.php">Salir</a>
  </li>
<?php else: ?>
  <li class="nav-item ms-lg-2">
    <a class="nav-link" href="login.php">Iniciar sesión</a>
  </li>
  <li class="nav-item ms-lg-2">
    <a class="nav-link" href="register.php">Registrarse</a>
  </li>
<?php endif; ?>

      </ul>
    </div>
  </div>
</nav>

<!-- HERO -->
<header class="container my-4" role="banner">
  <div class="hero" aria-labelledby="hero-title" aria-describedby="hero-desc">
    <div class="content">
      <img src="img/logo.png" alt="Logotipo de Clínica de Fisioterapia Plenium"
           class="logo-hero">
      <h1 id="hero-title">Clínica de Fisioterapia Plenium</h1>
      <p id="hero-desc">Añade tus sesiones al carrito y solicita cita de forma sencilla.</p>
      <p class="tagline">Equilibramos tu salud para una vida en plenitud</p>
    </div>
  </div>
</header>

<!-- CONTENIDO -->
<main id="contenido" class="container mb-5" role="main">
  <h2 class="visually-hidden">Catálogo de servicios</h2>
  <div id="grid" class="row g-3"><!-- Tarjetas desde js/app.js --></div>
</main>

<!-- OFFCANVAS CARRITO -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offCart" aria-labelledby="offCartLabel" aria-modal="true" role="dialog">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offCartLabel">Tu carrito</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar carrito"></button>
  </div>
  <div class="offcanvas-body d-flex flex-column">
    <div id="cartBox" class="flex-grow-1" aria-live="polite">
      <p class="text-muted mb-0">Tu carrito está vacío.</p>
    </div>
    <div class="border-top pt-2 mt-2">
      <div class="d-flex justify-content-between align-items-center">
        <strong>Total</strong>
        <strong id="cartTotal">0,00 €</strong>
      </div>
      <div class="d-grid mt-2">
        <a id="btnCheckout" href="reserva.php" class="btn btn-primary" aria-disabled="true">Ir a reserva</a>
      </div>
    </div>
  </div>
</div>

<!-- FOOTER -->
<footer class="py-3 mt-4 bg-primary text-light" role="contentinfo">
  <div class="container small text-center">
    <p class="mb-1">© <?= date('Y') ?> Clínica de Fisioterapia Plenium</p>
    <p class="mb-0">info@plenium.com</p>
  </div>
</footer>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/app.js"></script>
</body>
</html>
