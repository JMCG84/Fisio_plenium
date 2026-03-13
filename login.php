<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config/db.php';

// Si ya está logueado, redirigimos en función de next/rol
$next = $_GET['next'] ?? $_POST['next'] ?? '';
if (isset($_SESSION['usuario'])) {
  if ($next) {
    header('Location: ' . $next);
  } elseif (($_SESSION['usuario']['rol'] ?? 'paciente') === 'admin') {
    header('Location: admin_pedidos.php');
  } else {
    header('Location: index.php');
  }
  exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';
  $next  = $_POST['next'] ?? ''; // re-leer por si venía en el form

  if ($email === '' || $pass === '') {
    $error = 'Introduce email y contraseña.';
  } else {
    $sql = "SELECT id, nombre, email, password_hash, rol FROM usuarios WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($pass, $usuario['password_hash'])) {
      // Guardar sesión mínima
      $_SESSION['usuario'] = [
        'id'    => (int)$usuario['id'],
        'nombre'=> (string)$usuario['nombre'],
        'email' => (string)$usuario['email'],
        'rol'   => (string)$usuario['rol'],
      ];

      // Redirección: prioriza next; si no hay, admin → panel; si no, index
      if ($next) {
        header('Location: ' . $next);
      } elseif ($usuario['rol'] === 'admin') {
        header('Location: admin_pedidos.php');
      } else {
        header('Location: index.php');
      }
      exit;
    } else {
      $error = 'Credenciales incorrectas.';
    }
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Iniciar sesión · Plenium</title>
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

<main class="container my-4" style="max-width:420px;">
  <div class="bg-white p-4 rounded-3 shadow-sm">
    <h1 class="h5 mb-3">Iniciar sesión</h1>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
      <input type="hidden" name="next" value="<?= htmlspecialchars($next ?: ($_GET['next'] ?? '')) ?>">
      <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" id="email" name="email" required autocomplete="email">
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Entrar</button>
      </div>
      <p class="small text-muted mt-3 mb-0">
        ¿No tienes cuenta? <a href="register.php?next=<?= urlencode($next ?: ($_GET['next'] ?? '')) ?>">Crear cuenta</a>.
      </p>
    </form>
  </div>
</main>

<footer class="py-3 mt-4 bg-primary text-light">
  <div class="container small text-center">
    <p class="mb-0">© <?= date('Y') ?> Clínica de Fisioterapia Plenium</p>
  </div>
</footer>
</body>
</html>
