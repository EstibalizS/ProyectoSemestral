<?php
require_once '../conexion.php'; // Configuración de conexión a BD
header('Content-Type: application/json');

// Leer JSON recibido en el body
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(["error" => "El cuerpo de la solicitud es requerido y debe ser JSON válido."]);
    exit;
}

$IDITEM = isset($input['IDITEM']) ? intval($input['IDITEM']) : null;
$Cantidad = isset($input['Cantidad']) ? intval($input['Cantidad']) : null;
$IDFactura = isset($input['IDFactura']) ? intval($input['IDFactura']) : null;

// Validar campos requeridos
if ($IDITEM === null || $Cantidad === null || $IDFactura === null) {
    http_response_code(400);
    echo json_encode(["error" => "Los campos IDITEM, Cantidad y IDFactura son obligatorios."]);
    exit;
}

$sql = "{CALL ComprarProducto(?, ?, ?)}";
$params = [$IDITEM, $Cantidad, $IDFactura];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    http_response_code(500);
    $errors = sqlsrv_errors();
    echo json_encode([
        "error" => "Error al ejecutar el procedimiento almacenado.",
        "detalle" => $errors
    ]);
    exit;
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

echo json_encode(["mensaje" => "Producto agregado a ventas exitosamente."]);
