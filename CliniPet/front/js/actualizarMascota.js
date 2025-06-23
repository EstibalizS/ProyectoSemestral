document.getElementById('formActualizarMascota').addEventListener('submit', async function (event) {
    event.preventDefault();

    // Obtener valores del formulario
    const idMascota = document.getElementById('idMascota').value.trim();
    const nuevoPeso = document.getElementById('nuevoPeso').value.trim();
    const nuevaEdad = document.getElementById('nuevaEdad').value.trim();
    const resultContainer = document.getElementById('resultContainer');

    // Limpiar mensajes previos
    resultContainer.style.display = "none";
    resultContainer.innerHTML = "";

    // Validar campos
    if (!idMascota || !nuevoPeso || !nuevaEdad) {
        resultContainer.style.display = "block";
        resultContainer.innerHTML = `<p class="error">Por favor complete todos los campos.</p>`;
        return;
    }

    try {
        // Preparar los datos como x-www-form-urlencoded
        const formData = new URLSearchParams();
        formData.append('idMascota', idMascota);
        formData.append('nuevoPeso', nuevoPeso);
        formData.append('nuevaEdad', nuevaEdad);

        // Enviar solicitud PUT al backend PHP
        const response = await fetch('../back/actualizarMascota.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: formData.toString()
        });

        const result = await response.json();

        // Mostrar el resultado
        resultContainer.style.display = "block";
        if (response.ok) {
            resultContainer.innerHTML = `<p class="success">${result.message || "Mascota actualizada exitosamente."}</p>`;
        } else {
            resultContainer.innerHTML = `<p class="error">${result.error || "Error al actualizar la mascota."}</p>`;
        }

    } catch (error) {
        resultContainer.style.display = "block";
        resultContainer.innerHTML = `<p class="error">Error de conexión: ${error.message}</p>`;
    }
});
