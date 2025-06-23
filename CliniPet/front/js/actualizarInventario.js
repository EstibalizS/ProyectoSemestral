document.addEventListener('DOMContentLoaded', () => {
    const selectItem = document.getElementById('selectItem');
    const idItemInput = document.getElementById('idItem');

    fetch('../back/listarItems.php')
        .then(res => res.json())
        .then(data => {
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.IDITEM;
                option.textContent = item.Nombre;
                selectItem.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error al cargar los productos:', error);
        });

    selectItem.addEventListener('change', () => {
        idItemInput.value = selectItem.value;
    });
});

document.getElementById('formActualizarInventario').addEventListener('submit', async function (event) {
    event.preventDefault();

    const idItem = document.getElementById('idItem').value.trim();
    const cantidadAgregada = document.getElementById('cantidadAgregada').value.trim();
    const resultContainer = document.getElementById('resultContainer');
    const resultContent = document.getElementById('resultContent');

    resultContent.innerHTML = "";
    resultContainer.style.display = "none";

    if (!idItem || !cantidadAgregada) {
        resultContent.innerHTML = `<p class="error">Por favor complete todos los campos.</p>`;
        resultContainer.style.display = "block";
        return;
    }

    const requestData = {
        IDITEM: parseInt(idItem),
        CantidadAgregada: parseInt(cantidadAgregada)
    };

    try {
        const response = await fetch("../back/actualizarInventario.php", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestData)
        });

        const data = await response.json();

        if (response.ok) {
            resultContent.innerHTML = `<p class="success">${data.mensaje}</p>`;
        } else {
            resultContent.innerHTML = `<p class="error">Error: ${data.error}</p>`;
        }

    } catch (error) {
        resultContent.innerHTML = `<p class="error">Error de conexión: ${error.message}</p>`;
    }

    resultContainer.style.display = "block";
});
