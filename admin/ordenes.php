<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit;
}

require 'includes/db_admin.php';

// Obtener las órdenes de la base de datos
$query = $pdo->query("SELECT * FROM ordenes ORDER BY fecha DESC");
$ordenes = $query->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirmar_orden'])) {
        $id_orden = $_POST['id_orden'];
        $query = $pdo->prepare("UPDATE ordenes SET estado = 'Confirmada' WHERE id = :id");
        $query->bindParam(':id', $id_orden);
        $query->execute();
    }

    if (isset($_POST['eliminar_orden'])) {
        $id_orden = $_POST['id_orden'];

        // Eliminar primero los detalles de la orden
        $query = $pdo->prepare("DELETE FROM detalle_ordenes WHERE id_orden = :id");
        $query->bindParam(':id', $id_orden);
        $query->execute();

        // Luego, eliminar la orden principal
        $query = $pdo->prepare("DELETE FROM ordenes WHERE id = :id");
        $query->bindParam(':id', $id_orden);
        $query->execute();
    }

    header("Location: ordenes.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Órdenes</title>
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/admin/ordenes.css">
</head>
<body>
    <header>
        <h1>Gestión de Órdenes</h1>
        <div class="header-buttons">
            <a href="reporte_pedidos.php" class="btn">Generar Reporte de Pedidos</a>
            <a href="index_admin.php" class="btn">Volver al Panel</a>
        </div>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ordenes)): ?>
                    <tr>
                        <td colspan="5">No hay órdenes registradas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ordenes as $orden): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($orden['id']); ?></td>
                            <td><?php echo htmlspecialchars($orden['fecha']); ?></td>
                            <td>S/<?php echo htmlspecialchars($orden['total']); ?></td>
                            <td><?php echo isset($orden['estado']) ? htmlspecialchars($orden['estado']) : 'Pendiente'; ?></td>
                            <td>
                                <?php if ($orden['estado'] !== 'Confirmada'): ?>
                                    <form action="ordenes.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id_orden" value="<?php echo htmlspecialchars($orden['id']); ?>">
                                        <button type="submit" name="confirmar_orden">Confirmar</button>
                                    </form>
                                <?php endif; ?>
                                <form action="ordenes.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id_orden" value="<?php echo htmlspecialchars($orden['id']); ?>">
                                    <button type="submit" name="eliminar_orden" onclick="return confirm('¿Estás seguro de eliminar esta orden?');">Eliminar</button>
                                </form>
                                <a href="detalle_orden.php?id=<?php echo htmlspecialchars($orden['id']); ?>">Ver Detalle</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>