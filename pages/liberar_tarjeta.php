<?php
include __DIR__ . '/../includes/conexion.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $NoTARJETA = $_POST['NoTARJETA'];

    $sql = "UPDATE tarjeta SET estado = 'LIBRE' WHERE NoTARJETA = ?";
    $stmt = sqlsrv_query($conn, $sql, array($NoTARJETA));

    if ($stmt) {
        echo "Tarjeta liberada exitosamente.";
    } else {
        echo "Error al liberar la tarjeta.";
    }
}
?>
