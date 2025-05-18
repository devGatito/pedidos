<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include __DIR__ . '/../conexion.php';

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

// Validar datos mínimos requeridos
if (!isset($data['items']) || !is_array($data['items']) || count($data['items']) === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'El carrito está vacío o no contiene items válidos']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Generar número de pedido único con mayor precisión temporal
    $numeroPedido = 'PED-' . date('Ymd-His-') . substr(microtime(), 2, 4) . '-' . bin2hex(random_bytes(2));
    $totalPedido = 0;
    $metodoPago = isset($data['metodo_pago']) ? htmlspecialchars($data['metodo_pago']) : 'efectivo';
    $transaccionId = isset($data['transaccion_id']) ? htmlspecialchars($data['transaccion_id']) : null;
    $payerEmail = isset($data['payer_email']) ? filter_var($data['payer_email'], FILTER_SANITIZE_EMAIL) : null;

    // Insertar cabecera del pedido
    $stmtPedido = $pdo->prepare("INSERT INTO pedidos 
                    (numero_pedido, fecha, estado, total, metodo_pago, transaccion_id, payer_email) 
                    VALUES (?, NOW(), 'pendiente', 0, ?, ?, ?)");

    // Intentar insertar con manejo de duplicados
    $intentos = 0;
    $maxIntentos = 3;
    $insertado = false;
    
    while ($intentos < $maxIntentos && !$insertado) {
        try {
            $stmtPedido->execute([$numeroPedido, $metodoPago, $transaccionId, $payerEmail]);
            $insertado = true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Violación de restricción única
                $intentos++;
                if ($intentos < $maxIntentos) {
                    // Regenerar número de pedido con más componentes únicos
                    $numeroPedido = 'PED-' . date('Ymd-His-') . substr(microtime(), 2, 4) . '-' . bin2hex(random_bytes(2));
                    continue;
                }
            }
            throw $e;
        }
    }

    if (!$insertado) {
        throw new Exception('No se pudo generar un número de pedido único después de varios intentos');
    }

    $pedidoId = $pdo->lastInsertId();

    // Procesar cada ítem del pedido con validación
    foreach ($data['items'] as $item) {
        if (!isset($item['nombre'], $item['precio'], $item['cantidad']) || 
            !is_numeric($item['precio']) || 
            !is_numeric($item['cantidad']) ||
            $item['cantidad'] <= 0) {
            throw new Exception('Datos de plato o precio inválidos');
        }

        // Sanitizar inputs
        $nombrePlato = htmlspecialchars($item['nombre']);
        $precio = floatval($item['precio']);
        $cantidad = intval($item['cantidad']);
        
        // Validar valores
        if ($precio <= 0 || $cantidad <= 0) {
            throw new Exception('Precio o cantidad inválidos');
        }

        $subtotal = $precio * $cantidad;
        $totalPedido += $subtotal;

        $stmtDetalle = $pdo->prepare("INSERT INTO pedidos_detalle 
                             (pedido_id, plato, precio, cantidad) 
                             VALUES (?, ?, ?, ?)");
        $stmtDetalle->execute([$pedidoId, $nombrePlato, $precio, $cantidad]);
    }

    // Actualizar total del pedido con redondeo a 2 decimales
    $totalPedido = round($totalPedido, 2);
    $stmtUpdate = $pdo->prepare("UPDATE pedidos SET total = ? WHERE id = ?");
    $stmtUpdate->execute([$totalPedido, $pedidoId]);

    $pdo->commit();

    // Responder con información completa
    echo json_encode([
        'success' => true,
        'pedido_id' => $pedidoId,
        'numero_pedido' => $numeroPedido,
        'total' => $totalPedido,
        'metodo_pago' => $metodoPago,
        'transaccion_id' => $transaccionId,
        'fecha' => date('Y-m-d H:i:s'),
        'items_count' => count($data['items'])
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al procesar el pedido',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString() // Solo para desarrollo, quitar en producción
    ]);
}