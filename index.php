<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <title>Sistema de Pedidos - Cevichería</title>
  <link rel="stylesheet" href="./styles/index.css" />
  <link rel="stylesheet" href="./styles/header.css">
  <link rel="stylesheet" href="./styles/carrito.css">
  <link rel="stylesheet" href="./styles/platillos.css">

</head>

<body>

  <header>
    <nav class="navbar">
      <div class="logo">Cevicheria</div>
      <ul class="nav-links">
        <li><a href="./index.php">Menú</a></li>
        <li><a href="./crearPlatillos.php">Crear Platillos</a></li>
        <li><a href="./ordenesCreadas.php">Ordenes Creadas</a></li>
      </ul>

    </nav>
  </header>

  <div class="container">
    <h1>Pedidos - Cevichería Cantaros</h1>


    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Incluimos la conexión
    require 'conexion.php';

    // Consulta para obtener todos los platillos
    $sql = "SELECT id, nombre, descripcion, precio FROM platillos ORDER BY nombre ASC";
    $stmt = $pdo->query($sql);
    $platillos = $stmt->fetchAll();

    if (count($platillos) === 0) {
      echo "<p>No hay platillos disponibles por el momento.</p>";
    } else {
      echo '<div class="platillos-lista">';
      foreach ($platillos as $platillo) {
        echo '<div class="platillo">';
        echo '<h3>' . htmlspecialchars($platillo['nombre']) . '</h3>';
        echo '<p>' . htmlspecialchars($platillo['descripcion']) . '</p>';
        echo '<p><strong>Precio:</strong> S/ ' . number_format($platillo['precio'], 2) . '</p>';
        echo '<button class="agregar-btn" data-nombre="' . htmlspecialchars($platillo['nombre']) . '" data-precio="' . $platillo['precio'] . '">Agregar</button>';
        echo '</div>';
      }
      echo '</div>';
    }
    ?>
    <button id="boton-carrito" title="Ver pedido">
      <span id="cantidad-items">0</span>
    </button>



  </div>

  <!-- Modal resumen -->
  <div id="modal-resumen" class="modal" style="display:none;">
    <div class="modal-content">
      <span class="cerrar">&times;</span>
      <h2>Resumen del pedido</h2>
      <div id="modal-lista"></div>
      <div id="modal-total"></div>
          <div id="paypal-button-container"></div>

    </div>
  </div>

  <div id="resumen-pedido" style="display:none;">
    <h2>Resumen del pedido</h2>
    <div id="lista"></div>
    <div id="total"></div>
  </div>


  <script src="./js/main.js"></script>
<script src="https://www.sandbox.paypal.com/sdk/js?client-id=AQpBTgiZFSa1vZxyAVLxbZmOn946fuRYfzKuZoGfXpDnNy48kv02pW_WIRV1hkORUA8ZMDx2gecJUFFK&currency=USD"></script>

</body>

</html>