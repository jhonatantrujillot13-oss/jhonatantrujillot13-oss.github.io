<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'Admin') {
    header('Location: auth/login.php');
    exit();
}
include 'includes/conexion.php';

if (!isset($_GET['id'])) {
    header('Location: admin_manuales.php');
    exit();
}

$id = (int)$_GET['id'];
$mensaje = '';
$tipo = '';

try {
    $stmt = $conn->prepare("SELECT * FROM manuales WHERE id = ?");
    $stmt->execute([$id]);
    $manual = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$manual) {
        header('Location: admin_manuales.php');
        exit();
    }
} catch (PDOException $e) {
    header('Location: admin_manuales.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);

    if (empty($titulo)) {
        $mensaje = 'El título es obligatorio.';
        $tipo = 'danger';
    } else {
        $archivoNombre = $manual['archivo'];
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            if (!file_exists('files/manuales')) {
                mkdir('files/manuales', 0777, true);
            }
            $tmp = $_FILES['archivo']['tmp_name'];
            $nombre = $_FILES['archivo']['name'];
            $ext = pathinfo($nombre, PATHINFO_EXTENSION);
            $nuevo = 'files/manuales/' . uniqid() . '.' . $ext;
            if (move_uploaded_file($tmp, $nuevo)) {
                // eliminar anterior
                if ($manual['archivo'] && file_exists($manual['archivo'])) {
                    @unlink($manual['archivo']);
                }
                $archivoNombre = $nuevo;
            } else {
                $mensaje = 'Error al subir el archivo.';
                $tipo = 'danger';
            }
        }

        if ($mensaje === '') {
            try {
                $stmt = $conn->prepare("UPDATE manuales SET titulo = ?, descripcion = ?, archivo = ? WHERE id = ?");
                $stmt->execute([$titulo, $descripcion, $archivoNombre, $id]);
                $mensaje = 'Manual actualizado.';
                $tipo = 'success';
                // refrescar datos
                $manual['titulo'] = $titulo;
                $manual['descripcion'] = $descripcion;
                $manual['archivo'] = $archivoNombre;
            } catch (PDOException $e) {
                $mensaje = 'Error al actualizar.';
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
    <title>Editar Manual - Admin</title>
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
                <div class="card-header bg-warning text-dark">Editar Manual</div>
                <div class="card-body">
                    <?php if ($mensaje): ?>
                        <div class="alert alert-<?= $tipo ?>"><?= htmlspecialchars($mensaje) ?></div>
                    <?php endif; ?>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="titulo" class="form-control" required value="<?= htmlspecialchars($manual['titulo']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción (opcional)</label>
                            <textarea name="descripcion" class="form-control" rows="4"><?= htmlspecialchars($manual['descripcion']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Archivo actual</label>
                            <div class="mb-2">
                                <?php if ($manual['archivo']): ?>
                                    <a href="<?= htmlspecialchars($manual['archivo']) ?>" target="_blank">Ver / Descargar</a>
                                <?php else: ?>
                                    <span class="text-muted">Sin archivo</span>
                                <?php endif; ?>
                            </div>
                            <label class="form-label">Cambiar archivo (opcional)</label>
                            <input type="file" name="archivo" class="form-control" accept=".pdf,.doc,.docx,.txt">
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="admin_manuales.php" class="btn btn-secondary">Volver</a>
                            <button class="btn btn-warning">Guardar</button>
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
