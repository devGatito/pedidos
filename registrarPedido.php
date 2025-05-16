<?php
// Siempre al inicio del archivo, sin espacios/HTML antes
header("Content-Type: application/json"); // ¡Importante!

include 'conexion.php';

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'JSON inválido']);
    exit;
}

try {
    $pdo->beginTransaction();

    foreach ($data as $item) {
        if (!isset($item['plato'], $item['precio'])) {
            throw new Exception('Faltan datos de plato o precio');
        }

        $stmt = $pdo->prepare("INSERT INTO pedidos (plato, total) VALUES (?, ?)");
        $stmt->execute([$item['plato'], floatval($item['precio'])]);
    }

    $pdo->commit();
    echo json_encode(['success' => 'Pedido guardado']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['error' => $e->getMessage()]);
}
?>