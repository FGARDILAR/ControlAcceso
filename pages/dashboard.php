<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Control de Acceso</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- Incluir la barra de navegación -->
<?php include 'navbar.php'; ?>

<div class="container my-5">
    <h2 class="text-center mb-4">Bienvenido, <?php echo $_SESSION['usuario']; ?>!</h2>
    <div class="row">
        <div class="col-md-4 mb-3">
            <a href="preregistro.php" class="btn btn-outline-primary w-100 py-3">📋 Preregistro de Visitante</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="registro.php" class="btn btn-outline-success w-100 py-3">🛂 Registro de Ingreso/Salida</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="../logout.php" class="btn btn-outline-danger w-100 py-3">🚪 Cerrar Sesión</a>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
