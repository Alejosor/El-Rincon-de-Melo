<?php
session_start();
require 'includes/db_user.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $id_producto = $_POST['id_producto'];
    $cantidad = $_POST['cantidad'];

    if (isset($_SESSION['cart'][$id_producto])) {
        $_SESSION['cart'][$id_producto] += $cantidad;
    } else {
        $_SESSION['cart'][$id_producto] = $cantidad;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_cart'])) {
    $id_producto = $_POST['id_producto'];
    unset($_SESSION['cart'][$id_producto]);
}

$total = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Carrito de Compras</h1>
        <a href="index_user.php">Volver a Productos</a>
    </header>
    <main>
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
                <?php foreach ($_SESSION['cart'] as $id_producto => $cantidad):
                    $query = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
                    $query->bindParam(':id', $id_producto);
                    $query->execute();
                    $producto = $query->fetch(PDO::FETCH_ASSOC);

                    $subtotal = $producto['precio'] * $cantidad;
                    $total += $subtotal;
                ?>
                    <tr>
                        <td><?php echo $producto['nombre']; ?></td>
                        <td><?php echo $cantidad; ?></td>
                        <td><?php echo $producto['precio']; ?></td>
                        <td><?php echo $subtotal; ?></td>
                        <td>
                            <form action="carrito.php" method="POST">
                                <input type="hidden" name="id_producto" value="<?php echo $id_producto; ?>">
                                <button type="submit" name="remove_from_cart">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h3>Total: S/<?php echo $total; ?></h3>
        <a href="confirmacion.php">Confirmar Orden</a>
    </main>
</body>
</html>
