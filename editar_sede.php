<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'Admin') { header('Location: auth/login.php'); exit(); }
include 'includes/conexion.php';
if (!isset($_GET['id'])) { header('Location: admin_sedes.php'); exit(); }
$id = (int)$_GET['id'];
try { $stmt = $conn->prepare("SELECT * FROM sedes WHERE id = ?"); $stmt->execute([$id]); $sede = $stmt->fetch(PDO::FETCH_ASSOC); if (!$sede) header('Location: admin_sedes.php'); } catch (PDOException $e) { header('Location: admin_sedes.php'); }
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']); $direccion = trim($_POST['direccion']); $telefono = trim($_POST['telefono']); $descripcion = trim($_POST['descripcion']); $tipo = trim($_POST['tipo']);
    $imagen = $sede['imagen'];
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        if (!file_exists('img')) mkdir('img', 0777, true);
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imagen = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], 'img/' . $imagen);
        if ($sede['imagen'] && file_exists('img/' . $sede['imagen'])) @unlink('img/' . $sede['imagen']);
    }
    try { $stmt = $conn->prepare("UPDATE sedes SET nombre = ?, direccion = ?, telefono = ?, descripcion = ?, imagen = ?, tipo = ? WHERE id = ?"); $stmt->execute([$nombre, $direccion, $telefono, $descripcion, $imagen, $tipo, $id]); header('Location: admin_sedes.php'); exit(); } catch (PDOException $e) { $mensaje = 'Error al actualizar'; }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Sede</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container" style="margin-top:120px;">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header bg-warning text-dark">Editar Sede</div>
        <div class="card-body">
          <?php if ($mensaje): ?><div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div><?php endif; ?>
          <form method="post" enctype="multipart/form-data">
            <div class="mb-3"><label class="form-label">Nombre</label><input class="form-control" name="nombre" required value="<?= htmlspecialchars($sede['nombre']) ?>"></div>
            <div class="mb-3"><label class="form-label">Dirección</label><input class="form-control" name="direccion" value="<?= htmlspecialchars($sede['direccion']) ?>"></div>
            <div class="mb-3"><label class="form-label">Teléfono</label><input class="form-control" name="telefono" value="<?= htmlspecialchars($sede['telefono']) ?>"></div>
            <div class="mb-3"><label class="form-label">Tipo</label><input class="form-control" name="tipo" value="<?= htmlspecialchars($sede['tipo']) ?>"></div>
            <div class="mb-3"><label class="form-label">Descripción</label><textarea class="form-control" name="descripcion" rows="4"><?= htmlspecialchars($sede['descripcion']) ?></textarea></div>
            <div class="mb-3">
              <label class="form-label">Imagen actual</label>
              <div class="mb-2"><?php if ($sede['imagen']): ?><img src="img/<?= htmlspecialchars($sede['imagen']) ?>" style="max-height:120px;" alt=""><?php else: ?>Sin imagen<?php endif; ?></div>
              <label class="form-label">Cambiar imagen</label>
              <input type="file" name="imagen" class="form-control" accept="image/*">
            </div>
            <div class="d-flex justify-content-end"><a href="admin_sedes.php" class="btn btn-secondary me-2">Cancelar</a><button class="btn btn-warning">Guardar</button></div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
