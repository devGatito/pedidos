let pedido = [];
let totalSoles = 0;
let pedidoId = null;
let pedidoConfirmado = false;

// Mostrar/ocultar menú
function mostrarMenu() {
    const menu = document.getElementById('menu');
    menu.style.display = menu.style.display === 'none' || menu.style.display === '' ? 'block' : 'none';
}

// Agregar ítem al pedido
function agregarPedido(plato, precio) {
    pedido.push({ plato, precio: parseFloat(precio) });
    mostrarResumen();
}

// Mostrar resumen del pedido
function mostrarResumen() {
    const lista = document.getElementById('lista');
    const totalElement = document.getElementById('total');
    
    lista.innerHTML = '';
    totalSoles = 0;
    
    pedido.forEach(item => {
        lista.innerHTML += `<div class="item-pedido">
            <span>${item.plato}</span>
            <span>S/${item.precio.toFixed(2)}</span>
            <button onclick="eliminarItem('${item.plato}')" class="eliminar-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>`;
        totalSoles += item.precio;
    });
    
    totalElement.innerHTML = `<strong>Total: S/${totalSoles.toFixed(2)}</strong>`;
}

// Eliminar ítem del pedido
function eliminarItem(plato) {
    pedido = pedido.filter(item => item.plato !== plato);
    mostrarResumen();
}

// Confirmar pedido (ahora solo muestra PayPal)
function confirmarPedido() {
    if (pedido.length === 0) {
        mostrarAlerta('error', 'Selecciona al menos un producto');
        return;
    }

    pedidoConfirmado = true;
    document.getElementById('paypal-button-container').style.display = 'block';
    actualizarBotonPayPal();
}

// PayPal - Función mejorada
function actualizarBotonPayPal() {
    const container = document.getElementById('paypal-button-container');
    container.innerHTML = '';
    
    if (pedido.length === 0 || !pedidoConfirmado) return;

    const tipoCambio = 3.7; // Actualizar con tasa real
    const totalUSD = (totalSoles / tipoCambio).toFixed(2);

    paypal.Buttons({
        style: {
            color: 'gold',
            shape: 'rect',
            label: 'paypal',
            height: 40,
            layout: 'vertical'
        },

        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    description: `Pedido en Cevichería Cantaros`,
                    amount: {
                        value: totalUSD,
                        currency_code: "USD",
                        breakdown: {
                            item_total: { value: totalUSD, currency_code: "USD" }
                        }
                    },
                    items: pedido.map(item => ({
                        name: item.plato.substring(0, 127),
                        unit_amount: {
                            value: (item.precio / tipoCambio).toFixed(2),
                            currency_code: "USD"
                        },
                        quantity: "1",
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

        onApprove: async function(data, actions) {
            try {
                const details = await actions.order.capture();
                
                // Registrar el pedido en nuestra base de datos
                const response = await fetch('registrarPedido.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        pedido: pedido,
                        metodo_pago: 'paypal',
                        transaccion_id: details.id,
                        payer_email: details.payer.email_address
                    })
                });
                
                const result = await response.json();
                
                if (!response.ok) {
                    throw new Error(result.error || 'Error al registrar el pedido');
                }
                
                // Mostrar confirmación al usuario
                mostrarAlerta('success', 
                    `Pago completado! Pedido #${result.numero_pedido} registrado. 
                    ID de transacción: ${details.id}`);
                
                // Limpiar el carrito después de éxito
                pedido = [];
                totalSoles = 0;
                pedidoConfirmado = false;
                mostrarResumen();
                document.getElementById('paypal-button-container').style.display = 'none';
                
            } catch (error) {
                console.error("Error en el proceso completo:", error);
                mostrarAlerta('error', 
                    `Pago completado pero hubo un error al registrar: ${error.message}`);
            }
        },

        onCancel: function(data) {
            mostrarAlerta('warning', 'Pago cancelado por el usuario');
            pedidoConfirmado = false;
        },

        onError: function(err) {
            console.error(err);
            mostrarAlerta('error', 'Error en el proceso de pago con PayPal');
            pedidoConfirmado = false;
        }
    }).render('#paypal-button-container');
}

// Mostrar alertas estilizadas
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

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    // Ocultar botón PayPal inicialmente
   
});