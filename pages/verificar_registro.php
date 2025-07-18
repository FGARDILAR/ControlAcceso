<?php
include '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['identificacion'])) {
    $identificacion = $_POST['identificacion'];

    // Consultar el ID del visitante
    $sql = "SELECT ID FROM Visitante WHERE Identificacion = ?";
    $stmt = sqlsrv_query($conn, $sql, array($identificacion));
    $visitante = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $visitanteID = $visitante['ID'];

    if ($visitanteID) {
        // Ejecutar el SP para verificar el tipo de registro (ingreso o salida)
        $sqlSP = "{CALL spVerificarTipoRegistro(?)}";
        $paramsSP = array($visitanteID);
        $stmtSP = sqlsrv_query($conn, $sqlSP, $paramsSP);

        if ($stmtSP) {
            $row = sqlsrv_fetch_array($stmtSP, SQLSRV_FETCH_ASSOC);
            $tipoRegistro = $row['TipoRegistro'] ?? '';  // El SP debe retornar 'ingreso' o 'salida'

            echo $tipoRegistro;  // Retornar el tipo de registro
        } else {
            echo 'error';
        }
    } else {
        echo 'no_encontrado';
    }
}
