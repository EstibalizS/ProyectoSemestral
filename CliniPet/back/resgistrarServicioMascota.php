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

// Validar que los campos requeridos existan
if (!isset($request->IDMascota) || !isset($request->IDITEM) || !isset($request->IDFactura)) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields (IDMascota, IDITEM or IDFactura)."]);
    exit;
}

$sql = "{CALL RegistrarServicioMascota(?, ?, ?)}";
$params = [
    $request->IDMascota,
    $request->IDITEM,
    $request->IDFactura
];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    http_response_code(400);
    $errors = sqlsrv_errors();
    echo json_encode([
        "error" => "Error al registrar el servicio.",
        "detalle" => $errors
    ]);
    exit;
}

// Liberar recursos
sqlsrv_free_stmt($stmt);

echo json_encode(["mensaje" => "Servicio registrado exitosamente"]);
?>