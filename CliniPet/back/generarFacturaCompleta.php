<?php
require_once '../conexion.php';
header('Content-Type: application/json');

class FacturaController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Genera el id de la factura
    public function generarFactura() {
        $json = file_get_contents('php://input');
        $request = json_decode($json);

        if ($request === null) {
            http_response_code(400);
            echo json_encode(["error" => "The request field is required."]);
            return;
        }

        if (!isset($request->CedulaCliente)) {
            http_response_code(400);
            echo json_encode(["error" => "The 'CedulaCliente' field is required."]);
            return;
        }

        $idMascota = isset($request->IDMascota) ? $request->IDMascota : null;

        $sql = "{CALL GenerarFactura(?, ?)}";
        $params = [$request->CedulaCliente, $idMascota];

        $stmt = sqlsrv_query($this->conn, $sql, $params);

        if ($stmt === false) {
            http_response_code(400);
            echo json_encode(["error" => "Error al generar la factura.", "detalle" => sqlsrv_errors()]);
            return;
        }

        sqlsrv_next_result($stmt);
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_NUMERIC);
        $facturaId = $row[0];

        sqlsrv_free_stmt($stmt);

        echo json_encode(["mensaje" => "Factura generada exitosamente", "facturaId" => $facturaId]);
    }

    // Obtener mascotas por cédula
    public function obtenerMascotasPorCedula($cedulaCliente) {
        $sql = "SELECT IDMascota, Nombre FROM Mascota WHERE CedulaCliente = ?";
        $params = [$cedulaCliente];

        $stmt = sqlsrv_query($this->conn, $sql, $params);

        if ($stmt === false) {
            http_response_code(400);
            echo json_encode(["error" => "Error al obtener mascotas.", "detalle" => sqlsrv_errors()]);
            return;
        }

        $mascotas = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $mascotas[] = [
                "IDMascota" => $row["IDMascota"],
                "Nombre" => $row["Nombre"]
            ];
        }

        sqlsrv_free_stmt($stmt);

        if (empty($mascotas)) {
            http_response_code(404);
            echo json_encode(["error" => "No se encontraron mascotas para esta cédula."]);
        } else {
            echo json_encode($mascotas);
        }
    }

    // Registrar servicio a mascota
    public function registrarServicioMascota() {
        $json = file_get_contents('php://input');
        $request = json_decode($json);

        if ($request === null) {
            http_response_code(400);
            echo json_encode(["error" => "The request field is required."]);
            return;
        }

        if (!isset($request->IDMascota) || !isset($request->IDITEM) || !isset($request->IDFactura)) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required fields."]);
            return;
        }

        $sql = "{CALL RegistrarServicioMascota(?, ?, ?)}";
        $params = [$request->IDMascota, $request->IDITEM, $request->IDFactura];

        $stmt = sqlsrv_query($this->conn, $sql, $params);

        if ($stmt === false) {
            http_response_code(400);
            echo json_encode(["error" => "Error al registrar servicio.", "detalle" => sqlsrv_errors()]);
            return;
        }

        sqlsrv_free_stmt($stmt);
        echo json_encode(["mensaje" => "Servicio registrado exitosamente"]);
    }

    // Comprar producto
    public function comprarProducto() {
        $json = file_get_contents('php://input');
        $request = json_decode($json);

        if ($request === null) {
            http_response_code(400);
            echo json_encode(["error" => "The request field is required."]);
            return;
        }

        if (!isset($request->IDITEM) || !isset($request->Cantidad) || !isset($request->IDFactura)) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required fields."]);
            return;
        }

        $sql = "{CALL ComprarProducto(?, ?, ?)}";
        $params = [$request->IDITEM, $request->Cantidad, $request->IDFactura];

        $stmt = sqlsrv_query($this->conn, $sql, $params);

        if ($stmt === false) {
            http_response_code(400);
            echo json_encode(["error" => "Error al comprar producto.", "detalle" => sqlsrv_errors()]);
            return;
        }

        sqlsrv_free_stmt($stmt);
        echo json_encode(["mensaje" => "Producto agregado a ventas exitosamente"]);
    }

    // Obtener productos
    public function obtenerProductos() {
        $sql = "SELECT IDITEM, NombreProducto FROM Servicio_Producto WHERE Tipo = 'Producto'";
        $stmt = sqlsrv_query($this->conn, $sql);

        if ($stmt === false) {
            http_response_code(400);
            echo json_encode(["error" => "Error al obtener productos.", "detalle" => sqlsrv_errors()]);
            return;
        }

        $productos = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $productos[] = [
                "idItem" => $row["IDITEM"],
                "nombre" => $row["NombreProducto"]
            ];
        }

        sqlsrv_free_stmt($stmt);

        if (empty($productos)) {
            http_response_code(404);
            echo json_encode(["error" => "No se encontraron productos."]);
        } else {
            echo json_encode($productos);
        }
    }

    // Obtener servicios
    public function obtenerServicios() {
        $sql = "SELECT IDITEM, NombreProducto FROM Servicio_Producto WHERE Tipo = 'Servicio'";
        $stmt = sqlsrv_query($this->conn, $sql);

        if ($stmt === false) {
            http_response_code(400);
            echo json_encode(["error" => "Error al obtener servicios.", "detalle" => sqlsrv_errors()]);
            return;
        }

        $servicios = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $servicios[] = [
                "idItem" => $row["IDITEM"],
                "nombre" => $row["NombreProducto"]
            ];
        }

        sqlsrv_free_stmt($stmt);

        if (empty($servicios)) {
            http_response_code(404);
            echo json_encode(["error" => "No se encontraron servicios."]);
        } else {
            echo json_encode($servicios);
        }
    }

    // Completar factura
    public function completarFactura() {
        $json = file_get_contents('php://input');
        $request = json_decode($json);

        if ($request === null) {
            http_response_code(400);
            echo json_encode(["error" => "The request body is required."]);
            return;
        }

        if (!isset($request->IDFactura) || $request->IDFactura <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "The 'IDFactura' field is required and must be a positive integer."]);
            return;
        }

        $sql = "{CALL CompletarFactura(?)}";
        $params = [$request->IDFactura];

        $stmt = sqlsrv_query($this->conn, $sql, $params);

        if ($stmt === false) {
            http_response_code(400);
            echo json_encode(["error" => "Error al completar factura.", "detalle" => sqlsrv_errors()]);
            return;
        }

        $rowsAffected = sqlsrv_rows_affected($stmt);
        sqlsrv_free_stmt($stmt);

        if ($rowsAffected > 0) {
            echo json_encode(["mensaje" => "La factura ha sido completada.", "idFactura" => $request->IDFactura]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se encontró una factura con el ID proporcionado."]);
        }
    }

    // Ver factura
    public function verFactura($idFactura) {
        if ($idFactura <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "El ID de la factura es inválido."]);
            return;
        }

        $detalles = $this->obtenerDetallesFactura($idFactura);
        $resumen = $this->obtenerResumenFactura($idFactura);

        if (empty($detalles)) {
            http_response_code(404);
            echo json_encode(["error" => "No se encontraron productos para la factura proporcionada."]);
            return;
        }

        echo json_encode([
            "DetallesFactura" => $detalles,
            "ResumenFactura" => $resumen
        ]);
    }

    private function obtenerDetallesFactura($idFactura) {
        $sql = "SELECT sp.NombreProducto, v.CantidadVendida, v.PrecioBruto, v.ITBMSLinea, v.totalLinea 
                FROM Venta v 
                JOIN Servicio_Producto sp ON v.IDITEM = sp.IDITEM 
                WHERE v.IDFactura = ?";
        
        $stmt = sqlsrv_query($this->conn, $sql, [$idFactura]);
        if ($stmt === false) return [];

        $productos = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $productos[] = [
                "NombreProducto" => $row["NombreProducto"],
                "CantidadVendida" => $row["CantidadVendida"],
                "PrecioUnitario" => $row["PrecioBruto"],
                "ITBMSLinea" => $row["ITBMSLinea"],
                "TotalLinea" => $row["totalLinea"]
            ];
        }

        sqlsrv_free_stmt($stmt);
        return $productos;
    }

    private function obtenerResumenFactura($idFactura) {
        $sql = "SELECT f.Fecha, f.subtotalf, f.ITBMSFactura, f.totalFactura 
                FROM Factura f 
                WHERE f.IDFactura = ?";
        
        $stmt = sqlsrv_query($this->conn, $sql, [$idFactura]);
        if ($stmt === false) return null;

        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        sqlsrv_free_stmt($stmt);

        if (!$row) return null;

        return [
            "Fecha" => $row["Fecha"]->format('Y-m-d H:i:s'),
            "Subtotal" => $row["subtotalf"],
            "ITBMSFactura" => $row["ITBMSFactura"],
            "TotalFactura" => $row["totalFactura"]
        ];
    }
}

// Uso del controlador
$controller = new FacturaController($conn);

// Router básico
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Mapeo de rutas
if ($method == 'POST' && $path == '/api/generarFacturaCompleta/generar') {
    $controller->generarFactura();
} elseif ($method == 'GET' && preg_match('/\/api\/generarFacturaCompleta\/mascotas\/(.+)/', $path, $matches)) {
    $controller->obtenerMascotasPorCedula($matches[1]);
} elseif ($method == 'POST' && $path == '/api/generarFacturaCompleta/registrar-servicio') {
    $controller->registrarServicioMascota();
} elseif ($method == 'POST' && $path == '/api/generarFacturaCompleta/comprar-producto') {
    $controller->comprarProducto();
} elseif ($method == 'GET' && $path == '/api/generarFacturaCompleta/productos') {
    $controller->obtenerProductos();
} elseif ($method == 'GET' && $path == '/api/generarFacturaCompleta/servicios') {
    $controller->obtenerServicios();
} elseif ($method == 'POST' && $path == '/api/generarFacturaCompleta/completar-factura') {
    $controller->completarFactura();
} elseif ($method == 'GET' && preg_match('/\/api\/generarFacturaCompleta\/verFactura\/(\d+)/', $path, $matches)) {
    $controller->verFactura($matches[1]);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint no encontrado"]);
}
?>