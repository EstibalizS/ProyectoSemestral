<?php
$serverName = "localhost";
$connectionOptions = [
    "Database" => "CliniPet",
    "Uid" => "clinipet_user",
    "PWD" => "Clinipet123!", // <- cambia esto
    "CharacterSet" => "UTF-8"
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die(json_encode(["error" => "Error al conectar a la base de datos."]));
}
