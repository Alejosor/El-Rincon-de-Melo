<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit;
}

require 'includes/db_admin.php';

if (!isset($_GET['id']) || !isset($_GET['tipo'])) {
    header("Location: usuarios_admin.php");
    exit;
}

$id = $_GET['id'];
$tipo = $_GET['tipo'];

if ($tipo === 'Administrador') {
    $query = $pdo->prepare("DELETE FROM administradores WHERE id = :id");
} else {
    $query = $pdo->prepare("DELETE FROM clientes WHERE id = :id");
}

$query->bindParam(':id', $id, PDO::PARAM_INT);

try {
    $query->execute();
    header("Location: usuarios_admin.php");
    exit;
} catch (PDOException $e) {
    die("Error al eliminar usuario: " . $e->getMessage());
}
?>