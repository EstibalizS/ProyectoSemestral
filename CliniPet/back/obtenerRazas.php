<?php
require_once '../conexion.php'; // Conexión SQL Server

// Respuesta en formato JSON
header('Content-Type: application/json');

// Obtiene el parámetro 'especie' desde la URL (GET)
$especie = $_GET['especie'] ?? '';

// Valida que se haya enviado la especie
if (!$especie) {
    http_response_code(400);  // Error de solicitud
    echo json_encode(["error" => "Parámetro 'especie' es requerido."]);
    exit;
}

// Determinar el ID de especie según el valor recibido (perro o gato)
$especieId = strtolower($especie) === 'perro' ? 1 : (strtolower($especie) === 'gato' ? 2 : null);

// Valida especie
if ($especieId === null) {
    http_response_code(400);
    echo json_encode(["error" => "Especie no válida."]);
    exit;
}

// Consulta SQL para obtener razas según la especie
$sql = "SELECT RazaID, Nombre FROM Raza WHERE EspecieID = ?";
$params = [$especieId];

// Ejecuta la consulta
$stmt = sqlsrv_query($conn, $sql, $params);

// Verifica si hubo error en la consulta
if ($stmt === false) {
    http_response_code(500); // Error interno del servidor
    echo json_encode(["error" => "Error al consultar las razas."]);
    exit;
}

// Construye el arreglo de razas
$razas = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $razas[] = [
        "RazaID" => $row["RazaID"],
        "Nombre" => $row["Nombre"]
    ];
}

// Libera recursos y cierra conexión
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

// Devuelve las razas en formato JSON
echo json_encode($razas);
