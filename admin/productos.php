<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit;
}

require 'includes/db_admin.php';

// Consultar productos activos
$query_activos = $pdo->query("SELECT * FROM productos WHERE activo = 1");
$productos_activos = $query_activos->fetchAll(PDO::FETCH_ASSOC);

// Consultar productos deshabilitados
$query_inactivos = $pdo->query("SELECT * FROM productos WHERE activo = 0");
$productos_inactivos = $query_inactivos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/admin/productos.css">
</head>
<body>
    <header>
        <h1>Gestión de Productos</h1>
        <a href="index_admin.php" class="return-button">Volver al Panel</a>
    </header>

    <!-- Mensajes de éxito o error -->
    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert-card success">
            <p><?php echo htmlspecialchars($_GET['mensaje']); ?></p>
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert-card error">
            <p><?php echo htmlspecialchars($_GET['error']); ?></p>
        </div>
    <?php endif; ?>

    <main>
        <div class="header-actions">
            <a href="nuevo_producto.php" class="btn create-btn">Añadir Producto</a>
        </div>

        <!-- Tabla de productos activos -->
        <h2>Productos Activos</h2>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos_activos as $producto): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($producto['descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($producto['precio']); ?></td>
                        <td><?php echo htmlspecialchars($producto['stock']); ?></td>
                        <td>
                            <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" class="btn action-btn">Editar</a>
                            <a href="eliminar_producto.php?id=<?php echo $producto['id']; ?>" class="btn action-btn" onclick="return confirm('¿Estás seguro de eliminar este producto?');">Eliminar</a>
                            <a href="deshabilitar_producto.php?id=<?php echo $producto['id']; ?>" class="btn action-btn" onclick="return confirm('¿Estás seguro de deshabilitar este producto?');">Deshabilitar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Tabla de productos deshabilitados -->
        <h2>Productos Deshabilitados</h2>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos_inactivos as $producto): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($producto['descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($producto['precio']); ?></td>
                        <td><?php echo htmlspecialchars($producto['stock']); ?></td>
                        <td>
                            <a href="habilitar_producto.php?id=<?php echo $producto['id']; ?>" class="btn action-btn" onclick="return confirm('¿Estás seguro de habilitar este producto?');">Habilitar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>