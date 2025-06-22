document.getElementById('buscarMascotas').addEventListener('click', async () => {
    const cedula = document.getElementById('cedula').value.trim();
    const mascotaSelect = document.getElementById('mascota');
    const mensaje = document.getElementById('mensaje');

    if (!cedula) {
        mensaje.style.display = 'block';
        mensaje.textContent = 'Por favor, ingrese la cédula del cliente.';
        return;
    }

    try {
        // Llamada al PHP para obtener las mascotas del cliente
        const response = await fetch(`../back/consultar.php?cedula=${cedula}`);
        if (!response.ok) {
            throw new Error('Error al obtener las mascotas del cliente.');
        }

        const mascotas = await response.json();

        // Depuración:
        console.log('Datos recibidos del PHP:', mascotas);

        // Limpia el combo box
        mascotaSelect.innerHTML = '<option value="" disabled selected>Seleccione una mascota</option>';

        // Llena el combo box con las mascotas obtenidas
        mascotas.forEach(mascota => {
            console.log(`Mascota cargada: ${mascota.nombre}, IDMascota: ${mascota.idMascota}`);
            const option = document.createElement('option');
            option.value = mascota.idMascota; // Ajusta según el atributo de tu PHP
            option.textContent = mascota.nombre;
            mascotaSelect.appendChild(option);
        });

        mascotaSelect.disabled = false;
        mensaje.style.display = 'none';
        document.getElementById('siguiente').disabled = false;

    } catch (error) {
        mensaje.style.display = 'block';
        mensaje.textContent = error.message || 'Hubo un error al procesar la solicitud.';
    }
});

// Habilitar botón "Siguiente" si hay algo escrito en el campo de cédula
document.getElementById('cedula').addEventListener('input', () => {
    const cedula = document.getElementById('cedula').value.trim();
    const siguienteButton = document.getElementById('siguiente');

    siguienteButton.disabled = !cedula; // Habilitar o deshabilitar botón
});

// Lógica para el botón "Siguiente"
document.getElementById('siguiente').addEventListener('click', async () => {
    const cedulaCliente = document.getElementById('cedula').value.trim();
    const mascotaSelect = document.getElementById('mascota');
    const idMascota = mascotaSelect.value;

    idMascotaGlobal = idMascota;

    console.log('CedulaCliente ingresada:', cedulaCliente);
    console.log('IDMascota seleccionado en el combo box:', idMascota);

    if (!cedulaCliente) {
        const mensaje = document.getElementById('mensaje');
        mensaje.style.display = 'block';
        mensaje.classList.add('error');
        mensaje.textContent = 'Por favor, ingrese la cédula del cliente antes de continuar.';
        return;
    }

    const requestData = {
        CedulaCliente: cedulaCliente, 
        IDMascota: idMascota
    };

    // Depuración: Verifica los datos que se enviarán al servidor
    console.log('Datos que se enviarán al PHP:', requestData);

    try {
        // Llama al PHP para generar la factura
        const response = await fetch('../back/generarFactura.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(requestData)
        });

        if (!response.ok) {
            throw new Error('Error al generar la factura.');
        }

        const data = await response.json(); // Obtén la respuesta
        idFacturaGlobal = data.facturaId;

        const mensaje = document.getElementById('mensaje');
        mensaje.style.display = 'block';
        mensaje.classList.remove('error'); // Elimina la clase de error
        mensaje.classList.add('success'); // Aplica la clase de éxito
        mensaje.textContent = `Factura generada con éxito. ID de factura: ${data.facturaId}`;

        // Llamado a la función que muestra la prefactura
        mostrarPrefacturaVacia();

        // Colocar el id de la factura 
        document.getElementById('prefactura-id').textContent = data.facturaId;

    } catch (error) {
        console.error('Error en la generación de factura:', error);

        const mensaje = document.getElementById('mensaje');
        mensaje.style.display = 'block';
        mensaje.classList.remove('success'); // Elimina la clase de éxito
        mensaje.classList.add('error'); // Aplica la clase de error
        mensaje.textContent = `Hubo un error al generar la factura: ${error.message}`;
    }
});

// Función que muestra la prefactura
function mostrarPrefacturaVacia() {
    const prefactura = document.getElementById("prefactura");
    const mensaje = document.getElementById('mensaje');

    // Limpiar el contenido de la prefactura (si ya tiene algo de antes)
    document.getElementById("prefactura-id").textContent = '';

    // Mostrar el recuadro de la prefactura vacía
    prefactura.style.display = "block";

    // Ocultar los formularios de producto y servicio hasta que el usuario elija
    document.getElementById("form-producto").style.display = "none";
    document.getElementById("form-servicio").style.display = "none";

    // Habilitar los botones de producto y servicio
    document.getElementById("btnServicios").disabled = false;
    document.getElementById("btnProductos").disabled = false;
}

// Función que muestra el formulario de productos o servicios
function mostrarFormulario(tipo) {
    // Ocultar ambos formularios de productos y servicios
    document.getElementById("form-producto").style.display = "none";
    document.getElementById("form-servicio").style.display = "none";

    // Mostrar el formulario correspondiente
    if (tipo === "producto") {
        document.getElementById("form-producto").style.display = "block";
        // Cargar los productos desde el PHP
        cargarProductos();
    } else if (tipo === "servicio") {
        document.getElementById("form-servicio").style.display = "block";
        // Cargar los servicios desde el PHP
        cargarServicios();
    }
}

// Funciones para manejar los botones de "Servicios" y "Productos"
document.getElementById("btnServicios").addEventListener("click", function() {
    mostrarFormulario("servicio");
    // Rellenar el id de la mascota
    if (idMascotaGlobal) {
        const mascotaInput = document.getElementById('mascota-id'); 
        mascotaInput.value = idMascotaGlobal;
    }
});

document.getElementById("btnProductos").addEventListener("click", function() {
    mostrarFormulario("producto");
});

// Función para cargar los nombres de los productos 
async function cargarProductos() {
    try {
        const response = await fetch('../back/cargarProductos.php'); 
        if (!response.ok) throw new Error('Error al cargar productos.');
        const productos = await response.json();

        // Limpiar los productos anteriores
        const productosSelect = document.getElementById("productos");
        productosSelect.innerHTML = '<option value="" disabled selected>Seleccione un producto</option>';

        productos.forEach(producto => {
            const option = document.createElement('option');
            option.value = producto.idItem;
            option.textContent = producto.nombre;
            productosSelect.appendChild(option);
        });

    } catch (error) {
        console.error('Error al cargar productos:', error);
    }
}

// Función para cargar los nombres de los servicios
async function cargarServicios() {
    try {
        const response = await fetch('../back/cargarServicios.php'); 
        if (!response.ok) throw new Error('Error al cargar servicios.');
        const servicios = await response.json();

        // Limpiar los servicios anteriores
        const serviciosSelect = document.getElementById("servicios");
        serviciosSelect.innerHTML = '<option value="" disabled selected>Seleccione un servicio</option>';

        servicios.forEach(servicio => {
            const option = document.createElement('option');
            option.value = servicio.idItem;
            option.textContent = servicio.nombre;
            serviciosSelect.appendChild(option);
        });

    } catch (error) {
        console.error('Error al cargar servicios:', error);
    }
}

// Función para agregar un servicio
document.getElementById("btnAgregarServicio").addEventListener("click", async function () {
    const idItem = document.getElementById("servicios").value;
    const idMascota = idMascotaGlobal; 
    const idFactura = idFacturaGlobal;

    if (!idItem) {
        const mensaje = document.getElementById('mensaje');
        mensaje.style.display = 'block';
        mensaje.classList.add('error');
        mensaje.textContent = 'Por favor, seleccione un servicio.';
        return;
    }

    // Para verificar lo que se está enviando 
    const requestData = { idMascota: idMascota, iditem: idItem, idFactura: idFactura };

    console.log('Datos que se enviarán al PHP para agregar servicio:', requestData);

    try {
        const response = await fetch('../back/registrarServicio.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(requestData)
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Error al registrar el servicio.');
        }

        const mensaje = document.getElementById('mensaje');
        mensaje.style.display = 'block';
        mensaje.classList.remove('error');
        mensaje.classList.add('success');
        mensaje.textContent = 'Servicio agregado correctamente. Puedes agregar otro servicio si lo deseas.';

    } catch (error) {
        const mensaje = document.getElementById('mensaje');
        mensaje.style.display = 'block';
        mensaje.classList.remove('success');
        mensaje.classList.add('error');
        mensaje.textContent = `Hubo un error al agregar el servicio: ${error.message}`;
    }
});

// Función para agregar un producto
document.getElementById("btnAgregarProducto").addEventListener("click", async function () {
    const idItem = document.getElementById("productos").value;
    const cantidad = document.getElementById("producto-cantidad").value.trim();
    const idFactura = idFacturaGlobal;

    if (!idItem || !cantidad || isNaN(cantidad) || cantidad <= 0) {
        const mensaje = document.getElementById('mensaje');
        mensaje.style.display = 'block';
        mensaje.classList.add('error');
        mensaje.textContent = 'Por favor, seleccione un producto y una cantidad válida.';
        return;
    }

    // Para verificar lo que será enviado al PHP
    const requestData = { iditem: idItem, cantidad: parseInt(cantidad), idFactura: idFactura };

    console.log('Datos que se enviarán al PHP para agregar producto:', requestData);

    try {
        const response = await fetch('../back/comprarProducto.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(requestData)
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Error al registrar el producto.');
        }

        const mensaje = document.getElementById('mensaje');
        mensaje.style.display = 'block';
        mensaje.classList.remove('error');
        mensaje.classList.add('success');
        mensaje.textContent = 'Producto agregado correctamente. Puedes agregar otro producto si lo deseas.';
        // Reinicia el campo de cantidad pero deja el producto seleccionado
        document.getElementById("producto-cantidad").value = '';

    } catch (error) {
        console.error("Error al agregar producto:", error);
        const mensaje = document.getElementById('mensaje');
        mensaje.style.display = 'block';
        mensaje.classList.remove('success');
        mensaje.classList.add('error');
        mensaje.textContent = `Hubo un error al agregar el producto: ${error.message}`;
    }
});

// Función para finalizar la factura
async function finalizarFactura() {
    const idFactura = idFacturaGlobal;

    if (!idFactura) {
        const mensaje = document.getElementById('mensaje');
        mensaje.style.display = 'block';
        mensaje.classList.add('error');
        mensaje.textContent = 'No se encontró el ID de la factura.';
        return;
    }

    const requestBody = {
        IDFactura: idFactura 
    };

    try {
        const response = await fetch("../back/completarFactura.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json" 
            },
            body: JSON.stringify(requestBody) 
        });

        if (!response.ok) {
            throw new Error(`Error al completar la factura. Código: ${response.status}`);
        }

        const data = await response.json();

        if (data.mensaje) {
            const mensaje = document.getElementById('mensaje');
            mensaje.style.display = 'block';
            mensaje.classList.remove('error');
            mensaje.classList.add('success');
            mensaje.textContent = data.mensaje; // Mostrar mensaje de éxito

            // Redirigir a la página de detalles de la factura
            window.location.href = `verFactura.html?idFactura=${idFactura}`;
        } else {
            const mensaje = document.getElementById('mensaje');
            mensaje.style.display = 'block';
            mensaje.classList.remove('success');
            mensaje.classList.add('error');
            mensaje.textContent = 'Hubo un error al completar la factura: ' + (data.error || "Error desconocido");
        }
    } catch (error) {
        console.error("Error al completar la factura:", error);
        const mensaje = document.getElementById('mensaje');
        mensaje.style.display = 'block';
        mensaje.classList.remove('success');
        mensaje.classList.add('error');
        mensaje.textContent = `Hubo un error al completar la factura: ${error.message}`;
    }
}
