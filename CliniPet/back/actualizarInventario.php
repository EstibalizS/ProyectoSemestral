<?php
require_once '../conexion.php'; // Ajusta la ruta según tu estructura
header('Content-Type: application/json');

// Recibir datos del método PUT desde query string ($_GET)
$idMascota = isset($_GET['idMascota']) ? trim($_GET['idMascota']) : null;
$nuevoPeso = isset($_GET['nuevoPeso']) ? trim($_GET['nuevoPeso']) : null;
$nuevaEdad = isset($_GET['nuevaEdad']) ? trim($_GET['nuevaEdad']) : null;

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

// Verificar si el ID existe en la tabla Mascota
$sqlCheck = "SELECT COUNT(1) AS count FROM Mascota WHERE IDMascota = ?";
$paramsCheck = [$idMascota];
$stmtCheck = sqlsrv_query($conn, $sqlCheck, $paramsCheck);

if ($stmtCheck === false) {
    http_response_code(500);
    $errors = sqlsrv_errors();
    echo json_encode(["error" => "Error al verificar el ID de la mascota.", "detalle" => $errors]);
    exit;
}

$row = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);
if ($row['count'] == 0) {
    http_response_code(404);
    echo json_encode(["error" => "El ID de la mascota no existe."]);
    exit;
}
sqlsrv_free_stmt($stmtCheck);

// Llamar al procedimiento almacenado para actualizar la mascota
$sqlUpdate = "{CALL ActualizarMascota(?, ?, ?)}";
$paramsUpdate = [$idMascota, floatval($nuevoPeso), intval($nuevaEdad)];
$stmtUpdate = sqlsrv_query($conn, $sqlUpdate, $paramsUpdate);

if ($stmtUpdate === false) {
    http_response_code(500);
    $errors = sqlsrv_errors();
    echo json_encode(["error" => "Error al actualizar la mascota.", "detalle" => $errors]);
    exit;
}

http_response_code(200);
echo json_encode(["message" => "Mascota actualizada exitosamente."]);

sqlsrv_free_stmt($stmtUpdate);
sqlsrv_close($conn);
