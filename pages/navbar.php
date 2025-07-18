<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}
?>

<!-- Vincula el archivo CSS personalizado -->
<link rel="stylesheet" href="../css/navbar.css">

<!-- Barra de navegación -->
<nav class="navbar">
    <div class="container2">
        <!-- Menú a la izquierda -->
        <ul class="nav-links">
            <li><a href="preregistro.php">📋 Preregistro</a></li>
            <li><a href="registro.php">​🚪​ Registro</a></li>
            <li><a href="#" onclick="cargarTarjetas()">💳 Tarjeta</a></li>
        </ul>

        <!-- Menú de usuario a la derecha -->
        <div class="user-menu">
            <button class="user-btn" onclick="toggleMenu()">👤 <?php echo $_SESSION['usuario']; ?> ▼</button>
            <ul class="user-dropdown" id="userDropdown">
                <li><a href="#" onclick="abrirModal()">🔑 Cambiar Clave</a></li>
                <li><a href="../logout.php" class="text-danger">🔒​ Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Ventana Modal para cambiar clave -->
<div id="modalClave" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <h2>Cambiar Clave</h2>
        <form id="formCambiarClave">
            <label for="claveActual">Clave Actual:</label>
            <input type="password" id="claveActual" name="claveActual" required>

            <label for="nuevaClave">Nueva Clave:</label>
            <input type="password" id="nuevaClave" name="nuevaClave" required>

            <label for="confirmarClave">Confirmar Nueva Clave:</label>
            <input type="password" id="confirmarClave" name="confirmarClave" required>

            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</div>

<!-- Scripts -->
<script>
    function toggleMenu() {
        var dropdown = document.getElementById("userDropdown");
        dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
    }

    function abrirModal() {
        document.getElementById("modalClave").style.display = "block";
    }

    function cerrarModal() {
        document.getElementById("modalClave").style.display = "none";
    }

    window.onclick = function(event) {
        var modal = document.getElementById("modalClave");
        if (event.target === modal) {
            cerrarModal();
        }
    }
</script>
