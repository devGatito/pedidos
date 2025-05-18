<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="./styles/index.css" />
    <link rel="stylesheet" href="./styles/header.css" />
    <title>Órdenes Creadas</title>
    <style>
      /* Estilos simples para la tabla */
      table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
      }
      th, td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: left;
      }
      .items-list {
        margin: 0;
        padding-left: 20px;
      }
    </style>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="logo">Cevicheria</div>
            <ul class="nav-links">
                <li><a href="./index.php">Menú</a></li>
                <li><a href="./crearPlatillos.php">Crear Platillos</a></li>
                <li><a href="./ordenesCreadas.php">Órdenes Creadas</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Órdenes Creadas</h1>
        <div id="error-message" style="color: red;"></div>
        <table id="tabla-pedidos">
            <thead>
                <tr>
                    <th># Pedido</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th>Método de Pago</th>
                    <th>Transacción ID</th>
                    <th>Email</th>
                    <th>Items</th>
                </tr>
            </thead>
            <tbody>
                <!-- Aquí se agregarán las órdenes dinámicamente -->
            </tbody>
        </table>
    </main>

    <script>
      async function cargarPedidos() {
        const tbody = document.querySelector('#tabla-pedidos tbody');
        const errorMessage = document.getElementById('error-message');

        try {
          const response = await fetch('http://localhost/system/api/listarPedidos.php');
          const data = await response.json();

          if (data.success) {
            tbody.innerHTML = ''; // limpiar tabla

            data.pedidos.forEach(pedido => {
              const tr = document.createElement('tr');

              // Crear lista de items en un <ul>
              let itemsHtml = '<ul class="items-list">';
              pedido.items.forEach(item => {
                itemsHtml += `<li>${item.plato} - Cantidad: ${item.cantidad} - Precio: S/${parseFloat(item.precio).toFixed(2)}</li>`;
              });
              itemsHtml += '</ul>';

              tr.innerHTML = `
                <td>${pedido.numero_pedido}</td>
                <td>${pedido.fecha}</td>
                <td>${pedido.estado}</td>
                <td>S/${parseFloat(pedido.total).toFixed(2)}</td>
                <td>${pedido.metodo_pago}</td>
                <td>${pedido.transaccion_id ?? ''}</td>
                <td>${pedido.payer_email ?? ''}</td>
                <td>${itemsHtml}</td>
              `;

              tbody.appendChild(tr);
            });

          } else {
            errorMessage.textContent = 'No se pudieron cargar las órdenes: ' + (data.message || 'Error desconocido');
          }
        } catch (error) {
          errorMessage.textContent = 'Error al cargar las órdenes: ' + error.message;
        }
      }

      window.addEventListener('DOMContentLoaded', cargarPedidos);
    </script>
</body>

</html>
