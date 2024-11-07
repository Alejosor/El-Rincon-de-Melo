<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php"); // Redirige al login si no está logueado
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <header>
        <h1>Bienvenido, <?php echo $_SESSION['admin_username']; ?></h1>
        <a href="logout.php">Cerrar Sesión</a>
    </header>
    <main>
        <p>Panel de control. Aquí irán las estadísticas y opciones.</p>
    </main>
</body>
</html>
