<?php
session_start();
require 'includes/db_user.php';

if (!isset($_SESSION['cliente_id'])) {
    header("Location: login_user.php");
    exit;
}

$success = false;
$id_orden = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    if (!empty($_SESSION['carrito'])) {
        try {
            // Calcular total
            $total = 0;
            foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
                $queryProducto = $pdo->prepare("SELECT precio FROM productos WHERE id = :id");
                $queryProducto->bindParam(':id', $id_producto);
                $queryProducto->execute();
                $producto = $queryProducto->fetch(PDO::FETCH_ASSOC);
                $total += $producto['precio'] * $cantidad;
            }

            // Insertar la orden
            $query = $pdo->prepare("INSERT INTO ordenes (id_cliente, fecha, total, estado) VALUES (:id_cliente, NOW(), :total, 'Pendiente')");
            $query->bindParam(':id_cliente', $_SESSION['cliente_id']);
            $query->bindParam(':total', $total);
            $query->execute();
            $id_orden = $pdo->lastInsertId();

            // Insertar detalles de la orden
            foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
                $queryDetalle = $pdo->prepare("INSERT INTO detalle_ordenes (id_orden, id_producto, cantidad, subtotal) VALUES (:id_orden, :id_producto, :cantidad, :subtotal)");
                $queryDetalle->bindParam(':id_orden', $id_orden);
                $queryDetalle->bindParam(':id_producto', $id_producto);
                $queryDetalle->bindParam(':cantidad', $cantidad);
                $subtotal = $producto['precio'] * $cantidad;
                $queryDetalle->bindParam(':subtotal', $subtotal);
                $queryDetalle->execute();
            }

            // Limpiar carrito
            $_SESSION['carrito'] = [];
            $success = true;

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        header("Location: carrito.php?error=carrito_vacio");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Compra</title>
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/user/ordenes_exito.css">
</head>
<body>
    <header>
        <h1>Confirmación de Compra</h1>
    </header>
    <main>
        <?php if ($success): ?>
            <div class="success-message">
                <h2>¡Gracias por tu compra!</h2>
                <p>Tu compra ha sido realizada con éxito. Puedes descargar o imprimir tu boleta de venta.</p>
                <a href="boleta_usuario.php?id=<?php echo $id_orden; ?>" target="_blank" class="btn">Ver/Imprimir Boleta</a>
                <a href="index_user.php" class="btn return">Volver al Catálogo</a>
            </div>
        <?php else: ?>
            <div class="error-message">
                <h2>Ocurrió un problema</h2>
                <p>No se pudo procesar tu compra. Por favor, intenta nuevamente.</p>
                <a href="carrito.php" class="btn return">Volver al Carrito</a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>