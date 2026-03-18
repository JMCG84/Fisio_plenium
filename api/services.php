<?php
declare(strict_types=1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // For development; refine in production
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query("SELECT id, nombre, descripcion, precio FROM servicios WHERE activo = 1");
    $services = $stmt->fetchAll();

    // Map IDs to original static filenames because the 'archivo' column is missing in DB schema
    $imageMap = [
        1 => 'fisio-deportiva.jpg',
        2 => 'fisio-traumatologica.jpg',
        3 => 'fisio-pediátrica.jpg',
        4 => 'fisio-respiratoria.jpg',
        5 => 'fisio-suelo Pelvico.jpg',
        6 => 'Osteopatia.jpg'
    ];

    foreach ($services as &$service) {
        $serviceId = (int) $service['id'];
        $service['archivo'] = $imageMap[$serviceId] ?? 'fisio-deportiva.jpg';
    }

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
