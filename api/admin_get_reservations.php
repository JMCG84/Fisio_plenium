<?php
declare(strict_types=1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../config/db.php';

try {
    // 1. Obtener todos los pedidos
    $sqlPedidos = "SELECT p.*, u.nombre as usuario_nombre 
                   FROM pedidos p 
                   LEFT JOIN usuarios u ON p.usuario_id = u.id 
                   ORDER BY p.creado_en DESC";
    $stmtPedidos = $pdo->query($sqlPedidos);
    $pedidos = $stmtPedidos->fetchAll();

    // 2. Por cada pedido, obtener sus líneas
    $result = [];
    foreach ($pedidos as $pedido) {
        $stmtLineas = $pdo->prepare("SELECT lp.*, s.nombre as servicio_nombre 
                                    FROM lineas_pedidos lp 
                                    JOIN servicios s ON lp.servicio_id = s.id 
                                    WHERE lp.pedido_id = ?");
        $stmtLineas->execute([$pedido['id']]);
        $pedido['lineas'] = $stmtLineas->fetchAll();
        $result[] = $pedido;
    }

    echo json_encode([
        'success' => true,
        'data' => $result
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
