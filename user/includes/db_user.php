<?php
// Configuración de conexión a la base de datos
$host = 'localhost';     // Servidor de base de datos
$dbname = 'el_rincon_melo'; // Nombre de la base de datos
$username = 'root';      // Usuario 
$password = '';          // Contraseña 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>