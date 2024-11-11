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
    $query = $pdo->prepare("UPDATE productos SET activo = 0 WHERE id = :id");
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();

    header("Location: productos.php?mensaje=Producto deshabilitado correctamente");
    exit;
} catch (PDOException $e) {
    die("Error al deshabilitar el producto: " . $e->getMessage());
}
?>