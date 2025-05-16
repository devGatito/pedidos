<!-- index.php -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Sistema de Pedidos - Cevichería</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="./styles/index.css" />
  <style>
    .alerta {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 15px;
      border-radius: 5px;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 300px;
      z-index: 1000;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      animation: slideIn 0.5s forwards;
    }
    
    .alerta-success {
      background-color: #4CAF50;
    }
    
    .alerta-error {
      background-color: #F44336;
    }
    
    .alerta-warning {
      background-color: #FF9800;
    }
    
    @keyframes slideIn {
      from { transform: translateX(100%); }
      to { transform: translateX(0); }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Pedidos - Cevichería Cantaros</h2>

    <button class="cart-button" onclick="mostrarMenu()">
      <i class="fas fa-shopping-cart"></i> Hacer Pedido
    </button>

    <div class="menu" id="menu" style="display: none;">
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

   <script src="https://www.sandbox.paypal.com/sdk/js?client-id=Aa4btnTFzM9bkBberirQ8wo8q7WcmSXulSudK493O2uwo_LgEh8nsRrU53-j0jBSz5AhZc9z-YjTV0u8&currency=USD"></script>

    <script src="./js/main.js"></script> 
  </div>
</body>
</html>