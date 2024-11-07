<?php
session_start();
session_destroy(); // Destruir todas las sesiones activas
header("Location: login_user.php"); // Redirigir al login
exit;
?>