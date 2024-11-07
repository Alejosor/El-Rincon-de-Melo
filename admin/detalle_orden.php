<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit;
}

require 'includes/db_admin.php';

if (isset($_GET['id'])) {
    $id_orden = $_GET['id'];
    $query = $pdo->prepare("SELECT * FROM detalle_ordenes WHERE id_orden = :id_orden");
    $query->bindParam(':id_orden', $id_orden);
    $query->execute();
    $detalles = $query->fetchAll(PDO::FETCH_ASSOC);
} else {
    header("Location: ordenes.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Orden</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <header>
        <h1>Detalle de Orden #<?php echo $id_orden; ?></h1>
        <a href="ordenes.php">Volver a Órdenes</a>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalles as $detalle): 
                    $query_producto = $pdo->prepare("SELECT nombre FROM productos WHERE id = :id_producto");
                    $query_producto->bindParam(':id_producto', $detalle['id_producto']);
                    $query_producto->execute();
                    $producto = $query_producto->fetch(PDO::FETCH_ASSOC);
                ?>
                    <tr>
                        <td><?php echo $producto['nombre']; ?></td>
                        <td><?php echo $detalle['cantidad']; ?></td>
                        <td><?php echo $detalle['subtotal']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>