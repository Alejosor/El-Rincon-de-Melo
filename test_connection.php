<?php
require 'admin/includes/db_admin.php';

if ($pdo) {
    echo "Conexión exitosa a la base de datos.";
} else {
    echo "Conexión fallida.";
}
?>