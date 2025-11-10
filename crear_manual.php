<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'Admin') {
    header('Location: auth/login.php');
    exit();
}
include 'includes/conexion.php';

$mensaje = '';
$tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);

    if (empty($titulo)) {
        $mensaje = 'El título es obligatorio.';
        $tipo = 'danger';
    } else {
        $archivoNombre = null;
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['archivo']['tmp_name'];
            $nombre = $_FILES['archivo']['name'];

            // Asegurar carpeta
            if (!file_exists('files/manuales')) {
                mkdir('files/manuales', 0777, true);
            }

            $ext = pathinfo($nombre, PATHINFO_EXTENSION);
            $archivoNombre = 'files/manuales/' . uniqid() . '.' . $ext;
            if (!move_uploaded_file($tmp, $archivoNombre)) {
                $mensaje = 'Error al subir el archivo.';
                $tipo = 'danger';
                $archivoNombre = null;
            }
        }

        if ($mensaje === '') {
            try {
                $stmt = $conn->prepare("INSERT INTO manuales (titulo, descripcion, archivo, creado_por) VALUES (?, ?, ?, ?)");
                $stmt->execute([$titulo, $descripcion, $archivoNombre, $_SESSION['usuario_id']]);
                $mensaje = 'Manual creado correctamente.';
                $tipo = 'success';
            } catch (PDOException $e) {
                $mensaje = 'Error al guardar en la base de datos.';
                $tipo = 'danger';
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
    <title>Agregar Manual - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container" style="margin-top:120px;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">Agregar Manual</div>
                <div class="card-body">
                    <?php if ($mensaje): ?>
                        <div class="alert alert-<?= $tipo ?>"><?= htmlspecialchars($mensaje) ?></div>
                    <?php endif; ?>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="titulo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción (opcional)</label>
                            <textarea name="descripcion" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Archivo (PDF/DOCX) - opcional</label>
                            <input type="file" name="archivo" class="form-control" accept=".pdf,.doc,.docx,.txt">
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="admin_manuales.php" class="btn btn-secondary">Cancelar</a>
                            <button class="btn btn-primary">Guardar</button>
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
