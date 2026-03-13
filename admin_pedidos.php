<?php
declare(strict_types=1);

// 1) Sesión + exigir login
require __DIR__ . '/auth_require.php';

// 2) Exigir rol admin
if (($_SESSION['usuario']['rol'] ?? 'paciente') !== 'admin') {
  http_response_code(403);
  echo '<!doctype html><meta charset="utf-8"><title>Prohibido</title>';
  echo '<div style="font-family:system-ui;padding:24px">';
  echo '<h1>Acceso denegado</h1><p>Esta zona es solo para administradores.</p>';
  echo '<p><a href="index.php">Volver al inicio</a></p></div>';
  exit;
}

// 3) BD
require __DIR__ . '/config/db.php';
if (!isset($pdo) || !$pdo instanceof PDO) {
  http_response_code(500);
  echo '<!doctype html><meta charset="utf-8"><title>Error</title>';
  echo '<div style="font-family:system-ui;padding:24px">';
  echo '<h1>Error de conexión</h1><p>No hay conexión a la base de datos.</p>';
  echo '</div>';
  exit;
}

// 4) CSRF token
if (empty($_SESSION['csrf_admin'])) {
  $_SESSION['csrf_admin'] = bin2hex(random_bytes(16));
}
$CSRF = $_SESSION['csrf_admin'];

// 5) Acciones (POST): confirmar/cancelar
$flash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $token = $_POST['csrf'] ?? '';
  if (!hash_equals($CSRF, $token)) {
    $flash = 'Token CSRF inválido.';
  } else {
    $accion = $_POST['accion'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
      $flash = 'ID de pedido inválido.';
    } else {
      try {
        if ($accion === 'confirmar') {
          $st = $pdo->prepare("UPDATE pedidos SET estado='confirmado' WHERE id=?");
          $st->execute([$id]);
          $flash = "Pedido #$id confirmado.";
        } elseif ($accion === 'cancelar') {
          $st = $pdo->prepare("UPDATE pedidos SET estado='cancelado' WHERE id=?");
          $st->execute([$id]);
          $flash = "Pedido #$id cancelado.";
        } else {
          $flash = 'Acción no reconocida.';
        }
      } catch (Throwable $e) {
        $flash = 'Error al actualizar: ' . $e->getMessage();
      }
    }
  }
}

// 6) Filtros de listado (GET)
$estado = $_GET['estado'] ?? '';
$busca  = trim($_GET['q'] ?? '');

// 7) Query de pedidos
$params = [];
$where  = [];

if (in_array($estado, ['pendiente','confirmado','cancelado'], true)) {
  $where[] = 'p.estado = ?';
  $params[] = $estado;
}
if ($busca !== '') {
  // Busca en id, nombre y email
  $where[] = '(p.id = ? OR p.nombre LIKE ? OR p.email LIKE ?)';
  $params[] = ctype_digit($busca) ? (int)$busca : 0;
  $params[] = '%'.$busca.'%';
  $params[] = '%'.$busca.'%';
}

$sql = "SELECT p.*, u.nombre AS usuario_nombre, u.email AS usuario_email
        FROM pedidos p
        LEFT JOIN usuarios u ON u.id = p.usuario_id";
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY p.created_at DESC, p.id DESC LIMIT 100';

$st = $pdo->prepare($sql);
$st->execute($params);
$pedidos = $st->fetchAll();

// Helper precios
function eur(float $n): string {
  return number_format($n, 2, ',', '.') . ' €';
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Panel · Pedidos · Plenium</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .estado-badge { text-transform: capitalize; }
  </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">Plenium</a>
    <div class="ms-auto navbar-text text-light small">
      Admin: <?= htmlspecialchars($_SESSION['usuario']['nombre'] ?? ''); ?>
      · <a class="link-light text-decoration-none" href="logout.php">Salir</a>
    </div>
  </div>
</nav>

<main class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Pedidos (últimos 100)</h1>
    <a class="btn btn-outline-secondary btn-sm" href="admin_pedidos.php">Limpiar filtros</a>
  </div>

  <?php if ($flash): ?>
    <div class="alert alert-info"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <!-- Filtros -->
  <form class="row g-2 bg-white p-3 rounded-3 shadow-sm mb-3" method="get" role="search" aria-label="Filtrar pedidos">
    <div class="col-sm-4 col-md-3">
      <label for="estado" class="form-label">Estado</label>
      <select id="estado" name="estado" class="form-select">
        <option value="">— Todos —</option>
        <option value="pendiente"   <?= $estado==='pendiente'?'selected':''; ?>>Pendiente</option>
        <option value="confirmado"  <?= $estado==='confirmado'?'selected':''; ?>>Confirmado</option>
        <option value="cancelado"   <?= $estado==='cancelado'?'selected':''; ?>>Cancelado</option>
      </select>
    </div>
    <div class="col-sm-8 col-md-6">
      <label for="q" class="form-label">Buscar (ID, nombre o email)</label>
      <input id="q" name="q" class="form-control" value="<?= htmlspecialchars($busca) ?>" placeholder="Ej: 25 · Ana · ana@mail.com">
    </div>
    <div class="col-md-3 d-flex align-items-end">
      <button class="btn btn-primary w-100">Aplicar</button>
    </div>
  </form>

  <!-- Listado -->
  <div class="bg-white p-3 rounded-3 shadow-sm">
    <?php if (!$pedidos): ?>
      <p class="text-muted mb-0">No hay pedidos que coincidan con el filtro.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Fecha</th>
              <th>Paciente</th>
              <th>Email</th>
              <th>Preferencia</th>
              <th class="text-end">Total</th>
              <th>Estado</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pedidos as $p): ?>
              <tr>
                <td>#<?= (int)$p['id'] ?></td>
                <td>
                  <div><?= htmlspecialchars($p['created_at']) ?></div>
                  <?php if (!empty($p['usuario_nombre'])): ?>
                    <div class="small text-muted">Usuario: <?= htmlspecialchars($p['usuario_nombre']) ?></div>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($p['nombre']) ?></td>
                <td><?= htmlspecialchars($p['email']) ?></td>
                <td class="small">
                  <?= $p['pref_fecha'] ? htmlspecialchars($p['pref_fecha']) : '—' ?>
                  <?= $p['pref_hora']  ? ' · '.htmlspecialchars($p['pref_hora']) : '' ?>
                </td>
                <td class="text-end"><strong><?= eur((float)$p['total']) ?></strong></td>
                <td>
                  <?php
                    $badge = 'secondary';
                    if ($p['estado']==='pendiente')  $badge = 'warning';
                    if ($p['estado']==='confirmado') $badge = 'success';
                    if ($p['estado']==='cancelado')  $badge = 'danger';
                  ?>
                  <span class="badge estado-badge bg-<?= $badge ?>">
                    <?= htmlspecialchars($p['estado']) ?>
                  </span>
                </td>
                <td class="text-end">
                  <?php if ($p['estado']==='pendiente'): ?>
                    <form class="d-inline" method="post">
                      <input type="hidden" name="csrf" value="<?= htmlspecialchars($CSRF) ?>">
                      <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                      <input type="hidden" name="accion" value="confirmar">
                      <button class="btn btn-sm btn-success"
                              onclick="return confirm('¿Confirmar el pedido #<?= (int)$p['id'] ?>?')">
                        Confirmar
                      </button>
                    </form>
                    <form class="d-inline" method="post">
                      <input type="hidden" name="csrf" value="<?= htmlspecialchars($CSRF) ?>">
                      <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                      <input type="hidden" name="accion" value="cancelar">
                      <button class="btn btn-sm btn-outline-danger"
                              onclick="return confirm('¿Cancelar el pedido #<?= (int)$p['id'] ?>?')">
                        Cancelar
                      </button>
                    </form>
                  <?php elseif ($p['estado']==='confirmado'): ?>
                    <span class="text-success">—</span>
                  <?php else: ?>
                    <span class="text-muted">—</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</main>

<footer class="py-3 mt-4 bg-dark text-light">
  <div class="container small text-center">
    <p class="mb-0">© <?= date('Y') ?> Clínica de Fisioterapia Plenium · Panel</p>
  </div>
</footer>
</body>
</html>
