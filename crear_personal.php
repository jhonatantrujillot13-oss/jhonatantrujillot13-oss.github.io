<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'Admin') { header('Location: auth/login.php'); exit(); }
include 'includes/conexion.php';

$mensaje = '';
try { $stmt = $conn->query("SELECT id, nombre FROM sedes ORDER BY nombre ASC"); $sedes = $stmt->fetchAll(PDO::FETCH_ASSOC); } catch (PDOException $e) { $sedes = []; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $educacion = trim($_POST['educacion']);
    $edad = (int)$_POST['edad'];
    $telefono = trim($_POST['telefono']);
    $horario = trim($_POST['horario']);
    $sede_id = $_POST['sede_id'] ? (int)$_POST['sede_id'] : null;
    $rol = $_POST['rol'];
    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        if (!file_exists('img')) mkdir('img', 0777, true);
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], 'img/' . $foto);
    }
    try {
        $stmt = $conn->prepare("INSERT INTO personal (nombre, educacion, edad, telefono, horario, sede_id, rol, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $educacion, $edad, $telefono, $horario, $sede_id, $rol, $foto]);
        header('Location: admin_personal.php'); exit();
    } catch (PDOException $e) { $mensaje = 'Error al guardar.'; }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Crear Personal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container" style="margin-top:120px;">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header bg-primary text-white">Agregar Personal</div>
        <div class="card-body">
          <?php if ($mensaje): ?><div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div><?php endif; ?>
          <form method="post" enctype="multipart/form-data">
            <div class="mb-3"><label class="form-label">Nombre</label><input class="form-control" name="nombre" required></div>
            <div class="mb-3"><label class="form-label">Educación</label><input class="form-control" name="educacion"></div>
            <div class="mb-3"><label class="form-label">Edad</label><input type="number" class="form-control" name="edad"></div>
            <div class="mb-3"><label class="form-label">Teléfono</label><input class="form-control" name="telefono"></div>
            <div class="mb-3"><label class="form-label">Horario de atención</label><input class="form-control" name="horario"></div>
            <div class="mb-3"><label class="form-label">Sede</label><select class="form-select" name="sede_id"><option value="">-- Seleccionar --</option><?php foreach ($sedes as $s): ?><option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?></option><?php endforeach; ?></select></div>
            <div class="mb-3"><label class="form-label">Rol</label><select class="form-select" name="rol"><option>Profesor</option><option>Tecnico</option><option>Instructor</option></select></div>
            <div class="mb-3"><label class="form-label">Foto (opcional)</label><input type="file" name="foto" class="form-control" accept="image/*"></div>
            <div class="d-flex justify-content-end"><a href="admin_personal.php" class="btn btn-secondary me-2">Cancelar</a><button class="btn btn-primary">Guardar</button></div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
