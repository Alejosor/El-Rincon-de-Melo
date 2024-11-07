<?php
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index_admin.php"); // Redirige al panel si ya se está logueado
    exit;
}
?>

<?php
session_start();
require 'includes/db_admin.php'; // Incluimos la conexión a la base de datos

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = $pdo->prepare("SELECT * FROM usuarios WHERE username = :username");
    $query->bindParam(':username', $username);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Si las credenciales son correctas, iniciar sesión
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $user['username'];
        header("Location: index_admin.php"); // Redirige al panel de administración
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!-- Manejo de errores -->
<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="login-container">
        <h1>Login Administrador</h1>
        <form action="login_admin.php" method="POST">
            <input type="text" name="username" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit" name="login">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
