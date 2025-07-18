<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

$fotoRuta = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identificacion = $_POST['identificacion'];

    // Guardar la foto
    if (isset($_POST['foto_base64']) && !empty($_POST['foto_base64'])) {
        $foto_base64 = $_POST['foto_base64'];
        $foto = base64_decode(explode(',', $foto_base64)[1]);

        // Crear carpeta por usuario si no existe
        $baseDir = '../visitantes/' . $identificacion;
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        // Guardar la foto con fecha y hora
        $fotoNombre = date('Ymd_His') . '_foto.png';
        $fotoRuta = $baseDir . '/' . $fotoNombre;
        file_put_contents($fotoRuta, $foto);
    }

    // Verificar si el visitante ya existe
    $sqlCheck = "SELECT COUNT(*) AS count FROM Visitante WHERE Identificacion = ?";
    $paramsCheck = array($identificacion);
    $stmtCheck = sqlsrv_query($conn, $sqlCheck, $paramsCheck);
    $rowCheck = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);
    $existe = "";
    if ($rowCheck['count'] > 0) {
        $sqlInsert = "{CALL spRegistrarVisitante(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)}";
        $paramsInsert = array($_POST['nombre'], $_POST['apellidos'], $identificacion, $_POST['tipoIdentificacion'], $_POST['telefono'], $_POST['empresa'], $_POST['cargo'], $fotoRuta, $_SESSION['usuario'], $_POST['IngresaVehiculo'], $_POST['Placa'], $_POST['Autoriza'], $_POST['Tarjeta'], $_POST['identificacion']);
        $stmtInsert = sqlsrv_query($conn, $sqlInsert, $paramsInsert);
        $success = $stmtInsert ? "¡Preregistro exitoso!" : "Error al registrar al visitante.";
        //$sqlUpdate = "UPDATE Visitante SET Foto = ? WHERE Identificacion = ?";
        //$paramsUpdate = array($fotoRuta, $identificacion);
        //$stmtUpdate = sqlsrv_query($conn, $sqlUpdate, $paramsUpdate);
        //$success = $stmtUpdate ? "¡Foto actualizada exitosamente!" : "Error al actualizar la foto.";
    } else {

        $sqlInsert = "{CALL spRegistrarVisitante(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)}";
        $paramsInsert = array($_POST['nombre'], $_POST['apellidos'], $identificacion, $_POST['tipoIdentificacion'], $_POST['telefono'], $_POST['empresa'], $_POST['cargo'], $fotoRuta, $_SESSION['usuario'], $_POST['IngresaVehiculo'], $_POST['Placa'], $_POST['Autoriza'], $_POST['Tarjeta'], 1);
        $stmtInsert = sqlsrv_query($conn, $sqlInsert, $paramsInsert);
        $success = $stmtInsert ? "¡Preregistro exitoso!" : "Error al registrar al visitante.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preregistro de Visitante</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 1100px; margin-top: 30px; }
        .border-custom { border: 2px solid #007bff; border-radius: 5px; }
        .bg-light-custom { background-color: #f8f9fa; padding: 15px; border-radius: 5px; }
        canvas { display: none; }
        #fotoMiniatura { max-width: 150px; margin-top: 5px; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container bg-light-custom" >
    <h2 class="text-center mb-4">Preregistro de Visitante</h2>
    <div id="alerta"></div>

    <form method="POST">
        <div class="row">
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipo de Identificación:</label>
                        <select name="tipoIdentificacion" id="tipoIdentificacion" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <option value="Cedula">Cédula</option>
                            <option value="DNI">DNI</option>
                            <option value="Tarjeta Identidad">Tarjeta de Identidad</option>
                            <option value="Pasaporte">Pasaporte</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Identificación:</label>
                        <input type="text" name="identificacion" id="identificacion" class="form-control" required pattern="[0-9]*" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required 
                            oninput="
                                // Convertir a mayúsculas
                                this.value = this.value.toUpperCase();
                                // Eliminar números y cualquier carácter que no sea letra o espacio
                                this.value = this.value.replace(/[^A-Z\s]/g, '');
                            ">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Apellidos:</label>
                        <input type="text" name="apellidos" id="apellidos" class="form-control" required 
                            oninput="
                                // Convertir a mayúsculas
                                this.value = this.value.toUpperCase();
                                // Eliminar números y cualquier carácter que no sea letra o espacio
                                this.value = this.value.replace(/[^A-Z\s]/g, '');
                            ">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Teléfono:</label>
                        <input type="text" name="telefono" id="telefono" class="form-control" required pattern="[0-9]*" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Empresa:</label>
                        <input type="text" name="empresa" id="empresa" class="form-control" required oninput="this.value = this.value.toUpperCase()">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Cargo:</label>
                        <input type="text" name="cargo" id="cargo" class="form-control" required oninput="this.value = this.value.toUpperCase()">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Ingresa Vehiculo:</label>
                        <select name="IngresaVehiculo" id="IngresaVehiculo" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <option value="SI">SI</option>
                            <option value="NO">NO</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Placa:</label>
                        <input type="text" name="Placa" id="Placa" class="form-control" oninput="this.value = this.value.toUpperCase()">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tarjeta No:</label>
                        <select name="Tarjeta" id="Tarjeta" class="form-select" required>
                            <option value="">Seleccione una tarjeta...</option>
                        </select>
                    </div>


                    <div class="col-md-4 mb-3">
                        <label class="form-label">Autoriza:</label>
                        <select name="Autoriza" id="Autoriza" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <option value="Gerencia">Gerencia</option>
                            <option value="Talento Humano">Talento Humano</option>
                            <option value="Comercial">Comercial</option>
                            <option value="Jhon Freddy Murillo">DP. Jhon Freddy Murillo</option>
                            <option value="Anderson Rodriguez">DP. Anderson Rodriguez</option>
                            <option value="Hector Diaz">DP. Hector Diaz</option>
                        </select>
                    </div>

                    <!-- Contenedor para la miniatura de la foto capturada -->
                    <div class="col-md-4 mb-3 text-center">
                   
                        <img id="fotoMiniatura" src="" alt="Foto Capturada" class="img-thumbnail" style="display:none;">

                    </div>

                    
                </div>
            </div>

            <div class="col-md-3 text-center">
                <h5>Capturar Foto  📷​</h5>
                <video id="video" width="200" height="150" autoplay class="border-custom"></video>
                <br>
                <button type="button" onclick="capturarFoto()" class="btn btn-primary mt-2">Capturar</button>
                <input type="hidden" name="foto_base64" id="foto_base64">
            </div>
        </div>

        <button type="submit" class="btn btn-success w-100 mt-3">Guardar Preregistro</button>
    </form>

</div>

<div id="modalTarjetas" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <h2>Tarjetas en uso</h2>
        <table>
            <thead>
                <tr>
                    <th>No. Tarjeta</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="tablaTarjetas">
                <!-- Aquí se llenarán las tarjetas dinámicamente -->
            </tbody>
        </table>
    </div>
</div>

<script>
function cargarTarjetas() {
    fetch('obtener_tarjetas.php') // Llama a un archivo PHP que obtendrá las tarjetas
        .then(response => response.json())
        .then(data => {
            let tbody = document.getElementById("tablaTarjetas");
            tbody.innerHTML = ""; // Limpiar contenido anterior

            data.forEach(tarjeta => {
                let fila = `
                    <tr>
                        <td>${tarjeta.NoTarjeta}</td>
                        <td>${tarjeta.estado}</td>
                        <td>
                            <button onclick="liberarTarjeta(${tarjeta.NoTarjeta})">Liberar</button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += fila;
            });

            document.getElementById("modalTarjetas").style.display = "block";
        });
}

function liberarTarjeta(noTarjeta) {
    fetch('liberar_tarjeta.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'NoTARJETA=' + noTarjeta
    })
    .then(response => response.text())
    .then(data => {
        alert(data); // Muestra mensaje de éxito o error
        cargarTarjetas(); // Recargar la tabla después de la actualización
    });
}

function cerrarModal() {
    document.getElementById("modalTarjetas").style.display = "none";
}
</script>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $("#identificacion").on("blur", function () {
            const identificacion = $(this).val();
            if (identificacion) {
                $.post("buscar_visitante.php", { identificacion: identificacion }, function (data) {
                    const visitante = JSON.parse(data);
                    if (visitante) {
                        $("#nombre").val(visitante.Nombre);
                        $("#apellidos").val(visitante.Apellidos);
                        $("#tipoIdentificacion").val(visitante.TipoIdentificacion);
                        $("#telefono").val(visitante.Telefono);
                        $("#empresa").val(visitante.Empresa);
                        $("#cargo").val(visitante.Cargo);
                    } else {
                        $("#nombre, #apellidos, #tipoIdentificacion, #telefono, #empresa, #cargo").val('');
                    }
                });
            }
        });
    });

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => document.getElementById("video").srcObject = stream)
        .catch(error => alert("Error al acceder a la cámara: " + error.message));

    function capturarFoto() {
        const video = document.getElementById("video");
        const canvas = document.createElement("canvas");
        const fotoMiniatura = document.getElementById("fotoMiniatura");

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext("2d").drawImage(video, 0, 0);

        const fotoBase64 = canvas.toDataURL("image/png");
        document.getElementById("foto_base64").value = fotoBase64;

        fotoMiniatura.src = fotoBase64;
        fotoMiniatura.style.display = "block";
    }
</script>

<script>
$(document).ready(function () {
    function cargarTarjetasLibres() {
        $.get("../php/obtener_tarjetas_libres.php", function (data) {
            let tarjetas = JSON.parse(data);
            let selectTarjeta = $("#Tarjeta");

            selectTarjeta.empty();
            selectTarjeta.append('<option value="">Seleccione una tarjeta...</option>');

            tarjetas.forEach(function (tarjeta) {
                selectTarjeta.append('<option value="' + tarjeta + '">' + tarjeta + '</option>');
            });
        });
    }

    cargarTarjetasLibres();
});
</script>





</body>
<style>
    /* Estilos del modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: white;
        margin: 10% auto;
        padding: 20px;
        border-radius: 5px;
        width: 50%;
        text-align: center;
    }

    .close {
        color: red;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: center;
    }

    button {
        padding: 5px 10px;
        background-color: green;
        color: white;
        border: none;
        cursor: pointer;
    }

    button:hover {
        background-color: darkgreen;
    }


</style>
</html>
