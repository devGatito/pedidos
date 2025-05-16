<?php
include 'conexion.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['idPedido'], $data['estado'])) {
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

$idPedido = (int)$data['idPedido'];
$estado = $data['estado']; 

$sql = "UPDATE pedidos SET estado = ? WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$estado, $idPedido]);

echo json_encode(['success' => 'Estado del pedido actualizado correctamente']);
?>
