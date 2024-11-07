<?php
require 'includes/db_user.php';

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
            header("Location: login_user.php");
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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Registro de Cliente</h1>
    </header>
    <main>
        <form action="registro_user.php" method="POST" id="registroForm">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required>

            <label for="confirm_password">Confirmar Contraseña:</label>
            <input type="password" name="confirm_password" required>

            <button type="submit">Registrarse</button>
        </form>

        <?php if (isset($error)): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </main>

    <script>
        document.querySelector('#registroForm').addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden.');
            }
        });
    </script>
</body>
</html>
