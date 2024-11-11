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

if (isset($_GET['id'])) {
    $id_orden = $_GET['id'];

    // Obtener los detalles de la orden
    $query = $pdo->prepare("
        SELECT do.id_producto, p.nombre AS producto, do.cantidad, do.subtotal, 
               o.fecha, o.total, 
               COALESCE(c.nombre, 'Cliente no especificado') AS cliente
        FROM detalle_ordenes do
        JOIN productos p ON do.id_producto = p.id
        JOIN ordenes o ON do.id_orden = o.id
        LEFT JOIN clientes c ON o.id_cliente = c.id
        WHERE do.id_orden = :id_orden
    ");
    $query->bindParam(':id_orden', $id_orden, PDO::PARAM_INT);
    $query->execute();
    $detalles = $query->fetchAll(PDO::FETCH_ASSOC);

    if (empty($detalles)) {
        die("No se encontraron detalles para la orden #$id_orden.");
    }
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
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/admin/detalle_orden.css">
</head>
<body>
    <header>
        <h1>Detalle de Orden #<?php echo htmlspecialchars($id_orden); ?></h1>
        <a href="ordenes.php" class="return-button">Volver a Órdenes</a>
    </header>
    <main>
        <div class="boleta-detalle">
            <h2>Boleta de Venta</h2>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($detalles[0]['cliente']); ?></p>
            <p><strong>Fecha de Compra:</strong> <?php echo htmlspecialchars($detalles[0]['fecha']); ?></p>
            <hr>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $detalle): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($detalle['producto']); ?></td>
                            <td><?php echo htmlspecialchars($detalle['cantidad']); ?></td>
                            <td>S/<?php echo number_format($detalle['subtotal'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <hr>
            <p><strong>Total:</strong> S/<?php echo number_format($detalles[0]['total'], 2); ?></p>
        </div>
    </main>
</body>
</html>