<?php
require 'includes/db_user.php';

$query = $pdo->query("SELECT * FROM productos WHERE stock > 0");
$productos = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Rincón de Melo - Productos</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>El Rincón de Melo</h1>
        <a href="carrito.php">Ver Carrito</a>
    </header>
    <main>
        <div class="productos">
            <?php foreach ($productos as $producto): ?>
                <div class="producto">
                    <h2><?php echo $producto['nombre']; ?></h2>
                    <p><?php echo $producto['descripcion']; ?></p>
                    <p>Precio: S/<?php echo $producto['precio']; ?></p>
                    <form action="carrito.php" method="POST">
                        <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                        <label for="cantidad">Cantidad:</label>
                        <input type="number" name="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>" required>
                        <button type="submit" name="add_to_cart">Añadir al Carrito</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
