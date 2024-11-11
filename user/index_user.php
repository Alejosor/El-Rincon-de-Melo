<?php
session_start();
if (!isset($_SESSION['cliente_id'])) {
    header("Location: login_user.php");
    exit;
}

require 'includes/db_user.php';

// Obtener los productos disponibles
$query = $pdo->query("SELECT * FROM productos WHERE stock > 0");
$productos = $query->fetchAll(PDO::FETCH_ASSOC);

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $id_producto = $_POST['id_producto'];
    $cantidad = (int)$_POST['cantidad'];

    if (isset($_SESSION['carrito'][$id_producto])) {
        $_SESSION['carrito'][$id_producto] += $cantidad;
    } else {
        $_SESSION['carrito'][$id_producto] = $cantidad;
    }

    header("Location: carrito.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - El Rincón de Melo</title>
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/user/index_user.css">
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Bienvenid@, <?php echo htmlspecialchars($_SESSION['cliente_nombre']); ?></h1>
            <nav>
                <ul>
                    <li><a href="index_user.php">Inicio</a></li>
                    <li><a href="carrito.php">Mi Carrito (<?php echo array_sum($_SESSION['carrito']); ?>)</a></li>
                    <li><a href="logout.php" class="logout-button">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <section>
            <h2>Nuestros Productos</h2>
            <div class="productos-container">
                <?php foreach ($productos as $producto): ?>
                    <div class="producto-card">
                        <img src="/El_Rincon_de_Melo/admin/uploads/<?php echo htmlspecialchars(basename($producto['imagen'])); ?>" 
                             alt="Imagen de <?php echo htmlspecialchars($producto['nombre']); ?>">
                        <div class="producto-info">
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                            <p>Precio: S/<?php echo number_format($producto['precio'], 2); ?></p>
                            <form action="index_user.php" method="POST">
                                <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                                <label for="cantidad">Cantidad:</label>
                                <input type="number" name="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>" required>
                                <button type="submit" name="add_to_cart">Añadir al Carrito</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 El Rincón de Melo. Todos los derechos reservados.</p>
    </footer>
</body>
</html>