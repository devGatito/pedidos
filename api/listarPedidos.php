<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include __DIR__ . '/../conexion.php';

try {
    // Consulta para traer pedidos con sus detalles (puedes ajustarla)
    $stmtPedidos = $pdo->query("
        SELECT p.id, p.numero_pedido, p.fecha, p.estado, p.total, p.metodo_pago, p.transaccion_id, p.payer_email,
               pd.plato, pd.precio, pd.cantidad
        FROM pedidos p
        LEFT JOIN pedidos_detalle pd ON p.id = pd.pedido_id
        ORDER BY p.fecha DESC, p.id DESC
    ");

    $pedidos = [];
    while ($row = $stmtPedidos->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];

        // Si el pedido aún no existe en el arreglo, se crea
        if (!isset($pedidos[$id])) {
            $pedidos[$id] = [
                'pedido_id' => $id,
                'numero_pedido' => $row['numero_pedido'],
                'fecha' => $row['fecha'],
                'estado' => $row['estado'],
                'total' => $row['total'],
                'metodo_pago' => $row['metodo_pago'],
                'transaccion_id' => $row['transaccion_id'],
                'payer_email' => $row['payer_email'],
                'items' => []
            ];
        }

        // Agregar el detalle del ítem solo si existe (puede haber pedidos sin detalles)
        if ($row['plato']) {
            $pedidos[$id]['items'][] = [
                'plato' => $row['plato'],
                'precio' => $row['precio'],
                'cantidad' => $row['cantidad']
            ];
        }
    }

    // Reindexar para que sea un array simple y no asociativo
    $pedidos = array_values($pedidos);

    echo json_encode(['success' => true, 'pedidos' => $pedidos]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al obtener pedidos',
        'message' => $e->getMessage()
    ]);
}
