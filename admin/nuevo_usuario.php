<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit;
}

require 'includes/db_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $tipo = $_POST['tipo'];

    if (empty($nombre) || empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Por favor, ingrese un email válido.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if ($tipo === 'Administrador') {
            $query = $pdo->prepare("INSERT INTO administradores (nombre, email, password) VALUES (:nombre, :email, :password)");
        } else {
            $query = $pdo->prepare("INSERT INTO clientes (nombre, email, password) VALUES (:nombre, :email, :password)");
        }

        $query->bindParam(':nombre', $nombre);
        $query->bindParam(':email', $email);
        $query->bindParam(':password', $hashedPassword);

        try {
            $query->execute();
            header("Location: usuarios_admin.php");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicado de entrada
                $error = "El correo electrónico ya está registrado. Por favor, usa uno diferente.";
            } elseif (strpos($e->getMessage(), 'SQLSTATE[HY000]') !== false) { // Error de conexión
                $error = "Error de conexión a la base de datos. Por favor, verifica tu conexión.";
            } else { // Otros errores
                $error = "Error inesperado al crear el usuario. Por favor, inténtalo de nuevo más tarde.";
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
    <title>Nuevo Usuario</title>
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/global.css"> <!-- Ruta absoluta -->
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/admin/nuevo_usuario.css">
</head>
<body>
    <header class="nav-header">
        <h1>El Rincón de Melo</h1>
        <a href="index_admin.php" class="cancel-nav-button">Cancelar</a>
    </header>
    <main>
        <h2>Crear Nuevo Usuario</h2>
        <form action="nuevo_usuario.php" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required>

            <label for="tipo">Tipo de Usuario:</label>
            <select name="tipo" required>
                <option value="Cliente">Cliente</option>
                <option value="Administrador">Administrador</option>
            </select>

            <button type="submit">Crear Usuario</button>
        </form>
    </main>

    <!-- Mensaje de error con estilo, fuera del contenedor -->
    <?php if (isset($error)): ?>
        <div class="alert-card">
            <p><?php echo htmlspecialchars($error); ?></p>
        </div>
    <?php endif; ?>
</body>
</html>