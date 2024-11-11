<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit;
}

require 'includes/db_admin.php';

if (!isset($_GET['id'])) {
    header("Location: productos.php");
    exit;
}

$id = $_GET['id'];

try {
    // Verificar si el producto está relacionado en detalle_ordenes
    $query_check = $pdo->prepare("SELECT COUNT(*) FROM detalle_ordenes WHERE id_producto = :id");
    $query_check->bindParam(':id', $id, PDO::PARAM_INT);
    $query_check->execute();
    $referencias = $query_check->fetchColumn();

    if ($referencias > 0) {
        header("Location: productos.php?error=No se puede eliminar el producto porque está relacionado con una o más órdenes.");
        exit;
    }

    // Eliminar el producto si no tiene referencias
    $query = $pdo->prepare("DELETE FROM productos WHERE id = :id");
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();

    header("Location: productos.php?mensaje=Producto eliminado correctamente");
    exit;
} catch (PDOException $e) {
    die("Error al eliminar el producto: " . $e->getMessage());
}
?>