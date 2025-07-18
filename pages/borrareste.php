<?php
include '../includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identificacion = $_POST['identificacion'];
    $sql = "SELECT ID, Nombre, Apellidos, Foto FROM Visitante WHERE Identificacion = ?";
    $params = array($identificacion);
    $stmt = sqlsrv_query($conn, $sql, $params);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($row) {
        echo '
            <img src="' . $row['Foto'] . '" alt="Foto Visitante" class="img-thumbnail mb-3" width="150">
            <p><strong>Nombre:</strong> ' . $row['Nombre'] . ' ' . $row['Apellidos'] . '</p>
        ';
    } else {
        echo '';
    }
}
