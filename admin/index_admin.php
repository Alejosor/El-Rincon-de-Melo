<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php"); 
    exit;
}

require 'includes/db_admin.php';

// Consultar cantidad de usuarios (clientes y administradores)
$queryUsuarios = $pdo->query("SELECT 
    (SELECT COUNT(*) FROM clientes) AS total_clientes,
    (SELECT COUNT(*) FROM administradores) AS total_administradores");
$usuarios = $queryUsuarios->fetch(PDO::FETCH_ASSOC);

// Consultar cantidad total de productos
$queryProductos = $pdo->query("SELECT COUNT(*) AS total_productos FROM productos WHERE activo = 1");
$totalProductos = $queryProductos->fetchColumn();

// Consultar cantidad total de órdenes completadas
$queryOrdenes = $pdo->query("SELECT COUNT(*) AS total_ordenes FROM ordenes WHERE estado = 'Confirmada'");
$totalOrdenes = $queryOrdenes->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador</title>
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/admin/index_admin.css">
</head>
<body>
    <header class="nav-header">
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></h1>
        <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
    </header>
    <main>
        <section class="dashboard">
            <div class="card">
                <h2>Gestión de Usuarios</h2>
                <p>Administra clientes y administradores.</p>
                <a href="usuarios_admin.php" class="btn">Ir a Usuarios</a>
            </div>
            <div class="card">
                <h2>Gestión de Productos</h2>
                <p>Administra el inventario y productos.</p>
                <a href="productos.php" class="btn">Ir a Productos</a>
            </div>
            <div class="card">
                <h2>Gestión de Órdenes</h2>
                <p>Revisa y confirma las órdenes realizadas.</p>
                <a href="ordenes.php" class="btn">Ir a Órdenes</a>
            </div>
        </section>
        <section class="stats">
            <h2>Estadísticas Rápidas</h2>
            <div class="stats-container">
                <div class="stat">
                    <h3>Clientes</h3>
                    <p><?php echo htmlspecialchars($usuarios['total_clientes']); ?> registrados</p>
                </div>
                <div class="stat">
                    <h3>Administradores</h3>
                    <p><?php echo htmlspecialchars($usuarios['total_administradores']); ?> registrados</p>
                </div>
                <div class="stat">
                    <h3>Productos</h3>
                    <p><?php echo htmlspecialchars($totalProductos); ?> en stock</p>
                </div>
                <div class="stat">
                    <h3>Órdenes Confirmadas</h3>
                    <p><?php echo htmlspecialchars($totalOrdenes); ?> completadas</p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>