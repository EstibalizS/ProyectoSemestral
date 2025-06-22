<?php
require_once '../conexion.php'; // Configuración de conexión a BD
header('Content-Type: application/json');

// Obtener datos del cuerpo de la petición (POST)
$json = file_get_contents('php://input');
$request = json_decode($json);

// Validar que el request no sea nulo
if ($request === null) {
    http_response_code(400);
    echo json_encode(["error" => "The request body is required."]);
    exit;
}

// Validar que el IDFactura sea positivo
if (!isset($request->IDFactura) || $request->IDFactura <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "The 'IDFactura' field is required and must be a positive integer."]);
    exit;
}

$sql = "{CALL CompletarFactura(?)}";
$params = [$request->IDFactura];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    http_response_code(400);
    $errors = sqlsrv_errors();
    echo json_encode([
        "error" => "Error al completar la factura.",
        "detalle" => $errors
    ]);
    exit;
}

// Obtener filas afectadas (equivalente a ExecuteNonQueryAsync)
$rowsAffected = sqlsrv_rows_affected($stmt);

// Liberar recursos
sqlsrv_free_stmt($stmt);

// Verificar si se afectaron filas
if ($rowsAffected > 0) {
    echo json_encode(["mensaje" => "La factura ha sido completada."]);
} else {
    http_response_code(404);
    echo json_encode(["error" => "No se encontró una factura con el ID proporcionado."]);
}
?>