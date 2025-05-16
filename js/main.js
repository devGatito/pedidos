let pedido = [];

function mostrarMenu() {
    const menu = document.getElementById('menu');
    menu.style.display = menu.style.display === 'none' || menu.style.display === '' ? 'block' : 'none';
}

function agregarPedido(plato, precio) {
    pedido.push({ plato, precio });
    mostrarResumen();
}

function mostrarResumen() {
    const lista = document.getElementById('lista');
    const total = document.getElementById('total');
    lista.innerHTML = '';
    let totalSoles = 0;
    pedido.forEach(item => {
        lista.innerHTML += `<p>${item.plato} – S/${item.precio.toFixed(2)}</p>`;
        totalSoles += item.precio;
    });
    total.innerHTML = `<strong>Total: S/${totalSoles.toFixed(2)}</strong>`;
}

function confirmarPedido() {
    if (pedido.length === 0) {
        alert("Selecciona al menos un producto");
        return;
    }

    fetch('registrarPedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(pedido)
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        return response.text().then(text => {
            try {
                return JSON.parse(text); // Intenta parsear como JSON
            } catch {
                throw new Error(`Respuesta no JSON: ${text}`);
            }
        });
    })
    .then(data => {
        console.log("Respuesta:", data);
        alert(data.success || data.error);
    })
    .catch(error => {
        console.error("Error completo:", error);
        alert("Error al guardar el pedido. Detalles en consola.");
    });
}