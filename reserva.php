<?php
declare(strict_types=1);
require __DIR__ . '/auth_require.php';
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reserva de cita · Clínica de Fisioterapia Plenium</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Tus estilos -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary" role="navigation" aria-label="Barra de navegación">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">Plenium</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain"
            aria-controls="navMain" aria-expanded="false" aria-label="Abrir menú de navegación">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
      </ul>
    </div>
  </div>
</nav>

<main class="container my-4">
  <div class="row g-3">
    <!-- Resumen del carrito -->
    <section class="col-lg-7">
      <div class="bg-white p-3 rounded-3 shadow-sm">
        <h1 class="h5 mb-3">Resumen de tu reserva</h1>
        <div id="resumenCarrito" aria-live="polite">
          <p class="text-muted mb-0">Cargando carrito…</p>
        </div>
      </div>
    </section>

    <!-- Datos del paciente -->
    <section class="col-lg-5">
      <div class="bg-white p-3 rounded-3 shadow-sm">
        <h2 class="h6 mb-3">Tus datos</h2>
        <form id="formReserva" method="post" action="reserva_procesar.php" novalidate>
          <input type="hidden" name="cart_json" id="cart_json">
          <div class="mb-2">
            <label for="nombre" class="form-label">Nombre y apellidos</label>
            <input id="nombre" name="nombre" class="form-control" required autocomplete="name">
            <div class="invalid-feedback">Indica tu nombre y apellidos.</div>
          </div>
          <div class="mb-2">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control" required autocomplete="email">
            <div class="invalid-feedback">Indica un email válido.</div>
          </div>
          <div class="mb-2">
            <label for="telefono" class="form-label">Teléfono (opcional)</label>
            <input id="telefono" name="telefono" class="form-control" inputmode="tel" autocomplete="tel">
          </div>

          <hr class="my-3">
          <h3 class="h6">Preferencia de cita</h3>
          <div class="row g-2">
            <div class="col-6">
              <label for="pref_fecha" class="form-label">Fecha</label>
              <input id="pref_fecha" name="pref_fecha" type="date" class="form-control" min="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-6">
              <label for="pref_hora" class="form-label">Hora</label>
              <select id="pref_hora" name="pref_hora" class="form-select">
                <option value="">— Cualquiera —</option>
                <option>09:00</option><option>10:00</option><option>11:00</option><option>12:00</option>
                <option>16:00</option><option>17:00</option><option>18:00</option><option>19:00</option>
              </select>
            </div>
          </div>

          <div class="d-grid mt-3">
            <button id="btnEnviar" class="btn btn-primary btn-lg" type="submit" disabled>Enviar solicitud</button>
          </div>
          <p class="small text-muted mt-2 mb-0">
            No se realiza pago ahora. Confirmaremos tu cita por email.
          </p>
          <div class="d-grid mt-3">
            <a href="index.php" class="btn btn-outline-secondary">← Volver al catálogo</a>
          </div>
        </form>
      </div>
    </section>
  </div>
</main>

<footer class="py-3 mt-4 bg-primary text-light">
  <div class="container small text-center">
    <p class="mb-1">© <?= date('Y') ?> Clínica de Fisioterapia Plenium</p>
    <p class="mb-0">info@plenium.com</p>
  </div>
</footer>

<!-- JS Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script mínimo: leer localStorage, pintar resumen, habilitar botón -->
<script>
const CLAVE_CARRITO = 'plenium_carrito';

function cargarCarrito(){
  try { return JSON.parse(localStorage.getItem(CLAVE_CARRITO)) || {}; }
  catch { return {}; }
}
function formatoEuro(n){
  return new Intl.NumberFormat('es-ES',{style:'currency',currency:'EUR'}).format(n);
}
function renderResumen(){
  const box   = document.getElementById('resumenCarrito');
  const hidden= document.getElementById('cart_json');
  const btn   = document.getElementById('btnEnviar');

  if (!box || !hidden || !btn) {
    console.error('Faltan elementos en el DOM', {box:!!box, hidden:!!hidden, btn:!!btn});
    return;
  }

  const carrito = cargarCarrito();
  const items = Object.entries(carrito);

  if (!items.length){
    box.innerHTML = '<p class="text-muted mb-0">Tu carrito está vacío. Vuelve al catálogo para añadir servicios.</p>';
    btn.disabled = true;
    hidden.value = '';
    return;
  }

  let total = 0;
  let filas = items.map(([id, it])=>{
    const sub = (it.cantidad||0) * (+it.precio||0);
    total += sub;
    return `
      <tr>
        <td><strong>${it.nombre}</strong><div class="small text-muted">x${it.cantidad||0}</div></td>
        <td class="text-end">${formatoEuro(it.precio)}</td>
        <td class="text-end">${formatoEuro(sub)}</td>
      </tr>`;
  }).join('');

  box.innerHTML = `
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Servicio</th>
            <th class="text-end" style="width:120px;">Precio</th>
            <th class="text-end" style="width:140px;">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          ${filas}
          <tr>
            <td class="text-end" colspan="2"><strong>Total</strong></td>
            <td class="text-end"><strong>${formatoEuro(total)}</strong></td>
          </tr>
        </tbody>
      </table>
    </div>`;
  hidden.value = JSON.stringify(carrito);
  btn.disabled = false;
}

// Validación de formulario (Bootstrap)
(() => {
  const form = document.getElementById('formReserva');
  if (!form) return;
  form.addEventListener('submit', (e)=>{
    if (!form.checkValidity()){
      e.preventDefault();
      e.stopPropagation();
    }
    form.classList.add('was-validated');
  });
})();

window.addEventListener('DOMContentLoaded', renderResumen);
</script>
</body>
</html>
