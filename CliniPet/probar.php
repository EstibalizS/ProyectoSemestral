<?php
require_once 'conexion.php';

$sql = "SELECT TOP 1 * FROM Cliente"; // Cambia a cualquier tabla de prueba

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(json_encode(["error" => sqlsrv_errors()]));
}

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    print_r($row);
}
