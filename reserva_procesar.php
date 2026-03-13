<?php

declare(strict_types=1);

require __DIR__ . '/auth_require.php'; // exige sesión (y hace session_start)
header('Content-Type: text/html; charset=utf-8');

// 0) Conexión a BD
require __DIR__ . '/config/db.php';
if (!isset($pdo) || !$pdo instanceof PDO) {
  http_response_code(500);
  echo '<!doctype html><meta charset="utf-8"><title>Error</title>';
  echo '<div style="font-family:system-ui;padding:24px">';
  echo '<h1>Error de conexión</h1><p>No hay conexión a la base de datos.</p>';
  echo '</div>';
  exit;
}


// Usuario logueado (para asociar pedido)
$usuario_id = $_SESSION['usuario']['id'] ?? null;

// ----------------- Helpers -----------------
function txt(string $s): string { return trim($s); }
function okEmail(string $e): bool { return (bool) filter_var($e, FILTER_VALIDATE_EMAIL); }
function okFecha(?string $f): bool {
  if ($f === null || $f === '') return true;
  $d = DateTime::createFromFormat('Y-m-d', $f);
  return $d && $d->format('Y-m-d') === $f;
}
function okHora(?string $h): bool {
  if ($h === null || $h === '') return true;
  $d = DateTime::createFromFormat('H:i', $h);
  return $d && $d->format('H:i') === $h;
}

// ----------------- 1) Recoger POST -----------------
$nombre     = txt($_POST['nombre']  ?? '');
$email      = txt($_POST['email']   ?? '');
$telefono   = txt($_POST['telefono']?? '');
$pref_fecha = $_POST['pref_fecha']  ?? '';
$pref_hora  = $_POST['pref_hora']   ?? '';
$cart_json  = $_POST['cart_json']   ?? '';

$errores = [];
if ($nombre === '')           $errores[] = 'El nombre es obligatorio.';
if (!okEmail($email))         $errores[] = 'El email no es válido.';
if (!okFecha($pref_fecha))    $errores[] = 'La fecha de preferencia no es válida (AAAA-MM-DD).';
if (!okHora($pref_hora))      $errores[] = 'La hora de preferencia no es válida (HH:MM).';

if ($pref_fecha) {
  $hoy = (new DateTime('today'))->format('Y-m-d');
  if ($pref_fecha < $hoy) $errores[] = 'La fecha de preferencia no puede ser pasada.';
}

// Carrito
$carrito = json_decode($cart_json, true);
if (!is_array($carrito) || !count($carrito)) {
  $errores[] = 'El carrito está vacío o es inválido.';
}

// Si hay errores → responder y salir (HTML claro)
if ($errores) {
  http_response_code(422);
  ?>
  <!doctype html>
  <html lang="es">
  <head>
    <meta charset="utf-8">
    <title>Reserva — errores</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php">Plenium</a>
    </div>
  </nav>
  <main class="container my-4">
    <div class="bg-white p-4 rounded-3 shadow-sm">
      <h1 class="h5 mb-3">No se pudo enviar la reserva</h1>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errores as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <a class="btn btn-secondary" href="reserva.php">Volver a la reserva</a>
    </div>
  </main>
  </body>
  </html>
  <?php
  exit;
}

// ----------------- 2) Validar servicios contra BD -----------------
try {
  $slugs = array_keys($carrito); // IDs/slug del front (deben coincidir con servicios.slug)
  $placeholders = implode(',', array_fill(0, count($slugs), '?'));

  $sql = "SELECT slug, nombre, precio FROM servicios
          WHERE activo=1 AND slug IN ($placeholders)";
  $st  = $pdo->prepare($sql);
  $st->execute($slugs);

  $servBD = [];
  while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
    $servBD[$r['slug']] = [
      'nombre' => $r['nombre'],
      'precio' => (float)$r['precio'],
    ];
  }

  // asegurar que todos existen
  foreach ($slugs as $slug) {
    if (!isset($servBD[$slug])) {
      throw new RuntimeException("El servicio '$slug' no existe o está inactivo.");
    }
  }

  // calcular líneas y total con precios de BD
  $total  = 0.0;
  $lineas = [];
  foreach ($carrito as $slug => $item) {
    $qty        = max(1, (int)($item['cantidad'] ?? 0));
    $precio     = $servBD[$slug]['precio'];
    $nombreSrv  = $servBD[$slug]['nombre'];
    $subtotal   = $qty * $precio;
    $total     += $subtotal;
    $lineas[]   = [
      'slug'   => $slug,
      'nombre' => $nombreSrv,
      'precio' => $precio,
      'qty'    => $qty,
      'sub'    => $subtotal
    ];
  }

  // ----------------- 3) Insertar en BD (transacción) -----------------
  $pdo->beginTransaction();

  // Insert cabecera (ahora con usuario_id)
  $insP = $pdo->prepare("
    INSERT INTO pedidos (usuario_id, nombre, email, telefono, pref_fecha, pref_hora, total, estado, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente', NOW())
  ");
  $insP->execute([
    $usuario_id,                // puede ser null si no quieres obligar
    $nombre,
    $email,
    $telefono ?: null,
    $pref_fecha ?: null,
    $pref_hora ?: null,
    $total
  ]);
  $pedido_id = (int)$pdo->lastInsertId();

  // Insert líneas
  $insL = $pdo->prepare("
    INSERT INTO pedido_linea (pedido_id, slug, nombre, precio, qty)
    VALUES (?, ?, ?, ?, ?)
  ");
  foreach ($lineas as $l) {
    $insL->execute([$pedido_id, $l['slug'], $l['nombre'], $l['precio'], $l['qty']]);
  }

  $pdo->commit();

} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  http_response_code(500);
  echo '<!doctype html><meta charset="utf-8"><title>Error</title>';
  echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">';
  echo '<div class="container my-4"><div class="alert alert-danger">';
  echo '<h1 class="h5">No se pudo completar la reserva</h1>';
  echo '<p class="mb-0">'.htmlspecialchars($e->getMessage()).'</p>';
  echo '</div><a class="btn btn-secondary" href="reserva.php">Volver</a></div>';
  exit;
}

// ----------------- 4) Respuesta de confirmación -----------------
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reserva enviada · Clínica de Fisioterapia Plenium</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">Plenium</a>
  </div>
</nav>

<main class="container my-4">
  <div class="bg-white p-4 rounded-3 shadow-sm">
    <h1 class="h4 mb-3">¡Gracias, <?= htmlspecialchars($nombre) ?>!</h1>
    <p class="mb-3">Tu solicitud ha sido enviada con el número <strong>#<?= $pedido_id ?></strong>.</p>

    <h2 class="h6">Resumen</h2>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Servicio</th>
            <th class="text-end" style="width:120px;">Precio</th>
            <th class="text-center" style="width:90px;">Cant.</th>
            <th class="text-end" style="width:140px;">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($lineas as $l): ?>
            <tr>
              <td><?= htmlspecialchars($l['nombre']) ?></td>
              <td class="text-end"><?= number_format($l['precio'], 2, ',', '.') ?> €</td>
              <td class="text-center"><?= (int)$l['qty'] ?></td>
              <td class="text-end"><?= number_format($l['sub'], 2, ',', '.') ?> €</td>
            </tr>
          <?php endforeach; ?>
          <tr>
            <td colspan="3" class="text-end"><strong>Total</strong></td>
            <td class="text-end"><strong><?= number_format($total, 2, ',', '.') ?> €</strong></td>
          </tr>
        </tbody>
      </table>
    </div>

    <h3 class="h6 mt-4">Tus preferencias</h3>
    <ul class="mb-3">
      <li><strong>Fecha preferida:</strong> <?= $pref_fecha ? htmlspecialchars($pref_fecha) : 'Cualquiera' ?></li>
      <li><strong>Hora preferida:</strong> <?= $pref_hora ? htmlspecialchars($pref_hora) : 'Cualquiera' ?></li>
      <li><strong>Contacto:</strong> <?= htmlspecialchars($email) ?><?= $telefono ? ' · '.htmlspecialchars($telefono) : '' ?></li>
    </ul>

    <div class="alert alert-info">
      Te enviaremos la confirmación de la cita por correo electrónico. Si necesitas modificar algo, contáctanos.
    </div>

    <div class="d-flex gap-2">
      <a class="btn btn-primary" href="index.php">Volver al inicio</a>
      <a class="btn btn-outline-secondary" href="reserva.php">Hacer otra reserva</a>
    </div>
  </div>
</main>

<footer class="py-3 mt-4 bg-primary text-light">
  <div class="container small text-center">
    <p class="mb-1">© <?= date('Y') ?> Clínica de Fisioterapia Plenium</p>
    <p class="mb-0">info@plenium.com</p>
  </div>
</footer>

<script>
// Vaciar carrito al confirmar (clave usada en app.js y reserva.php)
try { localStorage.removeItem('plenium_carrito'); } catch (e) {}
</script>
</body>
</html>
