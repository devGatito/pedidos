<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include 'conexion.php';

// Validar método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Obtener y validar datos JSON
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON inválido: ' . json_last_error_msg()]);
    exit;
}

try {
    $pdo->beginTransaction();

    // Generar número de pedido único
    $numeroPedido = 'PED-' . date('Ymd') . '-' . substr(uniqid(), -5);
    $totalPedido = 0;
    $metodoPago = isset($data['metodo_pago']) ? $data['metodo_pago'] : 'efectivo';
    $transaccionId = isset($data['transaccion_id']) ? $data['transaccion_id'] : null;
    $payerEmail = isset($data['payer_email']) ? $data['payer_email'] : null;

    // Insertar cabecera del pedido
    $stmtPedido = $pdo->prepare("INSERT INTO pedidos 
                                (numero_pedido, fecha, estado, total, metodo_pago, transaccion_id, payer_email) 
                                VALUES (?, NOW(), 'pendiente', ?, ?, ?, ?)");
    $stmtPedido->execute([$numeroPedido, 0, $metodoPago, $transaccionId, $payerEmail]);
    $pedidoId = $pdo->lastInsertId();

    // Procesar cada ítem del pedido
    foreach ($data['pedido'] as $item) {
        if (!isset($item['plato'], $item['precio']) || !is_numeric($item['precio'])) {
            throw new Exception('Datos de plato o precio inválidos');
        }

        $precio = floatval($item['precio']);
        $totalPedido += $precio;

        // Insertar detalle del pedido
        $stmtDetalle = $pdo->prepare("INSERT INTO pedidos_detalle 
                                     (pedido_id, plato, precio) 
                                     VALUES (?, ?, ?)");
        $stmtDetalle->execute([$pedidoId, $item['plato'], $precio]);
    }

    // Actualizar total del pedido
    $stmtUpdate = $pdo->prepare("UPDATE pedidos SET total = ? WHERE id = ?");
    $stmtUpdate->execute([$totalPedido, $pedidoId]);

    $pdo->commit();

    // Respuesta con más información
    echo json_encode([
        'success' => true,
        'pedido_id' => $pedidoId,
        'numero_pedido' => $numeroPedido,
        'total' => $totalPedido,
        'metodo_pago' => $metodoPago,
        'fecha' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al procesar el pedido',
        'message' => $e->getMessage()
    ]);
}
?>