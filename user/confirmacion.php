<?php
session_start();
require 'includes/db_user.php';

if (empty($_SESSION['cart'])) {
    header("Location: index_user.php");
    exit;
}

$total = 0;

foreach ($_SESSION['cart'] as $id_producto => $cantidad) {
    $query = $pdo->prepare("SELECT precio FROM productos WHERE id = :id");
    $query->bindParam(':id', $id_producto);
    $query->execute();
    $producto = $query->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        die("Error: El producto con ID $id_producto no existe.");
    }

    $subtotal = $producto['precio'] * $cantidad;
    $total += $subtotal;
}

// Inserta la orden en la tabla `ordenes`
$query = $pdo->prepare("INSERT INTO ordenes (total) VALUES (:total)");
$query->bindParam(':total', $total);
$query->execute();
$id_orden = $pdo->lastInsertId();

// Inserta los detalles de la orden y actualiza el stock
foreach ($_SESSION['cart'] as $id_producto => $cantidad) {
    $query = $pdo->prepare("SELECT precio FROM productos WHERE id = :id");
    $query->bindParam(':id', $id_producto);
    $query->execute();
    $producto = $query->fetch(PDO::FETCH_ASSOC);

    $subtotal = $producto['precio'] * $cantidad;

    $query = $pdo->prepare("INSERT INTO detalle_ordenes (id_orden, id_producto, cantidad, subtotal) VALUES (:id_orden, :id_producto, :cantidad, :subtotal)");
    $query->bindParam(':id_orden', $id_orden);
    $query->bindParam(':id_producto', $id_producto);
    $query->bindParam(':cantidad', $cantidad);
    $query->bindParam(':subtotal', $subtotal);
    $query->execute();

    $query_update_stock = $pdo->prepare("UPDATE productos SET stock = stock - :cantidad WHERE id = :id_producto");
    $query_update_stock->bindParam(':cantidad', $cantidad);
    $query_update_stock->bindParam(':id_producto', $id_producto);
    $query_update_stock->execute();
}

unset($_SESSION['cart']);
header("Location: ordenes_exito.html");
exit;
?>