<?php
include '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['identificacion'])) {
    $identificacion = $_POST['identificacion'];

    // Consulta para obtener la información básica del visitante
    $sql = "SELECT ID, Nombre, Apellidos, Identificacion, TipoIdentificacion, Telefono, Empresa, Cargo, Foto FROM Visitante WHERE Identificacion = ?";
    $params = array($identificacion);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt && sqlsrv_has_rows($stmt)) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        // Obtener el ID del visitante
        $visitanteID = $row['ID'];

        // Consulta para verificar el último registro (ingreso/salida)
        $sqlRegistro = "SELECT TOP 1 FechaHoraSalida FROM Registro WHERE VisitanteID = ? ORDER BY FechaHoraIngreso DESC";
        $paramsRegistro = array($visitanteID);
        $stmtRegistro = sqlsrv_query($conn, $sqlRegistro, $paramsRegistro);

        if ($stmtRegistro && sqlsrv_has_rows($stmtRegistro)) {
            $registro = sqlsrv_fetch_array($stmtRegistro, SQLSRV_FETCH_ASSOC);

            // Verificar si hay salida registrada
            if ($registro['FechaHoraSalida'] === null) {
                $row['UltimoRegistro'] = 'ingreso';  // Registro pendiente de salida
            } else {
                $row['UltimoRegistro'] = 'salida';   // Registro disponible para ingreso
            }
        } else {
            $row['UltimoRegistro'] = 'ninguno';  // No tiene registros previos
        }

        // Quitar el ID antes de enviar el JSON
        unset($row['ID']);
        echo json_encode($row);  // Enviar datos en formato JSON
    } else {
        echo json_encode(null);  // No se encontró el visitante
    }
}
