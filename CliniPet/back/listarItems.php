<?php
require_once '../conexion.php'; // Ajusta si es necesario
header('Content-Type: application/json');

// Lista fija de productos con su ID
$items = [
    ["IDITEM" => 125, "Nombre" => "Alimento para Perros (15kg)"],
    ["IDITEM" => 126, "Nombre" => "Alimento para Gatos (10kg)"],
    ["IDITEM" => 127, "Nombre" => "Arena Sanitaria para Gatos"],
    ["IDITEM" => 128, "Nombre" => "Juguete de Cuerda para Perros"],
    ["IDITEM" => 129, "Nombre" => "Pelota de Goma para Mascotas"],
    ["IDITEM" => 130, "Nombre" => "Collar Antipulgas para Perros"],
    ["IDITEM" => 131, "Nombre" => "Collar Antipulgas para Gatos"],
    ["IDITEM" => 132, "Nombre" => "Champú Antipulgas"],
    ["IDITEM" => 133, "Nombre" => "Champú Hipoalergénico"],
    ["IDITEM" => 134, "Nombre" => "Cepillo para Mascotas"],
    ["IDITEM" => 135, "Nombre" => "Cama para Perros"],
    ["IDITEM" => 136, "Nombre" => "Cama para Gatos"],
    ["IDITEM" => 137, "Nombre" => "Transportadora Pequeña"],
    ["IDITEM" => 138, "Nombre" => "Transportadora Mediana"],
    ["IDITEM" => 139, "Nombre" => "Transportadora Grande"],
    ["IDITEM" => 140, "Nombre" => "Rascador para Gatos"],
    ["IDITEM" => 141, "Nombre" => "Plato de Comida Antideslizante"],
    ["IDITEM" => 142, "Nombre" => "Plato Doble para Mascotas"],
    ["IDITEM" => 143, "Nombre" => "Correa Retráctil para Perros"],
    ["IDITEM" => 144, "Nombre" => "Arnés para Perros"],
    ["IDITEM" => 145, "Nombre" => "Arnés para Gatos"],
    ["IDITEM" => 146, "Nombre" => "Kit de Cepillos Dentales"],
    ["IDITEM" => 147, "Nombre" => "Comida Húmeda para Perros (6 latas)"],
    ["IDITEM" => 148, "Nombre" => "Comida Húmeda para Gatos (6 latas)"],
    ["IDITEM" => 149, "Nombre" => "Snacks Dentales para Perros"],
    ["IDITEM" => 150, "Nombre" => "Snacks para Gatos"]
];

echo json_encode($items);
