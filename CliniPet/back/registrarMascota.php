<?php
require_once '../conexion.php'; // Archivo de conexión a SQL Server

header('Content-Type: application/json'); // Respuesta será en formato JSON

// Validar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Método no permitido
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

try {
    // Validar que todos los campos requeridos estén presentes y no vacíos
    $campos = ['nombreMascota', 'especie', 'peso', 'edad', 'cedulaCliente', 'razaID', 'genero'];
    foreach ($campos as $campo) {
        if (!isset($_POST[$campo]) || trim($_POST[$campo]) === '') {
            throw new Exception("Falta el campo requerido: $campo");
        }
    }

    // Obtener datos del formulario
    $nombre = $_POST['nombreMascota'];
    $especie = $_POST['especie'];
    $peso = floatval($_POST['peso']);
    $edad = $_POST['edad'];
    $cedula = $_POST['cedulaCliente'];
    $razaID = intval($_POST['razaID']);
    $genero = $_POST['genero'];

    // Validar y obtener la imagen de la mascota
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Se requiere una foto válida para la mascota.");
    }
    $foto = file_get_contents($_FILES['foto']['tmp_name']); // Convertir imagen a binario

    // Llamada al procedimiento almacenado con todos los parámetros
    $sql = "{CALL RegistrarMascota(?, ?, ?, ?, ?, ?, ?, ?)}";
    $params = [
        $nombre,
        $especie,
        $peso,
        $edad,
        $cedula,
        $razaID,
        $genero,
        // Parametro especial para la imagen en binario
        [$foto, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STREAM(SQLSRV_ENC_BINARY), SQLSRV_SQLTYPE_VARBINARY('max')]
    ];

    $stmt = sqlsrv_query($conn, $sql, $params); // Ejecutar SP

    // Si ocurre un error, limpiar el mensaje y mostrarlo
    if ($stmt === false) {
        $errors = sqlsrv_errors();
        $mensajeLimpio = "Error al registrar la mascota.";

        if (!empty($errors[0]['message'])) {
            $sqlMsg = $errors[0]['message'];
            // Extraer mensaje después de ']'
            if (strpos($sqlMsg, ']') !== false) {
                $partes = explode(']', $sqlMsg);
                $posibleMensaje = trim(end($partes));
                if ($posibleMensaje !== '') {
                    $mensajeLimpio = $posibleMensaje;
                }
            } else {
                $mensajeLimpio = $sqlMsg;
            }
        }

        http_response_code(500); // Error del servidor
        echo json_encode(["error" => $mensajeLimpio]);
        exit;
    }

    // Liberar recursos y cerrar conexión
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);

    // Respuesta exitosa
    echo json_encode(["mensaje" => "Mascota registrada exitosamente."]);

} catch (Exception $e) {
    // Manejo de errores de validación
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}
