<?php
session_start();

// Redirige al login si no hay sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Determina el módulo a cargar, por defecto "paciente"
$module = $_GET['module'] ?? 'paciente';
$moduleFile = "modulos/" . $module . ".php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Clínica</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<nav class="navbar">
    <ul>
        <li class="<?= $module=='paciente'?'active':'' ?>"><a href="?module=paciente">Pacientes</a></li>
        <li class="<?= $module=='medico'?'active':'' ?>"><a href="?module=medico">Doctores</a></li>
        <li class="<?= $module=='cita'?'active':'' ?>"><a href="?module=cita">Citas</a></li>
        <!--li class="<?= $module=='tratamiento'?'active':'' ?>"><a href="?module=tratamiento">Tratamientos</a></li-->
        <li class="<?= $module=='consultorio'?'active':'' ?>"><a href="?module=consultorio">Consultorios</a></li>
        <li class="<?= $module=='historial_clinico'?'active':'' ?>"><a href="?module=historial_clinico">Historial Médico</a></li>
        <li class="logout"><a href="logout.php">Salir</a></li>
    </ul>
</nav>

<main>
    <?php
    if (file_exists($moduleFile)) {
        include $moduleFile;
    } else {
        echo "<p>Módulo no encontrado.</p>";
    }
    ?>
</main>

<footer class="footer">
    &copy; <?= date('Y') ?> Derechos reservados
</footer>
</body>
</html>
