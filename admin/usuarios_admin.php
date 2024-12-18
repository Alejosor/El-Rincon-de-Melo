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

// Obtener tipo de usuario seleccionado en el filtro
$filtro_tipo = $_GET['tipo'] ?? 'Todos';

// Construir la consulta con filtro
if ($filtro_tipo === 'Todos') {
    $query = $pdo->query("SELECT id, nombre, email, 'Cliente' AS tipo FROM clientes
                          UNION
                          SELECT id, nombre, email, 'Administrador' AS tipo FROM administradores
                          ORDER BY tipo, nombre");
} else {
    $tabla = $filtro_tipo === 'Administrador' ? 'administradores' : 'clientes';
    $query = $pdo->prepare("SELECT id, nombre, email, :tipo AS tipo FROM $tabla ORDER BY nombre");
    $query->bindParam(':tipo', $filtro_tipo);
    $query->execute();
}

$usuarios = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/admin/usuarios_admin.css">
</head>
<body>
    <header>
        <h1>Gestión de Usuarios</h1>
        <a href="index_admin.php" class="return-button">Volver al Panel</a>
    </header>
    <main>
    <div class="filter-wrapper">
        <a href="nuevo_usuario.php" class="btn create-btn">Crear Nuevo Usuario</a>
        <form method="GET" action="usuarios_admin.php" class="filter-form">
            <label for="tipo">Filtrar por tipo de usuario:</label>
            <select name="tipo" id="tipo" onchange="this.form.submit()">
                <option value="Todos" <?= $filtro_tipo === 'Todos' ? 'selected' : '' ?>>Todos</option>
                <option value="Cliente" <?= $filtro_tipo === 'Cliente' ? 'selected' : '' ?>>Cliente</option>
                <option value="Administrador" <?= $filtro_tipo === 'Administrador' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['tipo']); ?></td>
                    <td>
                        <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>&tipo=<?php echo $usuario['tipo']; ?>" class="btn action-btn">Editar</a>
                        <a href="eliminar_usuario.php?id=<?php echo $usuario['id']; ?>&tipo=<?php echo $usuario['tipo']; ?>" class="btn action-btn" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </main>
</body>
</html>