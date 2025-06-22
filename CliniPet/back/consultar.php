<?php
require_once '../conexion.php'; // Configuración de conexión a BD
header('Content-Type: application/json');

$cedula = !empty($_GET['cedula']) ? $_GET['cedula'] : null;
$idMascota = !empty($_GET['idMascota']) ? intval($_GET['idMascota']) : null;

if ($cedula === null && $idMascota === null) {
    http_response_code(400);
    echo json_encode(["error" => "Se debe proporcionar una cédula o un ID de mascota."]);
    exit;
}

$sql = "{CALL ConsultarClienteYMascota(?, ?)}";
$params = [$cedula, $idMascota];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    http_response_code(500);
    $errors = sqlsrv_errors();
    echo json_encode([
        "error" => "Error al ejecutar la consulta.",
        "detalle" => $errors
    ]);
    exit;
}


$results = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $results[] = [
        "cedulaCliente" => $row["CedulaCliente"],
        "nombreCliente" => $row["NombreCliente"],
        "teléfono" => $row["Teléfono"],
        "email" => $row["Email"],
        "dirección" => $row["Dirección"],
        "cantidadDeMascotas" => $row["CantidadDeMascotas"],
        "idMascota" => $row["IDMascota"],
        "nombreMascota" => $row["NombreMascota"],
        "especie" => $row["Especie"],
        "peso" => $row["Peso"],
        "edad" => $row["Edad"],
        "genero" => $row["Genero"],
        "fechaRegistro" => $row["FechaRegistro"] ? $row["FechaRegistro"]->format('Y-m-d') : null,
        "razaMascota" => $row["RazaMascota"],
        "foto" => $row["Foto"]
    ];
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

if (empty($results)) {
    http_response_code(404);
    echo json_encode(["message" => "No se encontraron resultados."]);
} else {
    echo json_encode($results);
}
