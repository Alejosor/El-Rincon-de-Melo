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

// Habilitar la visualización de errores para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['id'])) {
    $id_orden = $_GET['id'];

    // Obtener los detalles de la orden
    $query = $pdo->prepare("
        SELECT do.id_producto, p.nombre, do.cantidad, do.subtotal 
        FROM detalle_ordenes do
        JOIN productos p ON do.id_producto = p.id
        WHERE do.id_orden = :id_orden
    ");
    $query->bindParam(':id_orden', $id_orden, PDO::PARAM_INT);
    $query->execute();
    $detalles = $query->fetchAll(PDO::FETCH_ASSOC);

    // Validación en caso de que no haya detalles
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
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <header>
        <h1>Detalle de Orden #<?php echo htmlspecialchars($id_orden); ?></h1>
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
                <?php foreach ($detalles as $detalle): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($detalle['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($detalle['cantidad']); ?></td>
                        <td>S/<?php echo number_format($detalle['subtotal'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>