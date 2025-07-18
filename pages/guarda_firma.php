<?php
include '../includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identificacion = $_POST['identificacion'];
    $firma_base64 = $_POST['firma_base64'];
    $firma = base64_decode(explode(',', $firma_base64)[1]);

    // Obtener ID del visitante
    $sqlId = "SELECT ID FROM Visitante WHERE Identificacion = ?";
    $paramsId = array($identificacion);
    $stmtId = sqlsrv_query($conn, $sqlId, $paramsId);
    $rowId = sqlsrv_fetch_array($stmtId, SQLSRV_FETCH_ASSOC);
    $visitanteID = $rowId['ID'];

    $baseDir = "../visitantes/" . $identificacion;
    if (!is_dir($baseDir)) {
        mkdir($baseDir, 0777, true);
    }

    $firmaNombre = date('Ymd_His') . '_firma.png';
    $firmaRuta = $baseDir . '/' . $firmaNombre;
    file_put_contents($firmaRuta, $firma);

    $sqlInsert = "{CALL spRegistrarAcceso(?, ?, ?)}";
    $paramsInsert = array($visitanteID, $firmaRuta, date('Y-m-d H:i:s'));
    $stmtInsert = sqlsrv_query($conn, $sqlInsert, $paramsInsert);

    echo $stmtInsert ? "¡Registro exitoso!" : "Error al guardar el registro.";
}
