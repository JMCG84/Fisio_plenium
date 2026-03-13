<?php
/* register.php — Alta de usuario (rol paciente) + login automático */
declare(strict_types=1);
session_start();
require __DIR__ . '/config/db.php';

$next = $_GET['next'] ?? $_POST['next'] ?? 'index.php';
$errores = [];
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = trim($_POST['nombre'] ?? '');
  $email  = trim($_POST['email'] ?? '');
  $pass   = $_POST['password'] ?? '';
  $pass2  = $_POST['password2'] ?? '';

  if ($nombre === '') $errores[] = 'El nombre es obligatorio.';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'Email no válido.';
  if (strlen($pass) < 6) $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
  if ($pass !== $pass2) $errores[] = 'Las contraseñas no coinciden.';

  if (!$errores) {
    try {
      // ¿Existe ya ese email?
      $st = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
      $st->execute([$email]);
      if ($st->fetch()) {
        $errores[] = 'Ya existe un usuario con ese email.';
      } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $ins = $pdo->prepare('INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (?, ?, ?, "paciente")');
        $ins->execute([$nombre, $email, $hash]);
        $uid = (int)$pdo->lastInsertId();

        // Login automático
        $_SESSION['usuario'] = [
          'id' => $uid,
          'nombre' => $nombre,
          'email' => $email,
          'rol' => 'paciente'
        ];
        header('Location: ' . $next);
        exit;
      }
    } catch (Throwable $e) {
      $errores[] = 'Error al registrar: ' . $e->getMessage();
    }
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Crear cuenta · Plenium</title>
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

<main class="container my-4" style="max-width:520px;">
  <div class="bg-white p-4 rounded-3 shadow-sm">
    <h1 class="h5 mb-3">Crear cuenta</h1>

    <?php if ($errores): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errores as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" novalidate>
      <input type="hidden" name="next" value="<?= htmlspecialchars($next) ?>">
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre y apellidos</label>
        <input id="nombre" name="nombre" class="form-control" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input id="email" name="email" type="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input id="password" name="password" type="password" class="form-control" required minlength="6">
      </div>
      <div class="mb-3">
        <label for="password2" class="form-label">Repite la contraseña</label>
        <input id="password2" name="password2" type="password" class="form-control" required minlength="6">
      </div>
      <div class="d-grid">
        <button class="btn btn-primary">Crear cuenta</button>
      </div>
      <p class="small text-muted mt-3 mb-0">
        ¿Ya tienes cuenta? <a href="login.php?next=<?= urlencode($next) ?>">Inicia sesión</a>.
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
