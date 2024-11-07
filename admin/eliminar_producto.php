<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit;
}

require 'includes/db_admin.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = $pdo->prepare("DELETE FROM productos WHERE id = :id");
    $query->bindParam(':id', $id);
    $query->execute();
}

header("Location: productos.php");
exit;
?>
