document.getElementById('formActualizarCliente').addEventListener('submit', function(event) {
    event.preventDefault();
    actualizarCliente();
});

function actualizarCliente() {
    const cedula = document.getElementById('cedula').value.trim();
    const telefono = document.getElementById('telefono').value.trim();
    const email = document.getElementById('email').value.trim();
    const direccion = document.getElementById('direccion').value.trim();

    const messageDiv = document.getElementById('message');
    const resultContainer = document.getElementById('resultContainer');

    // Limpiar mensajes previos
    messageDiv.style.display = 'none';
    messageDiv.textContent = '';
    resultContainer.style.display = 'none';
    resultContainer.innerHTML = '';

    // Validar campos requeridos
    if (!cedula || !telefono || !email || !direccion) {
        messageDiv.textContent = 'Por favor complete todos los campos.';
        messageDiv.style.display = 'block';
        return;
    }

    // Armar los datos para enviar por método PUT (como x-www-form-urlencoded)
    const formData = new URLSearchParams();
    formData.append('cedula', cedula);
    formData.append('telefono', telefono);
    formData.append('email', email);
    formData.append('direccion', direccion);

    fetch('../back/actualizarCliente.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData.toString()
    })
    .then(response => response.json()
        .then(data => ({ status: response.status, body: data }))
    )
    .then(({ status, body }) => {
        if (status >= 200 && status < 300) {
            // éxito
            resultContainer.innerHTML = `<p class="success">${body.message || 'Cliente actualizado exitosamente.'}</p>`;
            resultContainer.style.display = 'block';
        } else {
            // error
            messageDiv.textContent = body.error || 'Error al actualizar el cliente.';
            messageDiv.style.display = 'block';
        }
    })
    .catch(error => {
        console.error(error);
        messageDiv.textContent = `Error de conexión: ${error.message}`;
        messageDiv.style.display = 'block';
    });
}

