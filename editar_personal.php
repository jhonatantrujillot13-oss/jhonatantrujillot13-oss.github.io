<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'Admin') { header('Location: auth/login.php'); exit(); }
include 'includes/conexion.php';
if (!isset($_GET['id'])) { header('Location: admin_personal.php'); exit(); }
$id = (int)$_GET['id'];
try { $stmt = $conn->prepare("SELECT * FROM personal WHERE id = ?"); $stmt->execute([$id]); $p = $stmt->fetch(PDO::FETCH_ASSOC); if (!$p) header('Location: admin_personal.php'); } catch (PDOException $e) { header('Location: admin_personal.php'); }
try { $stmt2 = $conn->query("SELECT id, nombre FROM sedes ORDER BY nombre ASC"); $sedes = $stmt2->fetchAll(PDO::FETCH_ASSOC); } catch (PDOException $e) { $sedes = []; }
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']); $educacion = trim($_POST['educacion']); $edad = (int)$_POST['edad']; $telefono = trim($_POST['telefono']); $horario = trim($_POST['horario']); $sede_id = $_POST['sede_id'] ? (int)$_POST['sede_id'] : null; $rol = $_POST['rol']; $foto = $p['foto'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        if (!file_exists('img')) mkdir('img', 0777, true);
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nuevo = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], 'img/' . $nuevo);
        if ($p['foto'] && file_exists('img/' . $p['foto'])) @unlink('img/' . $p['foto']);
        $foto = $nuevo;
    }
    try { $stmt = $conn->prepare("UPDATE personal SET nombre=?, educacion=?, edad=?, telefono=?, horario=?, sede_id=?, rol=?, foto=? WHERE id=?"); $stmt->execute([$nombre,$educacion,$edad,$telefono,$horario,$sede_id,$rol,$foto,$id]); header('Location: admin_personal.php'); exit(); } catch (PDOException $e) { $mensaje='Error al actualizar'; }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Personal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container" style="margin-top:120px;">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header bg-warning text-dark">Editar: <?= htmlspecialchars($p['nombre']) ?></div>
        <div class="card-body">
          <?php if ($mensaje): ?><div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div><?php endif; ?>
          <form method="post" enctype="multipart/form-data">
            <div class="mb-3"><label class="form-label">Nombre</label><input class="form-control" name="nombre" required value="<?= htmlspecialchars($p['nombre']) ?>"></div>
            <div class="mb-3"><label class="form-label">Educación</label><input class="form-control" name="educacion" value="<?= htmlspecialchars($p['educacion']) ?>"></div>
            <div class="mb-3"><label class="form-label">Edad</label><input type="number" class="form-control" name="edad" value="<?= htmlspecialchars($p['edad']) ?>"></div>
            <div class="mb-3"><label class="form-label">Teléfono</label><input class="form-control" name="telefono" value="<?= htmlspecialchars($p['telefono']) ?>"></div>
            <div class="mb-3"><label class="form-label">Horario</label><input class="form-control" name="horario" value="<?= htmlspecialchars($p['horario']) ?>"></div>
            <div class="mb-3"><label class="form-label">Sede</label><select class="form-select" name="sede_id"><option value="">-- Seleccionar --</option><?php foreach ($sedes as $s): ?><option value="<?= $s['id'] ?>" <?= $p['sede_id']==$s['id'] ? 'selected':'' ?>><?= htmlspecialchars($s['nombre']) ?></option><?php endforeach; ?></select></div>
            <div class="mb-3"><label class="form-label">Rol</label><select class="form-select" name="rol"><option <?= $p['rol']=='Profesor'?'selected':'' ?>>Profesor</option><option <?= $p['rol']=='Tecnico'?'selected':'' ?>>Tecnico</option><option <?= $p['rol']=='Instructor'?'selected':'' ?>>Instructor</option></select></div>
            <div class="mb-3"><label class="form-label">Foto actual</label><div><?php if ($p['foto']): ?><img src="img/<?= htmlspecialchars($p['foto']) ?>" style="max-height:120px;" alt=""><?php else: ?>Sin foto<?php endif; ?></div></div>
            <div class="mb-3"><label class="form-label">Cambiar foto</label><input type="file" name="foto" class="form-control" accept="image/*"></div>
            <div class="d-flex justify-content-end"><a href="admin_personal.php" class="btn btn-secondary me-2">Cancelar</a><button class="btn btn-warning">Guardar</button></div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
