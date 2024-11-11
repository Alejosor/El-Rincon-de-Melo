<?php
session_start();
require 'includes/db_user.php';

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Manejo de eliminación de productos del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_cart'])) {
    $id_producto = $_POST['id_producto'];
    unset($_SESSION['carrito'][$id_producto]);
}

// Obtener los detalles de los productos en el carrito
$productos_carrito = [];
if (!empty($_SESSION['carrito'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['carrito']), '?'));
    $query = $pdo->prepare("SELECT * FROM productos WHERE id IN ($placeholders)");
    $query->execute(array_keys($_SESSION['carrito']));
    $productos_carrito = $query->fetchAll(PDO::FETCH_ASSOC);
}

$total = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/user/carrito.css">
</head>
<body>
    <header>
        <h1>Carrito de Compras</h1>
        <a href="index_user.php" class="btn-back">Volver a Productos</a>
    </header>
    <main>
        <?php if (!empty($productos_carrito)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos_carrito as $producto):
                        $cantidad = $_SESSION['carrito'][$producto['id']];
                        $subtotal = $producto['precio'] * $cantidad;
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td><?php echo $cantidad; ?></td>
                            <td>S/<?php echo number_format($producto['precio'], 2); ?></td>
                            <td>S/<?php echo number_format($subtotal, 2); ?></td>
                            <td>
                                <form action="carrito.php" method="POST">
                                    <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                                    <button type="submit" name="remove_from_cart" class="btn-remove">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Total: S/<?php echo number_format($total, 2); ?></h3>
            <form action="confirmar_orden.php" method="POST">
                <button type="submit" name="confirm_order" class="btn-confirm">Confirmar Orden</button>
            </form>
        <?php else: ?>
            <p>Tu carrito está vacío.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2024 El Rincón de Melo. Todos los derechos reservados.</p>
    </footer>
</body>
</html>