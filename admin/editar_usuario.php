<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit;
}

require 'includes/db_admin.php';

if (!isset($_GET['id']) || !isset($_GET['tipo'])) {
    header("Location: usuarios_admin.php");
    exit;
}

$id = $_GET['id'];
$tipo = $_GET['tipo'];
$error = null;

if ($tipo === 'Administrador') {
    $query = $pdo->prepare("SELECT * FROM administradores WHERE id = :id");
} else {
    $query = $pdo->prepare("SELECT * FROM clientes WHERE id = :id");
}

$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->execute();
$usuario = $query->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: usuarios_admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);

    if ($tipo === 'Administrador') {
        $query = $pdo->prepare("UPDATE administradores SET nombre = :nombre, email = :email WHERE id = :id");
    } else {
        $query = $pdo->prepare("UPDATE clientes SET nombre = :nombre, email = :email WHERE id = :id");
    }

    $query->bindParam(':nombre', $nombre);
    $query->bindParam(':email', $email);
    $query->bindParam(':id', $id, PDO::PARAM_INT);

    try {
        $query->execute();
        header("Location: usuarios_admin.php");
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Código para error de clave duplicada
            $error = "El email ya está registrado.";
        } else {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/global.css"> <!-- Ruta absoluta -->
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/admin/editar_usuario.css">
</head>
<body>
    <header class="nav-header">
        <h1>Editar Usuario - <?php echo htmlspecialchars($usuario['nombre']); ?></h1>
        <a href="usuarios_admin.php" class="return-button">Volver</a>
    </header>
    <main>
        <form action="editar_usuario.php?id=<?php echo $id; ?>&tipo=<?php echo $tipo; ?>" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>

            <button type="submit">Guardar Cambios</button>
        </form>

        <!-- Alerta de error -->
        <?php if ($error): ?>
            <div class="alert-card">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>