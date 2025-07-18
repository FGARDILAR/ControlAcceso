<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

// Función para guardar la firma como imagen
function guardarFirma($firma_base64, $identificacion, $tipo) {
    $firma = base64_decode(explode(',', $firma_base64)[1]);
    $baseDir = '../visitantes/' . $identificacion;
    if (!is_dir($baseDir)) {
        mkdir($baseDir, 0777, true);
    }
    $firmaNombre = date('Ymd_His') . "_firma_$tipo.png";
    $firmaRuta = $baseDir . '/' . $firmaNombre;
    file_put_contents($firmaRuta, $firma);
    return $firmaRuta;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identificacion = $_POST['identificacion'];
    $tipoRegistro = $_POST['tipoRegistro'];
    $firma_base64 = $_POST['firma_base64'];
    $fechaHora = date('Y-m-d H:i:s');

    // Obtener el ID del visitante
    $sql = "SELECT ID FROM Visitante WHERE Identificacion = ?";
    $stmt = sqlsrv_query($conn, $sql, array($identificacion));
    $visitante = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $visitanteID = $visitante['ID'];

    if ($visitanteID) {
        $firmaRuta = guardarFirma($firma_base64, $identificacion, $tipoRegistro);
        $sqlSP = "{CALL spRegistrarAcceso(?, ?, ?, ?)}";
        $paramsSP = array($visitanteID, $firmaRuta, $fechaHora, $_SESSION['usuario']);
        $stmtSP = sqlsrv_query($conn, $sqlSP, $paramsSP);

       
        if ($stmtSP) {
            $_SESSION['mensaje'] = '<div class="alert alert-success">¡Registro guardado exitosamente!</div>';
        } else {
            $_SESSION['mensaje'] = '<div class="alert alert-danger">Error al registrar acceso.</div>';
        }
        } else {
            $_SESSION['mensaje'] = '<div class="alert alert-danger">Visitante no encontrado.</div>';
        }

        header("Location: registro.php"); // Redirige para mostrar el mensaje
        exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Ingreso/Salida</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        
    .container { 
        max-width: 900px; 
        margin-top: 30px; 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        flex-direction: column; /* Alinea elementos en columna si hay más de uno */ 
    }
    .signature-pad { 
        border: 2px solid #007bff; 
        border-radius: 5px; 
        height: 150px; 
        cursor: crosshair; 
    }
    #firma_base64 { 
        display: none; 
    }
    .img-thumbnail { 
        width: 150px; 
        height: auto; 
    }
    @media (max-width: 768px) {
        .signature-pad { 
            height: 100px; 
        }
    }

    .fixed-bottom {
        width: 100%;
        position: fixed;
        bottom: 0;
        left: 0;
        text-align: center;
    }

    .alert {
        display: inline-block;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 16px;
        width: auto;
        max-width: 80%;
    }


</style>
</head>

<?php include 'navbar.php'; ?>

<div class="container fixed-bottom text-center pb-3">
    <?php
    if (isset($_SESSION['mensaje'])) {
        echo $_SESSION['mensaje'];
        unset($_SESSION['mensaje']); // Limpiar la variable después de mostrarla
    }
    ?>
</div>

<body class="bg-light">

<div class="container bg-white p-4 rounded">
    <h2 class="text-center mb-4">Registro de Ingreso/Salida</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Identificación:</label>
            <input type="text" name="identificacion" id="identificacion" class="form-control" required>
        </div>
        <div id="info-visitante" class="mb-3 text-center"></div>
        <div id="firma" class="mb-3 text-center"><label>Firma:</label></div>
        
        <canvas id="signature-pad" class="signature-pad mb-3"></canvas>
        <input type="hidden" name="firma_base64" id="firma_base64">

        <div class="text-center mb-3">
            <button type="button" id="limpiarFirma" class="btn btn-secondary">Limpiar Firma</button>
            <button type="button" id="guardarFirma" class="btn btn-primary">Guardar Firma</button>
        </div>
        
        <input type="hidden" name="tipoRegistro" id="tipoRegistro">
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const canvas = document.getElementById('signature-pad');
    const ctx = canvas.getContext('2d');
    let isDrawing = false;

    canvas.addEventListener('mousedown', () => isDrawing = true);
    canvas.addEventListener('mouseup', () => {
        isDrawing = false;
        ctx.beginPath();
    });
    canvas.addEventListener('mousemove', draw);

    function draw(event) {
        if (!isDrawing) return;
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000';
        ctx.lineTo(event.offsetX, event.offsetY);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(event.offsetX, event.offsetY);
    }

    $('#limpiarFirma').click(function () {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    });

    $('#guardarFirma').click(function () {
        const dataURL = canvas.toDataURL();
        $('#firma_base64').val(dataURL);
        $('#tipoRegistro').val('ingreso');
        $('form').submit();
    });

    $('#identificacion').on('blur', function () {
    const identificacion = $(this).val();
    if (identificacion) {
        $.post('buscar_visitante.php', { identificacion }, function (data) {
            const visitante = JSON.parse(data);
            if (visitante) {
                $('#info-visitante').html(`
                    <div class="mb-3">
                        <img src="${visitante.Foto}" alt="Foto Visitante" class="img-thumbnail mb-2">
                        <p><strong>Nombre:</strong> ${visitante.Nombre} ${visitante.Apellidos}</p>
                        <p><strong>Identificación:</strong> ${visitante.Identificacion} (${visitante.TipoIdentificacion})</p>
                        <p><strong>Teléfono:</strong> ${visitante.Telefono}</p>
                        <p><strong>Empresa:</strong> ${visitante.Empresa}</p>
                        <p><strong>Cargo:</strong> ${visitante.Cargo}</p>
                    </div>
                `);

                // Llamada AJAX para consultar el tipo de registro (ingreso o salida) basado en el SP
                $.post('verificar_registro.php', { identificacion }, function (tipoRegistro) {
                    let mensajeTipoRegistro = '';  // Variable para mostrar el tipo de registro

                    if (tipoRegistro === 'ingreso') {
                        $('#tipoRegistro').val('ingreso');
                        mensajeTipoRegistro = '<div class="alert alert-info">Crear nuevo registro: <strong>Ingreso</strong></div>';
                    } else if (tipoRegistro === 'Salida') {
                        $('#tipoRegistro').val('Salida');
                        mensajeTipoRegistro = '<div class="alert alert-warning">Actualizar registro existente: <strong>Salida</strong></div>';
                    } else {
                        mensajeTipoRegistro = '<div class="alert alert-danger">Error al consultar el tipo de registro.</div>';
                    }

                    // Mostrar el mensaje del tipo de registro
                    $('#info-visitante').append(mensajeTipoRegistro);
                });
                
            } else {
                $('#info-visitante').html('<div class="alert alert-danger">Visitante no encontrado.</div>');
                $('#tipoRegistro').val('');  // Limpiar tipo de registro si no se encuentra visitante
            }
            });
        }
    });


</script>
</body>
</html>
