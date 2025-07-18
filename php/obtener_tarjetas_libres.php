<?php
include __DIR__ . '/../includes/conexion.php'; // Conexión a la base de datos

$sql = "SELECT NoTarjeta FROM Tarjeta WHERE Estado = 'LIBRE'";
$stmt = sqlsrv_query($conn, $sql);

$tarjetas = [];
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $tarjetas[] = $row['NoTarjeta'];
    }
}

echo json_encode($tarjetas);
?>
