<?php
require_once '../conexion.php'; // Configuración de conexión a BD
header('Content-Type: application/json');

// Obtener datos del cuerpo de la petición (POST)
$json = file_get_contents('php://input');
$request = json_decode($json);

if ($request === null) {
    http_response_code(400);
    echo json_encode(["error" => "The request field is required."]);
    exit;
}

// Validar campos requeridos
if (!isset($request->CedulaCliente)) {
    http_response_code(400);
    echo json_encode(["error" => "The 'CedulaCliente' field is required."]);
    exit;
}

// Preparar parámetros (convertir null a NULL de SQL)
$idMascota = isset($request->IDMascota) ? $request->IDMascota : null;

$sql = "{CALL GenerarFactura(?, ?)}";
$params = [
    $request->CedulaCliente,
    $idMascota
];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    http_response_code(400);
    $errors = sqlsrv_errors();
    echo json_encode([
        "error" => "Error al generar la factura.",
        "detalle" => $errors
    ]);
    exit;
}

// Obtener el ID de la factura generada
sqlsrv_next_result($stmt); // Avanzar al primer resultado (necesario para procedimientos con SELECT)
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_NUMERIC);

if ($row === false) {
    http_response_code(500);
    echo json_encode(["error" => "No se pudo obtener el ID de la factura generada."]);
    exit;
}

$facturaId = $row[0];

// Liberar recursos
sqlsrv_free_stmt($stmt);

// Devolver respuesta exitosa
echo json_encode([
    "mensaje" => "Factura generada exitosamente",
    "facturaId" => $facturaId
]);
?>