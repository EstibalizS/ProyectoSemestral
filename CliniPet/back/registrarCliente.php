<?php
require_once '../conexion.php';     // Archivo de conexión a SQL Server

header('Content-Type: application/json');  // Respuesta será en formato JSON

// Lee el cuerpo de la solicitud en formato JSON
$input = json_decode(file_get_contents('php://input'), true);

// Verifica si el JSON es válido
if (!$input) {
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(["error" => "El cuerpo de la solicitud está vacío o malformado."]);
    exit;
}

// Obtener campos/ datos del cliente desde el JSON
$cedula = $input['Cedula'] ?? null;
$nombre = $input['NombreCliente'] ?? null;
$telefono = $input['Teléfono'] ?? null;
$email = $input['Email'] ?? null;
$direccion = $input['Dirección'] ?? null;
$cantidad = $input['CantidadDeMascotas'] ?? null;

// Verifica que todos los campos estén completos
if (!$cedula || !$nombre || !$telefono || !$email || !$direccion || $cantidad === null) {
    http_response_code(400);
    echo json_encode(["error" => "Todos los campos son obligatorios."]);
    exit;
}

// Llama al procedimiento almacenado para registrar al cliente
$sql = "{CALL RegistrarCliente(?, ?, ?, ?, ?, ?)}";
$params = [$cedula, $nombre, $telefono, $email, $direccion, $cantidad];

$stmt = sqlsrv_query($conn, $sql, $params);

// Si ocurre un error al ejecutar el procedimiento
if ($stmt === false) {
    http_response_code(500); // Error interno del servidor
    $errors = sqlsrv_errors();

    // Solo tomam el último mensaje, que es el del RAISERROR (el amigable)
    $ultimoError = end($errors);
    $mensajeAmigable = $ultimoError['message'] ?? 'Error al registrar el cliente.';

    // Limpia el mensaje si viene con etiquetas ODBC
    if (strpos($mensajeAmigable, ']') !== false) {
        $partes = explode(']', $mensajeAmigable);
        $mensajeAmigable = trim(end($partes));
    }

    echo json_encode(["error" => $mensajeAmigable]);
    exit;
}

// Libera recursos y cierra la conexión
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

// Respuesta exitosa
echo json_encode(["mensaje" => "Cliente registrado exitosamente."]);
