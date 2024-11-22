<?php
session_start();
require 'includes/db_user.php';

if (!isset($_SESSION['cliente_id'])) {
    header("Location: login_user.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    if (!empty($_SESSION['carrito'])) {
        try {
            // Insertar la orden en la tabla `ordenes`
            $query = $pdo->prepare("INSERT INTO ordenes (id_cliente, fecha, total, estado) VALUES (:id_cliente, NOW(), :total, 'Pendiente')");
            $query->bindParam(':id_cliente', $_SESSION['cliente_id']);
            $query->bindParam(':total', $total);

            // Calcular total y guardar precios de productos
            $total = 0;
            $productosPrecios = []; // Array temporal para almacenar precios de los productos
            foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
                $queryProducto = $pdo->prepare("SELECT precio FROM productos WHERE id = :id");
                $queryProducto->bindParam(':id', $id_producto);
                $queryProducto->execute();
                $producto = $queryProducto->fetch(PDO::FETCH_ASSOC);

                if (!$producto) {
                    throw new Exception("Producto con ID $id_producto no encontrado.");
                }

                $productosPrecios[$id_producto] = $producto['precio'];
                $total += $producto['precio'] * $cantidad;
            }

            $query->execute();
            $id_orden = $pdo->lastInsertId();

            // Insertar los detalles de la orden
            foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
                if (!isset($productosPrecios[$id_producto])) {
                    throw new Exception("Precio del producto con ID $id_producto no encontrado.");
                }

                $subtotal = $productosPrecios[$id_producto] * $cantidad;

                $queryDetalle = $pdo->prepare("INSERT INTO detalle_ordenes (id_orden, id_producto, cantidad, subtotal) VALUES (:id_orden, :id_producto, :cantidad, :subtotal)");
                $queryDetalle->bindParam(':id_orden', $id_orden);
                $queryDetalle->bindParam(':id_producto', $id_producto);
                $queryDetalle->bindParam(':cantidad', $cantidad);
                $queryDetalle->bindParam(':subtotal', $subtotal);
                $queryDetalle->execute();
            }

            // Limpiar carrito
            $_SESSION['carrito'] = [];

            header("Location: ordenes_exito.html");
            exit;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        header("Location: carrito.php?error=carrito_vacio");
        exit;
    }
} else {
    header("Location: carrito.php");
    exit;
}
?>