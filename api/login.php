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

if (!isset($input['email']) || !isset($input['password'])) {
    echo json_encode(['success' => false, 'message' => 'Email y contraseña requeridos']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, nombre, email, password_hash, rol FROM usuarios WHERE email = ?");
    $stmt->execute([$input['email']]);
    $user = $stmt->fetch();

    if ($user) {
        // En un proyecto real usaríamos password_verify($input['password'], $user['password_hash'])
        // Si tu proyecto original no usaba hashes, comparamos directamente o adaptamos:
        $isCorrect = (password_verify($input['password'], $user['password_hash']) || $input['password'] === $user['password_hash']);

        if ($isCorrect) {
            unset($user['password_hash']); // No enviamos el hash al front
            echo json_encode([
                'success' => true,
                'user' => $user,
                'message' => 'Login correcto'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
