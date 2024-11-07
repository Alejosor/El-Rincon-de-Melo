<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit;
}

require 'includes/db_admin.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
    $query->bindParam(':id', $id);
    $query->execute();
    $producto = $query->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        header("Location: productos.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    $query = $pdo->prepare("UPDATE productos SET nombre = :nombre, descripcion = :descripcion, precio = :precio, stock = :stock WHERE id = :id");
    $query->bindParam(':nombre', $nombre);
    $query->bindParam(':descripcion', $descripcion);
    $query->bindParam(':precio', $precio);
    $query->bindParam(':stock', $stock);
    $query->bindParam(':id', $id);
    $query->execute();

    header("Location: productos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <header>
        <h1>Editar Producto</h1>
        <a href="productos.php">Volver a Productos</a>
    </header>
    <main>
        <form action="editar_producto.php?id=<?php echo $id; ?>" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" value="<?php echo $producto['nombre']; ?>" required>
            
            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" required><?php echo $producto['descripcion']; ?></textarea>
            
            <label for="precio">Precio:</label>
            <input type="number" name="precio" step="0.01" value="<?php echo $producto['precio']; ?>" required>
            
            <label for="stock">Stock:</label>
            <input type="number" name="stock" value="<?php echo $producto['stock']; ?>" required>
            
            <button type="submit">Actualizar Producto</button>
        </form>
    </main>
</body>
</html>
