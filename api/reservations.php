<?php
declare(strict_types=1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'No input data provided']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Cabecera del pedido (simulamos usuario_id=1 si no hay sesión para demostración)
    // En producción esto debería sacarse de una sesión de JWT
    $usuario_id = $input['usuario_id'] ?? 1;

    $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, telefono, fecha_cita, mensaje, total, estado, creado_en) VALUES (?, ?, ?, ?, ?, 'pendiente', NOW())");
    $stmt->execute([
        $usuario_id,
        $input['telefono'] ?? '',
        $input['fecha'] ?? null,
        $input['mensaje'] ?? '',
        $input['total']
    ]);

    $pedido_id = $pdo->lastInsertId();

    // 2. Líneas del pedido
    $stmtLinea = $pdo->prepare("INSERT INTO lineas_pedidos (pedido_id, servicio_id, cantidad, precio_u) VALUES (?, ?, ?, ?)");

    foreach ($input['items'] as $item) {
        $stmtLinea->execute([
            $pedido_id,
            $item['id'],
            $item['cantidad'],
            $item['precio']
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'pedido_id' => $pedido_id,
        'message' => 'Reserva creada correctamente en la base de datos MySQL'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
