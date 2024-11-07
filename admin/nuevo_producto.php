<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit;
}

require 'includes/db_admin.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    $query = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (:nombre, :descripcion, :precio, :stock)");
    $query->bindParam(':nombre', $nombre);
    $query->bindParam(':descripcion', $descripcion);
    $query->bindParam(':precio', $precio);
    $query->bindParam(':stock', $stock);
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
    <title>Nuevo Producto</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <header>
        <h1>Añadir Nuevo Producto</h1>
        <a href="productos.php">Volver a Productos</a>
    </header>
    <main>
        <form action="nuevo_producto.php" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" required>
            
            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" required></textarea>
            
            <label for="precio">Precio:</label>
            <input type="number" name="precio" step="0.01" required>
            
            <label for="stock">Stock:</label>
            <input type="number" name="stock" required>
            
            <button type="submit">Añadir Producto</button>
        </form>
    </main>
</body>
</html>
