// main.js
document.addEventListener('DOMContentLoaded', function() {
  // Variables globales
  let carrito = [];
  const botonCarrito = document.getElementById('boton-carrito');
  const modal = document.getElementById('modal-resumen');
  const spanCerrar = document.getElementsByClassName('cerrar')[0];
  const modalLista = document.getElementById('modal-lista');
  const modalTotal = document.getElementById('modal-total');
  const cantidadItems = document.getElementById('cantidad-items');
  const paypalContainer = document.getElementById('paypal-button-container');

  // Ocultar PayPal inicialmente
  paypalContainer.style.display = 'none';

  // Event listeners para botones "Agregar"
  document.querySelectorAll('.agregar-btn').forEach(button => {
    button.addEventListener('click', agregarAlCarrito);
  });

  // Event listener para el botón del carrito
  botonCarrito.addEventListener('click', mostrarModalResumen);

  // Event listener para cerrar el modal
  spanCerrar.addEventListener('click', () => {
    modal.style.display = 'none';
  });

  // Cerrar modal al hacer clic fuera de él
  window.addEventListener('click', (event) => {
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  });

  // Función para agregar items al carrito
  function agregarAlCarrito(event) {
    const boton = event.target;
    const nombre = boton.getAttribute('data-nombre');
    const precio = parseFloat(boton.getAttribute('data-precio'));
    
    // Buscar si el item ya está en el carrito
    const itemExistente = carrito.find(item => item.nombre === nombre);
    
    if (itemExistente) {
      itemExistente.cantidad += 1;
    } else {
      carrito.push({
        nombre: nombre,
        precio: precio,
        cantidad: 1
      });
    }
    
    actualizarContadorCarrito();
    
    // Feedback visual
    boton.textContent = '✓ Agregado';
    setTimeout(() => {
      boton.textContent = 'Agregar';
    }, 1000);
  }

  // Función para mostrar el modal con el resumen del pedido
  function mostrarModalResumen() {
    if (carrito.length === 0) {
      modalLista.innerHTML = '<p>No hay items en el carrito</p>';
      modalTotal.textContent = '';
      paypalContainer.style.display = 'none';
    } else {
      modalLista.innerHTML = '';
      let total = 0;
      
      carrito.forEach(item => {
        const subtotal = item.precio * item.cantidad;
        total += subtotal;
        
        const itemElement = document.createElement('div');
        itemElement.className = 'item-carrito';
        itemElement.innerHTML = `
          <div class="item-info">
            <span class="item-nombre">${item.nombre}</span>
            <span class="item-cantidad">${item.cantidad} x S/${item.precio.toFixed(2)}</span>
          </div>
          <div class="item-subtotal">S/${subtotal.toFixed(2)}</div>
          <div class="item-acciones">
            <button class="btn-menos" data-nombre="${item.nombre}">-</button>
            <button class="btn-mas" data-nombre="${item.nombre}">+</button>
            <button class="btn-eliminar" data-nombre="${item.nombre}">×</button>
          </div>
        `;
        
        modalLista.appendChild(itemElement);
      });
      
      modalTotal.innerHTML = `<strong>Total: S/${total.toFixed(2)}</strong>`;
      
      // Agregar event listeners a los botones del modal
      document.querySelectorAll('.btn-menos').forEach(btn => {
        btn.addEventListener('click', disminuirCantidad);
      });
      
      document.querySelectorAll('.btn-mas').forEach(btn => {
        btn.addEventListener('click', aumentarCantidad);
      });
      
      document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', eliminarItem);
      });

      // Mostrar botón de PayPal
      renderPayPalButton(total);
    }
    
    modal.style.display = 'block';
  }

  // Funciones para modificar el carrito
  function aumentarCantidad(event) {
    const nombre = event.target.getAttribute('data-nombre');
    const item = carrito.find(item => item.nombre === nombre);
    
    if (item) {
      item.cantidad += 1;
      mostrarModalResumen();
      actualizarContadorCarrito();
    }
  }

  function disminuirCantidad(event) {
    const nombre = event.target.getAttribute('data-nombre');
    const item = carrito.find(item => item.nombre === nombre);
    
    if (item && item.cantidad > 1) {
      item.cantidad -= 1;
    } else {
      carrito = carrito.filter(item => item.nombre !== nombre);
    }
    
    mostrarModalResumen();
    actualizarContadorCarrito();
  }

  function eliminarItem(event) {
    const nombre = event.target.getAttribute('data-nombre');
    carrito = carrito.filter(item => item.nombre !== nombre);
    mostrarModalResumen();
    actualizarContadorCarrito();
  }

  // Función para actualizar el contador del carrito
  function actualizarContadorCarrito() {
    const totalItems = carrito.reduce((total, item) => total + item.cantidad, 0);
    cantidadItems.textContent = totalItems;
  }

  // Función para renderizar el botón de PayPal (igual que antes)
  function renderPayPalButton(total) {
    paypalContainer.innerHTML = '';
    paypalContainer.style.display = 'block';

    const tasaCambio = 0.26;
    const totalUSD = (total * tasaCambio).toFixed(2);

    paypal.Buttons({
      style: {
        color: 'gold',
        shape: 'rect',
        label: 'pay',
        height: 40
      },
     createOrder: function(data, actions) {
  const tasaCambio = 0.26; // tasa de cambio PEN -> USD

  // Calcular la suma total en USD basada en los items del carrito
  const itemTotalUSD = carrito.reduce((sum, item) => {
    return sum + (item.precio * item.cantidad * tasaCambio);
  }, 0);

  // Convertir a cadena con 2 decimales para PayPal
  const totalUSDStr = itemTotalUSD.toFixed(2);

  return actions.order.create({
    purchase_units: [{
      amount: {
        currency_code: "USD",
        value: totalUSDStr,
        breakdown: {
          item_total: {
            currency_code: "USD",
            value: totalUSDStr
          }
        }
      },
      description: "Compra en Cevichería Cantaros",
      items: carrito.map(item => ({
        name: item.nombre.substring(0, 127),
        unit_amount: {
          currency_code: "USD",
          value: (item.precio * tasaCambio).toFixed(2)
        },
        quantity: item.cantidad.toString(),
        category: "PHYSICAL_GOODS"
      }))
    }],
    application_context: {
      shipping_preference: "NO_SHIPPING",
      brand_name: "Cevichería Cantaros",
      user_action: "PAY_NOW"
    }
  });
},


      onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
          enviarPedidoAlBackend(details, total);
          mostrarAlerta('success', `Pago completado por ${details.payer.name.given_name}! ID de transacción: ${details.id}`);
          carrito = [];
          actualizarContadorCarrito();
          modal.style.display = 'none';
          paypalContainer.style.display = 'none';

          window.location.href = 'http://localhost/system/thanks.html';
        });
      },
      onError: function(err) {
        console.error('Error en el pago:', err);
        mostrarAlerta('error', 'Ocurrió un error al procesar el pago. Por favor, inténtalo de nuevo.');
      }
    }).render('#paypal-button-container');
  }

  // Función para enviar el pedido al backend
  function enviarPedidoAlBackend(detallesPago, total) {
    const pedido = {
      items: carrito,
      total: total,
      fecha: new Date().toISOString(),
      metodo_pago: 'paypal',
      transaccion_id: detallesPago.id,
      payer_email: detallesPago.payer.email_address
    };
    console.log('Pedido a enviar:', pedido);

    fetch('http://localhost/system/api/registrarPedido.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(pedido)
    })
    .then(response => response.json())
    .then(data => {
      console.log('Respuesta del servidor:', data);
    })
    .catch(error => {
      console.error('Error al enviar el pedido:', error);
    });
  }

  // Función para mostrar alertas
  function mostrarAlerta(tipo, mensaje) {
    const alerta = document.createElement('div');
    alerta.className = `alerta alerta-${tipo}`;
    alerta.innerHTML = `
      <span>${mensaje}</span>
      <button onclick="this.parentElement.remove()">&times;</button>
    `;
    document.body.appendChild(alerta);
    setTimeout(() => alerta.remove(), 5000);
  }
});
