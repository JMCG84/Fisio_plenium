<?php
declare(strict_types=1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // For development; refine in production
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query("SELECT id, nombre, descripcion, precio, 'fisio-deportiva.jpg' as archivo FROM servicios WHERE activo = 1");
    $services = $stmt->fetchAll();
    
    // Note: 'archivo' is mapped to a default for now as it's not in the original SQL schema, 
    // but we can update the SQL to include it later.
    
    echo json_encode([
        'success' => true,
        'data' => $services
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
