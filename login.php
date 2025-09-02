<?php
session_start();
require 'db_init.php'; // Conexión a SQLite y creación de usuario master

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Consultar usuario en la base de datos
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar contraseña
    if ($user && password_verify($password, $user['contrasena'])) {
        $_SESSION['usuario'] = $user['usuario'];
        header("Location: index.php"); // Redirige al inicio con CRUD
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Clínica</title>
    <link rel="stylesheet" href="css/estilos.css"> <!-- Estilos globales -->
</head>
<body>
    <div class="loginPHP-container">
        <div class="form-section">
            <h2>Iniciar Sesión</h2>
            <?php if($error): ?>
                <div class="message-error"><?= $error ?></div>
            <?php endif; ?>
            <form method="post">
                <input type="text" name="usuario" placeholder="Usuario" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit">Ingresar</button>
            </form>
        </div>
    </div>
</body>
</html>
