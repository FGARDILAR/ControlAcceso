<?php
$serverName = "10.75.0.214, 1433";
$connectionInfo = array("Database"=>"ControlAccesoDC", "UID"=>"sa", "PWD"=>"CASAhh03");
$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn) {
    echo "¡Conexión exitosa!";
} else {
    echo "Error en la conexión.";
    die(print_r(sqlsrv_errors(), true));
}
