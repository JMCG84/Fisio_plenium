<?php
// config/db.php — conexión PDO a MySQL (XAMPP por defecto)
$DB_HOST = '127.0.0.1';
$DB_NAME = 'fisio_plenium';
$DB_USER = 'root';
$DB_PASS = ''; // en XAMPP suele ser vacío
$DB_CHARSET = 'utf8mb4';

$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=$DB_CHARSET";

$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
  $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (Throwable $e) {
  // No imprimir HTML en archivos de API. Enviar JSON si es posible.
  if (str_contains($_SERVER['REQUEST_URI'], '/api/')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de conexión DB: ' . $e->getMessage()]);
  } else {
    echo "<h1>Error de conexión</h1>";
    echo "<p>No se pudo conectar a la base de datos.</p>";
  }
  exit;
}
