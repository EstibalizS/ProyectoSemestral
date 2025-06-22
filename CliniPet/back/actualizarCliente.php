<?php
require_once '../conexion.php'; // Conexión a BD
header('Content-Type: application/json');

// Recibir parámetros por método PUT (se envían en el cuerpo o query string)
parse_str(file_get_contents("php://input"), $put_vars);

$cedula = !empty($put_vars['cedula']) ? $put_vars['cedula'] : null;
$telefono = !empty($put_vars['telefono']) ? $put_vars['telefono'] : null;
$email = !empty($put_vars['email']) ? $put_vars['email'] : null;
$direccion = !empty($put_vars['direccion']) ? $put_vars['direccion'] : null;

// Validaciones
if (!$cedula) {
    http_response_code(400);
    echo json_encode(["error" => "La cédula no puede estar vacía."]);
    exit;
}

if (!$telefono) {
    http_response_code(400);
    echo json_encode(["error" => "El campo de teléfono debe ser llenado."]);
    exit;
}

if (!$email) {
    http_response_code(400);
    echo json_encode(["error" => "El campo de correo electrónico debe ser llenado."]);
    exit;
}

if (!$direccion) {
    http_response_code(400);
    echo json_encode(["error" => "El campo de dirección debe ser llenado."]);
    exit;
}

// Preparar llamada al procedimiento almacenado
$sql = "{CALL ActualizarCliente(?, ?, ?, ?)}";
$params = [$cedula, $telefono, $email, $direccion];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    http_response_code(500);
    $errors = sqlsrv_errors();
    echo json_encode([
        "error" => "Error al ejecutar la actualización.",
        "detalle" => $errors
    ]);
    exit;
}

// Si todo bien, retornamos mensaje de éxito
http_response_code(200);
echo json_encode(["message" => "Datos del cliente actualizados exitosamente."]);

// Liberar recursos y cerrar conexión
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
