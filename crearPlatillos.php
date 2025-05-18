<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="./styles/index.css" />
  <link rel="stylesheet" href="./styles/header.css" />
    <link rel="stylesheet" href="./styles/platillos.css" />

  <title>Crear Platillos</title>
  
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
    <h1>Crear Nuevo Platillo</h1>

    <form id="formPlatillo">
      <label for="nombre">Nombre del Platillo</label>
      <input type="text" id="nombre" name="nombre" required maxlength="100" />

      <label for="descripcion">Descripción</label>
      <textarea id="descripcion" name="descripcion" rows="4" maxlength="300" required></textarea>

      <label for="precio">Precio (S/)</label>
      <input type="number" id="precio" name="precio" step="0.01" min="0.01" required />

      <button type="submit">Guardar Platillo</button>

      <div id="message"></div>
    </form>
  </main>

  <script>
    const form = document.getElementById('formPlatillo');
    const messageDiv = document.getElementById('message');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      // Validar campos (ya están con required pero podemos hacer validaciones extras si quieres)
      const nombre = form.nombre.value.trim();
      const descripcion = form.descripcion.value.trim();
      const precio = parseFloat(form.precio.value);

      if (!nombre || !descripcion || isNaN(precio) || precio <= 0) {
        messageDiv.textContent = 'Por favor, llena todos los campos correctamente.';
        messageDiv.style.color = 'red';
        return;
      }

      // Preparar datos para enviar
      const platillo = {
        nombre,
        descripcion,
        precio
      };

      try {
        // Aquí cambia la URL por la que usas para guardar el platillo
        const response = await fetch('http://localhost/system/api/crearPlatillo.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(platillo)
        });

        const result = await response.json();

        if (result.success) {
          messageDiv.textContent = 'Platillo guardado correctamente.';
          messageDiv.style.color = 'green';
          form.reset();
        } else {
          messageDiv.textContent = 'Error: ' + (result.message || 'No se pudo guardar el platillo.');
          messageDiv.style.color = 'red';
        }
      } catch (error) {
        messageDiv.textContent = 'Error en la comunicación con el servidor.';
        messageDiv.style.color = 'red';
      }
    });
  </script>
</body>

</html>
