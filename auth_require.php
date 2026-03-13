<?php
// auth_require.php — exige usuario en sesión antes de continuar
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

$next = $_SERVER['REQUEST_URI'] ?? 'index.php';
if (!isset($_SESSION['usuario'])) {
  header('Location: login.php?next=' . urlencode($next));
  exit;
}
