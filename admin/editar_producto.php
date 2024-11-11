<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit;
}

require 'includes/db_admin.php';

$error = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
    $query->bindParam(':id', $id);
    $query->execute();
    $producto = $query->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        header("Location: productos.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $imagen_ruta = $producto['imagen']; // Mantener imagen actual por defecto

    // Validar si se sube una nueva imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen_tmp = $_FILES['imagen']['tmp_name'];
        $imagen_nombre = $_FILES['imagen']['name'];
        $imagen_tipo = mime_content_type($imagen_tmp);

        if (in_array($imagen_tipo, ['image/jpeg', 'image/png', 'image/webp'])) {
            $directorio_destino = 'uploads/';
            if (!is_dir($directorio_destino)) {
                mkdir($directorio_destino, 0777, true);
            }

            $imagen_ruta = $directorio_destino . uniqid() . '-' . basename($imagen_nombre);
            if (!move_uploaded_file($imagen_tmp, $imagen_ruta)) {
                $error = "Error al mover la imagen al directorio de destino.";
            }
        } else {
            $error = "La imagen debe ser de tipo JPG, PNG o WEBP.";
        }
    }

    if (!$error) {
        $query = $pdo->prepare("UPDATE productos SET nombre = :nombre, descripcion = :descripcion, precio = :precio, stock = :stock, imagen = :imagen WHERE id = :id");
        $query->bindParam(':nombre', $nombre);
        $query->bindParam(':descripcion', $descripcion);
        $query->bindParam(':precio', $precio);
        $query->bindParam(':stock', $stock);
        $query->bindParam(':imagen', $imagen_ruta);
        $query->bindParam(':id', $id);
        $query->execute();

        header("Location: productos.php?mensaje=Producto actualizado correctamente");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/global.css">
    <link rel="stylesheet" href="/El_Rincon_de_Melo/assets/css/admin/editar_producto.css">
</head>
<body>
    <header class="nav-header">
        <h1>Editar Producto</h1>
        <a href="productos.php" class="return-button">Volver</a>
    </header>
    <main>
        <div class="form-container">
            <!-- Marco de previsualización -->
            <div class="image-upload-container">
                <div class="image-preview" id="imagePreview">
                    <img src="/El_Rincon_de_Melo/admin/<?php echo htmlspecialchars($producto['imagen'] ?? ''); ?>" 
                         alt="Previsualización de la imagen" 
                         class="image-preview__image" 
                         style="<?php echo $producto['imagen'] ? 'display: block;' : 'display: none;'; ?>">
                    <span class="image-preview__default-text" 
                          style="<?php echo $producto['imagen'] ? 'display: none;' : 'display: block;'; ?>">
                        Sin imagen seleccionada
                    </span>
                </div>
            </div>

            <!-- Formulario -->
            <div class="form-wrapper">
                <form action="editar_producto.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>

                    <label for="descripcion">Descripción:</label>
                    <textarea name="descripcion" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>

                    <label for="precio">Precio:</label>
                    <input type="number" name="precio" step="0.01" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>

                    <label for="stock">Stock:</label>
                    <input type="number" name="stock" value="<?php echo htmlspecialchars($producto['stock']); ?>" required>

                    <label for="imagen">Actualizar Imagen:</label>
                    <input type="file" name="imagen" accept="image/*" id="fileInput" onchange="previewImage(event)">

                    <button type="submit">Actualizar Producto</button>
                </form>
            </div>
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