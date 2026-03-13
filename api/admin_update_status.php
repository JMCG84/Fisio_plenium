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

if (!isset($input['pedido_id']) || !isset($input['estado'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters: pedido_id or estado']);
    exit;
}

$validStatuses = ['pendiente', 'confirmado', 'cancelado'];
if (!in_array($input['estado'], $validStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status: ' . $input['estado']]);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
    $stmt->execute([
        $input['estado'],
        $input['pedido_id']
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'El estado de la reserva #' . $input['pedido_id'] . ' ha sido actualizado a ' . $input['estado']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontró la reserva or el estado no ha cambiado']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
