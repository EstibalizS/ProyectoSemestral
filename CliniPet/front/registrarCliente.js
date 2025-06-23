// Registrar Cliente - Validación y envío del formulario
document.getElementById("clienteForm").addEventListener("submit", async function (event) {
    event.preventDefault(); // Evita que se recargue la página

    // Obtiene los valores de los campos del formulario
    const cedula = document.getElementById("cedula").value.trim();
    const nombre = document.getElementById("nombre").value.trim();
    const telefono = document.getElementById("telefono").value.trim();
    const email = document.getElementById("email").value.trim();
    const direccion = document.getElementById("direccion").value.trim();
    const cantidadInput = document.getElementById("cantidadDeMascotas").value;
    const cantidad = parseInt(cantidadInput, 10);

    const responseDiv = document.getElementById("responseMessage");
    responseDiv.innerText = ""; // Limpia mensaje anterior

    // Expresiones regulares para validaciones
    const regexCedula = /(^[1-9]-\d{3}-\d{4}$)|(^10-\d{4}-\d{4}$)|(^E-\d{6,}$)|(^[A-Z][0-9].*)/;
    const regexTelefono = /^\d{4}-\d{4}$/;

    // Validar formato de cédula
    if (!regexCedula.test(cedula)) {
        responseDiv.innerText = "La cédula no tiene un formato válido.";
        responseDiv.style.color = "red";
        return;
    }

    // Validar formato de teléfono
    if (!regexTelefono.test(telefono)) {
        responseDiv.innerText = "El número de teléfono debe tener el formato 1234-5678.";
        responseDiv.style.color = "red";
        return;
    }

    // Validar cantidad permitida de mascotas
    if (![1, 2].includes(cantidad)) {
        responseDiv.innerText = "La cantidad de mascotas debe ser 1 o 2.";
        responseDiv.style.color = "red";
        return;
    }

    // Objeto con datos del cliente para enviar al backend
    const clienteData = {
        Cedula: cedula,
        NombreCliente: nombre,
        Teléfono: telefono,
        Email: email,
        Dirección: direccion,
        CantidadDeMascotas: cantidad
    };

    try {
        // Enviar datos al backend con fetch y método POST
        const response = await fetch("/CliniPet/back/registrarCliente.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(clienteData),
        });

        const result = await response.json(); // Leer respuesta

        if (response.ok) {
            // Éxito: mostrar mensaje y limpiar formulario
            responseDiv.innerText = result.mensaje || "Cliente registrado con éxito.";
            responseDiv.style.color = "green";
            document.getElementById("clienteForm").reset();
        } else {
            // Error enviado desde el backend
            console.log("Respuesta completa del servidor (error):", result);
            responseDiv.innerText = result.error || "No se pudo registrar al cliente.";
            responseDiv.style.color = "red";
            console.error("Detalles del error:", result.error);
        }
    } catch (error) {
        // Error de red u otro fallo general
        console.error("Error al registrar cliente:", error);
        responseDiv.innerText = "Ocurrió un error al procesar la solicitud.";
        responseDiv.style.color = "red";
    }
});
