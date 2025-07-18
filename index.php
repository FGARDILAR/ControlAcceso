<?php
session_start();
include 'includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    // Validación con SHA2_256
    $sql = "SELECT * FROM Usuarios WHERE Usuario = ? AND Contrasena = HASHBYTES('SHA2_256', ?)";
    $params = array($usuario, $contrasena);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt && sqlsrv_has_rows($stmt)) {
        $_SESSION['usuario'] = $usuario;
        header("Location: pages/preregistro.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Control de Acceso</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center vh-100 position-relative" style="background-image: url('assets/img/fondo.jpg'); background-size: cover; background-position: center;">
    <!-- Capa semi-transparente -->
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.5);"></div>
    
    <!-- Formulario de login -->
    <div class="card p-4 shadow-lg bg-white bg-opacity-75 position-relative" style="width: 400px; z-index: 1;">
        <h2 class="text-center mb-4">Iniciar Sesión</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <input type="text" name="usuario" placeholder="Usuario" required class="form-control">
            </div>
            <div class="mb-3">
                <input type="password" name="contrasena" placeholder="Contraseña" required class="form-control">
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Ingresar</button>
            <?php if (isset($error)) echo "<div class='alert alert-danger text-center'>$error</div>"; ?>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>


</html>
