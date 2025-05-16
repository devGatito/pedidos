<!-- index.php -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Sistema de Pedidos - Cevichería</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="./styles/index.css" />
</head>
<body>
  <div class="container">
    <h2>Pedidos - Cevichería Cantaros</h2>

    <button class="cart-button" onclick="mostrarMenu()">
      <i class="fas fa-shopping-cart"></i> Hacer Pedido
    </button>

    <div class="menu" id="menu">
      <button onclick="agregarPedido('ceviche', 35)">ceviche – S/35.00</button>
      <button onclick="agregarPedido('parihuela', 40)">parihuela – S/40.00</button>
      <button onclick="agregarPedido('trio marino', 50)">trio marino – S/50.00</button>
      <button onclick="agregarPedido('arroz con mariscos', 45)">arroz con mariscos – S/45.00</button>
      <h4>Entradas</h4>
      <button onclick="agregarPedido('chicharrón de pota', 10)">chicharrón de pota – S/10.00</button>
      <button onclick="agregarPedido('chilcano', 10)">chilcano – S/10.00</button>
      <h5>Bebidas</h5>
      <button onclick="agregarPedido('chicha morada', 12)">chicha morada – S/12.00</button>
      <button onclick="agregarPedido('maracuyá', 10)">maracuyá – S/10.00</button>
      <button onclick="agregarPedido('limonada frozen', 14)">limonada frozen – S/14.00</button>
    </div>

    <div class="summary" id="resumen">
      <h3>Resumen del pedido</h3>
      <div id="lista"></div>
      <p id="total"></p>
    </div>

    <button class="confirmar-btn" onclick="confirmarPedido()">Confirmar Pedido</button>

    <div id="paypal-button-container"></div>

    <script src="https://www.paypal.com/sdk/js?client-id=TU_CLIENT_ID_SANDBOX&currency=USD"></script>
    <script src="./js/main.js"></script> 
  </div>
</body>
</html>
