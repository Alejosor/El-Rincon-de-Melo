<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit;
}

require 'includes/db_admin.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = trim($_POST['precio']);
    $stock = trim($_POST['stock']);

    // Validación del nombre para evitar duplicados
    $query = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE nombre LIKE :nombre");
    $nombre_like = '%' . $nombre . '%';
    $query->bindParam(':nombre', $nombre_like);
    $query->execute();
    $existe_nombre = $query->fetchColumn();

    if ($existe_nombre > 0) {
        $error = "Ya existe un producto con un nombre similar.";
    } elseif (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen_tmp = $_FILES['imagen']['tmp_name'];
        $imagen_nombre = $_FILES['imagen']['name'];
        $imagen_tipo = mime_content_type($imagen_tmp);

        if (!in_array($imagen_tipo, ['image/jpeg', 'image/png', 'image/webp'])) {
            $error = "La imagen debe ser de tipo JPG, PNG o WEBP.";
        } else {
            $directorio_destino = __DIR__ . '/uploads/';
            if (!is_dir($directorio_destino)) {
                mkdir($directorio_destino, 0777, true);
            }

            $imagen_ruta = $directorio_destino . uniqid() . '-' . basename($imagen_nombre);
            if (!move_uploaded_file($imagen_tmp, $imagen_ruta)) {
                $error = "Error al mover la imagen al directorio de destino.";
            }
        }
    } else {
        $error = "No se subió ninguna imagen.";
    }

    if (!$error && !empty($nombre) && !empty($descripcion) && is_numeric($precio) && is_numeric($stock)) {
        try {
            $query = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, imagen) VALUES (:nombre, :descripcion, :precio, :stock, :imagen)");
            $query->bindParam(':nombre', $nombre);
            $query->bindParam(':descripcion', $descripcion);
            $query->bindParam(':precio', $precio);
            $query->bindParam(':stock', $stock);
            $query->bindParam(':imagen', $imagen_ruta);
            $query->execute();

            header("Location: productos.php");
            exit;
        } catch (PDOException $e) {
            $error = "Error al guardar el producto: " . $e->getMessage();
        }
    } elseif (!$error) {
        $error = "Todos los campos son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Producto</title>
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/global.css">
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/admin/nuevo_producto.css">
</head>
<body>
    <header class="nav-header">
        <h1>Añadir Nuevo Producto</h1>
        <a href="productos.php" class="return-button">Volver</a>
    </header>
    <main>
        <div class="form-container">
            <!-- Marco de previsualización fuera del formulario -->
            <div class="image-upload-container">
                <div class="image-preview" id="imagePreview">
                    <img src="" alt="Previsualización de la imagen" class="image-preview__image" style="display: none;">
                    <span class="image-preview__default-text">Sin imagen seleccionada</span>
                </div>
            </div>

            <!-- Formulario -->
            <form action="nuevo_producto.php" method="POST" enctype="multipart/form-data">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" required>

                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" required></textarea>

                <label for="precio">Precio:</label>
                <input type="number" name="precio" step="0.01" required>

                <label for="stock">Stock:</label>
                <input type="number" name="stock" required>

                <label for="imagen">Seleccionar Imagen:</label>
                <input type="file" name="imagen" accept="image/*" id="fileInput" onchange="previewImage(event)" required>

                <button type="submit">Añadir Producto</button>
            </form>
        </div>

        <!-- Alerta de error -->
        <?php if ($error): ?>
            <div class="alert-card">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>
    </main>

    <script>
        function previewImage(event) {
            const imagePreview = document.getElementById('imagePreview');
            const previewImage = imagePreview.querySelector('.image-preview__image');
            const defaultText = imagePreview.querySelector('.image-preview__default-text');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = "block";
                    defaultText.style.display = "none";
                };

                reader.readAsDataURL(file);
            } else {
                previewImage.style.display = "none";
                defaultText.style.display = "block";
            }
        }
    </script>
</body>
</html>