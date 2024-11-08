<?php
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index_admin.php"); // Redirige al panel si ya se está logueado
    exit;
}

ini_set('display_errors', 0); // No mostrar errores en pantalla
ini_set('log_errors', 1); // Registrar errores en el archivo de logs del servidor
error_reporting(E_ALL); // Seguir reportando todos los errores en los logs

require 'includes/db_admin.php'; // Conexión a la base de datos

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = $pdo->prepare("SELECT * FROM usuarios WHERE username = :username");
    $query->bindParam(':username', $username);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['username'];
            header("Location: index_admin.php");
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador</title>
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/global.css"> <!-- Ruta absoluta -->
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/admin/login_admin.css">
</head>
<body>
    <div class="main-wrapper"> <!-- Nuevo contenedor principal -->
        <div class="login-container">
            <h1>Login Administrador</h1>
            <form action="login_admin.php" method="POST">
                <input type="text" name="username" placeholder="Usuario" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit" name="login">Iniciar Sesión</button>
            </form>
        </div>

        <!-- Alerta debajo del formulario -->
        <?php if (isset($error)): ?>
            <div class="alert-card">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>