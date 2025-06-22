document.getElementById('formConsultar').addEventListener('submit', function(event) {
    event.preventDefault();
    consultar();
});

function consultar() {
    const cedula = document.getElementById("cedula").value.trim();
    const idMascota = document.getElementById("idMascota").value.trim();
    const messageDiv = document.getElementById("message");
    const resultContainer = document.getElementById("resultContainer");
    const resultsTableBody = document.getElementById("resultsTableBody");

    messageDiv.style.display = "none";
    messageDiv.textContent = "";
    resultContainer.style.display = "none";
    resultsTableBody.innerHTML = "";

    if (!cedula && !idMascota) {
        messageDiv.textContent = "Por favor, ingrese una cédula o un ID de mascota.";
        messageDiv.style.display = "block";
        return;
    }

    const queryParams = new URLSearchParams();
    if (cedula) queryParams.append("cedula", cedula);
    if (idMascota) queryParams.append("idMascota", idMascota);

    fetch(`../back/consultar.php?${queryParams.toString()}`)
        .then((response) => {
            if (!response.ok) throw new Error("Error en la consulta.");
            return response.json();
        })
        .then((data) => {
            if (data.error) {
                messageDiv.textContent = data.error;
                messageDiv.style.display = "block";
                return;
            }

            if (data.message) {
                messageDiv.textContent = data.message;
                messageDiv.style.display = "block";
                return;
            }

            data.forEach((row) => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${row.cedulaCliente}</td>
                    <td>${row.nombreCliente}</td>
                    <td>${row.teléfono}</td>
                    <td>${row.email}</td>
                    <td>${row.dirección}</td>
                    <td>${row.cantidadDeMascotas}</td>
                    <td>${row.idMascota}</td>
                    <td>${row.nombreMascota}</td>
                    <td>${row.especie}</td>
                    <td>${row.peso}</td>
                    <td>${row.edad}</td>
                    <td>${row.genero}</td>
                    <td>${row.fechaRegistro}</td>
                    <td>${row.razaMascota}</td>
                    <td><img src="${row.foto}" alt="Foto Mascota" style="max-width: 100px;"></td>
                `;
                resultsTableBody.appendChild(tr);
            });

            resultContainer.style.display = "block";
        })
        .catch((error) => {
            console.error(error);
            messageDiv.textContent = "Ocurrió un error al procesar la solicitud.";
            messageDiv.style.display = "block";
        });
}
