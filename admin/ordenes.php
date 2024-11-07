<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit;
}

require 'includes/db_admin.php'; // Conexión a la base de datos

$query = $pdo->query("SELECT * FROM ordenes ORDER BY fecha DESC");
$ordenes = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Órdenes</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <header>
        <h1>Gestión de Órdenes</h1>
        <a href="index_admin.php">Volver al Panel</a>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ordenes as $orden): ?>
                    <tr>
                        <td><?php echo $orden['id']; ?></td>
                        <td><?php echo $orden['fecha']; ?></td>
                        <td><?php echo $orden['total']; ?></td>
                        <td>
                            <a href="detalle_orden.php?id=<?php echo $orden['id']; ?>">Ver Detalle</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
