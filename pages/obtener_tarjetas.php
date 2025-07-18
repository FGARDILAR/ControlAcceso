<?php
include __DIR__ . '/../includes/conexion.php'; // Conexión a la base de datos

header('Content-Type: application/json'); // Asegura que la respuesta sea JSON

// Manejo de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

$sql = "SELECT NoTarjeta, estado FROM tarjeta WHERE estado = 'OCUPADA'";
$result = sqlsrv_query($conn, $sql);

if (!$result) {
    die(json_encode(["error" => "Error en la consulta SQL"]));
}

$tarjetas = [];
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $tarjetas[] = $row;
}

echo json_encode($tarjetas);
?>
