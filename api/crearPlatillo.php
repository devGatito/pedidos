<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include __DIR__ . '/../conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON inválido']);
    exit;
}

if (empty($data['nombre']) || empty($data['descripcion']) || !isset($data['precio'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

try {
    $nombre = htmlspecialchars($data['nombre']);
    $descripcion = htmlspecialchars($data['descripcion']);
    $precio = floatval($data['precio']);

    if ($precio <= 0) {
        throw new Exception("Precio inválido");
    }

    $stmt = $pdo->prepare("INSERT INTO platillos (nombre, descripcion, precio) VALUES (?, ?, ?)");
    $stmt->execute([$nombre, $descripcion, $precio]);

    echo json_encode(['success' => true, 'message' => 'Platillo creado correctamente']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al guardar platillo', 'message' => $e->getMessage()]);
}
?>
