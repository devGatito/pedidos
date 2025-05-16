
<?php
include 'conexion.php';

$resultado = $conexion->query("SELECT * FROM platos ORDER BY nombre");

$platos = [];

while ($fila = $resultado->fetch_assoc()) {
    $platos[] = $fila;
}

echo json_encode($platos);
?>

