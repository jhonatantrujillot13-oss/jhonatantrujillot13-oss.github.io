<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'Admin') {
    header('Location: auth/login.php');
    exit();
}
include 'includes/conexion.php';

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slug = trim($_POST['slug']);
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    if ($slug === '' || $titulo === '' || $contenido === '') {
        $mensaje = 'Todos los campos son obligatorios.';
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO informacion (slug, titulo, contenido) VALUES (?, ?, ?)");
            $stmt->execute([$slug, $titulo, $contenido]);
            header('Location: admin_informacion.php'); exit();
        } catch (PDOException $e) {
            $mensaje = 'Error al guardar.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agregar Información - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container" style="margin-top:120px;">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header bg-primary text-white">Agregar sección informativa</div>
        <div class="card-body">
          <?php if ($mensaje): ?><div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div><?php endif; ?>
          <form method="post">
            <div class="mb-3">
              <label class="form-label">Slug (p.ej. matriculas)</label>
              <input class="form-control" name="slug" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Título</label>
              <input class="form-control" name="titulo" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Contenido</label>
              <textarea class="form-control" name="contenido" rows="8" required></textarea>
            </div>
            <div class="d-flex justify-content-end">
              <a href="admin_informacion.php" class="btn btn-secondary me-2">Cancelar</a>
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
