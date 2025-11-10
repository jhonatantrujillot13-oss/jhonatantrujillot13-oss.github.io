<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'Admin') {
    header('Location: auth/login.php');
    exit();
}
include 'includes/conexion.php';


$mensaje = '';
$tipo_mensaje = '';

// Obtener categorías
$categorias = [];
try {
    $catStmt = $conn->prepare("SELECT id, nombre FROM categorias_noticias ORDER BY nombre ASC");
    $catStmt->execute();
    $categorias = $catStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

// Verificar que se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    $categoria_id = isset($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null;

    if (empty($titulo) || empty($contenido) || empty($categoria_id)) {
        $mensaje = "Todos los campos son obligatorios, incluida la categoría.";
        $tipo_mensaje = 'danger';
    } else {
        // Obtener información de la imagen
        $imagenNombre = '';
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $imagenNombre = $_FILES['imagen']['name'];
            $imagenTmp = $_FILES['imagen']['tmp_name'];
            
            // Validar tipo de archivo
            $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $tipoArchivo = mime_content_type($imagenTmp);
            
            if (!in_array($tipoArchivo, $tiposPermitidos)) {
                $mensaje = "Solo se permiten archivos de imagen (JPG, PNG, GIF).";
                $tipo_mensaje = 'danger';
            } else {
                // Generar nombre único para la imagen
                $extension = pathinfo($imagenNombre, PATHINFO_EXTENSION);
                $imagenNombre = uniqid() . '.' . $extension;
                
                // Asegurar que la carpeta existe
                if (!file_exists('img')) {
                    mkdir('img', 0777, true);
                }

                // Carpeta de destino
                $rutaDestino = 'img/' . $imagenNombre;

                // Mover el archivo a la carpeta
                if (move_uploaded_file($imagenTmp, $rutaDestino)) {
                    try {
                        // Guardar en la base de datos
                        $stmt = $conn->prepare("INSERT INTO noticias (titulo, contenido, imagen, fecha_publicacion, categoria_id) VALUES (?, ?, ?, NOW(), ?)");
                        $stmt->execute([$titulo, $contenido, $imagenNombre, $categoria_id]);
                        $mensaje = "Noticia publicada correctamente.";
                        $tipo_mensaje = 'success';
                        
                        // Limpiar formulario
                        $titulo = '';
                        $contenido = '';
                    } catch (PDOException $e) {
                        $mensaje = "Error al guardar en la base de datos.";
                        $tipo_mensaje = 'danger';
                    }
                } else {
                    $mensaje = "Error al subir la imagen.";
                    $tipo_mensaje = 'danger';
                }
            }
        } else {
            // Sin imagen
            try {
                $stmt = $conn->prepare("INSERT INTO noticias (titulo, contenido, fecha_publicacion, categoria_id) VALUES (?, ?, NOW(), ?)");
                $stmt->execute([$titulo, $contenido, $categoria_id]);
                $mensaje = "Noticia publicada correctamente.";
                $tipo_mensaje = 'success';
                
                // Limpiar formulario
                $titulo = '';
                $contenido = '';
            } catch (PDOException $e) {
                $mensaje = "Error al guardar en la base de datos.";
                $tipo_mensaje = 'danger';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Noticia - I.E. Promoción Social</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container" style="margin-top: 120px;">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card form-card">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>Publicar Nueva Noticia
                    </h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($mensaje): ?>
                        <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($mensaje) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="titulo" class="form-label fw-bold">Título de la Noticia</label>
                            <input type="text" name="titulo" id="titulo" class="form-control" value="<?= htmlspecialchars($titulo ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="contenido" class="form-label fw-bold">Contenido</label>
                            <textarea name="contenido" id="contenido" class="form-control" rows="8" required><?= htmlspecialchars($contenido ?? '') ?></textarea>
                            <div class="form-text">Escribe el contenido completo de la noticia.</div>
                        </div>
                        <div class="mb-3">
                            <label for="categoria_id" class="form-label fw-bold">Categoría</label>
                            <select name="categoria_id" id="categoria_id" class="form-select" required>
                                <option value="">Selecciona una categoría</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (isset($categoria_id) && $categoria_id == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="imagen" class="form-label fw-bold">Imagen (Opcional)</label>
                            <input type="file" name="imagen" id="imagen" class="form-control" accept="image/*">
                            <div class="form-text">Formatos permitidos: JPG, PNG, GIF. Máximo 5MB.</div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="admin.php" class="btn btn-secondary me-md-2">
                                <i class="bi bi-arrow-left me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Publicar Noticia
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
