<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit;
}

require 'includes/db_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $tipo = $_POST['tipo'];

    if ($tipo === 'Administrador') {
        $query = $pdo->prepare("INSERT INTO administradores (nombre, email, password) VALUES (:nombre, :email, :password)");
    } else {
        $query = $pdo->prepare("INSERT INTO clientes (nombre, email, password) VALUES (:nombre, :email, :password)");
    }

    $query->bindParam(':nombre', $nombre);
    $query->bindParam(':email', $email);
    $query->bindParam(':password', $password);

    try {
        $query->execute();
        header("Location: usuarios_admin.php");
        exit;
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Usuario</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <header>
        <h1>Crear Nuevo Usuario</h1>
        <a href="usuarios_admin.php">Volver a Gestión de Usuarios</a>
    </header>
    <main>
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
        <?php if (isset($error)): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </main>
</body>
</html>
