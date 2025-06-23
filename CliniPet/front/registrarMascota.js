// Espera a que el DOM esté completamente cargado
document.addEventListener("DOMContentLoaded", () => {
    const especieInput = document.getElementById("especie");
    const razaSelect = document.getElementById("raza");
    const form = document.getElementById("formRegistrarMascota");
    const responseMessage = document.getElementById("responseMessage");

    // Cargar razas dinámicamente al seleccionar especie
    especieInput.addEventListener("change", async () => {
        const especie = especieInput.value;

        // Si no se seleccionó especie, se limpia el select de razas
        if (!especie) {
            razaSelect.innerHTML = '<option value="">Seleccione una especie primero</option>';
            return;
        }

        try {
            // Llama a la API para obtener las razas según especie
            const response = await fetch(`/CliniPet/back/obtenerRazas.php?especie=${encodeURIComponent(especie)}`);
            if (!response.ok) {
                throw new Error(`Error al cargar razas: ${response.statusText}`);
            }
            const razas = await response.json();

            if (razas.error) {
                throw new Error(razas.error);
            }

            // Si hay razas, se cargan en el select
            if (razas.length > 0) {
                razaSelect.innerHTML = '<option value="">Seleccione una raza</option>';
                razas.forEach(raza => {
                    const option = document.createElement('option');
                    option.value = raza.RazaID;
                    option.textContent = raza.Nombre;
                    razaSelect.appendChild(option);
                });
            } else {
                razaSelect.innerHTML = '<option value="">No se encontraron razas</option>';
            }
        } catch (error) {
            console.error(error);
            razaSelect.innerHTML = `<option value="">Error: ${error.message}</option>`;
        }
    });

    // Envío del formulario de registro de mascota
    form.addEventListener("submit", async (event) => {
        event.preventDefault(); // Evita que recargue la página

        // Captura de datos del formulario
        const nombreMascota = document.getElementById("nombreMascota").value.trim();
        const peso = document.getElementById("peso").value.trim();
        const edad = document.getElementById("edad").value.trim();
        const cedulaCliente = document.getElementById("cedulaCliente").value.trim();
        const genero = document.getElementById("genero").value;
        const fotoInput = document.getElementById("foto");
        const razaID = razaSelect.value;
        const especie = especieInput.value;

        // Validación de campos vacíos
        if (!nombreMascota || !peso || !edad || !cedulaCliente || !genero || !razaID || !especie) {
            alert("Por favor complete todos los campos.");
            return;
        }

        // Validación de archivo de foto
        if (!fotoInput.files[0]) {
            alert("Por favor seleccione una foto.");
            return;
        }

        const foto = fotoInput.files[0];
        if (!foto.type.startsWith('image/')) {
            alert("El archivo de foto debe ser una imagen.");
            return;
        }

        // Crear objeto FormData con todos los datos
        const formData = new FormData();
        formData.append("nombreMascota", nombreMascota);
        formData.append("especie", especie);
        formData.append("peso", peso);
        formData.append("edad", edad);
        formData.append("cedulaCliente", cedulaCliente);
        formData.append("razaID", razaID);
        formData.append("genero", genero);
        formData.append("foto", foto);

        try {
            // Enviar datos al backend
            const response = await fetch("/CliniPet/back/registrarMascota.php", {
                method: "POST",
                body: formData,
            });

            const data = await response.json();

            if (response.ok) {
                // Mostrar mensaje de éxito
                responseMessage.innerText = data.mensaje || "Mascota registrada con éxito.";
                responseMessage.style.color = "green";
                form.reset(); // Limpia el formulario
                razaSelect.innerHTML = '<option value="">Seleccione una raza</option>';
            } else {
                // Mostrar mensaje de error del servidor
                responseMessage.innerText = data.error || "No se pudo registrar la mascota.";
                responseMessage.style.color = "red";
                console.error("Error:", data.error);
            }
        } catch (error) {
            // Error de red o inesperado
            console.error("Error al registrar la mascota:", error);
            responseMessage.innerText = "Ocurrió un error al registrar la mascota.";
            responseMessage.style.color = "red";
        }
    });
});
