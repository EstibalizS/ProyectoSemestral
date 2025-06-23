<?php
require_once '../conexion.php'; // Asegúrate de tener la conexión bien configurada
header('Content-Type: application/json');

parse_str(file_get_contents("php://input"), $put_vars);

$idMascota = isset($put_vars['idMascota']) ? (int)$put_vars['idMascota'] : null;
$nuevoPeso = isset($put_vars['nuevoPeso']) ? (float)$put_vars['nuevoPeso'] : null;
$nuevaEdad = isset($put_vars['nuevaEdad']) ? (int)$put_vars['nuevaEdad'] : null;

// Validaciones
if (!$idMascota) {
    http_response_code(400);
    echo json_encode(["error" => "El ID de la mascota no puede estar vacío."]);
    exit;
}

if (!$nuevoPeso) {
    http_response_code(400);
    echo json_encode(["error" => "El campo de peso no puede estar vacío."]);
    exit;
}

if (!$nuevaEdad) {
    http_response_code(400);
    echo json_encode(["error" => "El campo de edad no puede estar vacío."]);
    exit;
}

// Validar existencia de la mascota
$verificarSql = "SELECT COUNT(1) as total FROM Mascota WHERE IDMascota = ?";
$verificarParams = [$idMascota];
$verificarStmt = sqlsrv_query($conn, $verificarSql, $verificarParams);

if ($verificarStmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Error al verificar la existencia de la mascota."]);
    exit;
}

$row = sqlsrv_fetch_array($verificarStmt, SQLSRV_FETCH_ASSOC);
if ($row['total'] == 0) {
    http_response_code(404);
    echo json_encode(["error" => "El ID de la mascota no existe."]);
    exit;
}

// Ejecutar procedimiento almacenado
$sql = "{CALL ActualizarMascota(?, ?, ?)}";
$params = [$idMascota, $nuevoPeso, $nuevaEdad];

$stmt = sqlsrv_query($conn, $sql, $params);
if ($stmt === false) {
    http_response_code(500);
    echo json_encode([
        "error" => "Error al actualizar la mascota.",
        "detalle" => sqlsrv_errors()
    ]);
    exit;
}

http_response_code(200);
echo json_encode(["message" => "Mascota actualizada exitosamente."]);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
