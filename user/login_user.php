<?php
session_start();
require 'includes/db_user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $query = $pdo->prepare("SELECT * FROM clientes WHERE email = :email");
    $query->bindParam(':email', $email);
    $query->execute();
    $cliente = $query->fetch(PDO::FETCH_ASSOC);

    if ($cliente && password_verify($password, $cliente['password'])) {
        $_SESSION['cliente_id'] = $cliente['id'];
        $_SESSION['cliente_nombre'] = $cliente['nombre'];
        header("Location: index_user.php");
        exit;
    } else {
        $error = "Credenciales incorrectas.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login de Cliente</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Login de Cliente</h1>
    </header>
    <main>
        <form action="login_user.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required>

            <button type="submit">Iniciar Sesión</button>
        </form>
        <?php if (isset($error)): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </main>
</body>
</html>
