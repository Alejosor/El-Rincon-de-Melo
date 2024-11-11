<?php
require 'includes/db_user.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        $query = $pdo->prepare("INSERT INTO clientes (nombre, email, password) VALUES (:nombre, :email, :password)");
        $query->bindParam(':nombre', $nombre);
        $query->bindParam(':email', $email);
        $query->bindParam(':password', $password_hashed);

        try {
            $query->execute();
            header("Location: login_user.php?registro=exitoso");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Código para error de clave duplicada
                $error = "El email ya está registrado.";
            } else {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cliente</title>
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/global.css">
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/user/registro_user.css">
</head>
<body>
    <div class="main-wrapper">
        <div class="register-container">
            <h1>Registro de Cliente</h1>
            <form action="registro_user.php" method="POST" id="registroForm">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" placeholder="Ingresa tu nombre" required>

                <label for="email">Email:</label>
                <input type="email" name="email" placeholder="Ingresa tu email" required>

                <label for="password">Contraseña:</label>
                <input type="password" name="password" placeholder="Crea una contraseña" required>

                <label for="confirm_password">Confirmar Contraseña:</label>
                <input type="password" name="confirm_password" placeholder="Confirma tu contraseña" required>

                <button type="submit">Registrarse</button>
            </form>

            <?php if (isset($error)): ?>
                <div class="alert-card">
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>

            <p class="login-link">¿Ya tienes una cuenta? <a href="login_user.php">Inicia sesión aquí</a></p>
        </div>
    </div>
</body>
</html>