<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'Admin') {
    header('Location: auth/login.php');
    exit();
}
include 'includes/conexion.php';

// delete
if (isset($_GET['delete'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM sedes WHERE id = ?");
        $stmt->execute([(int)$_GET['delete']]);
        header('Location: admin_sedes.php'); exit();
    } catch (PDOException $e) { $error = 'Error al eliminar sede.'; }
}

try {
    $stmt = $conn->query("SELECT id, nombre, direccion, telefono, tipo FROM sedes ORDER BY nombre ASC");
    $sedes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $sedes = []; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Sedes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container" style="margin-top:120px;">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
      <a href="sedes_detalle.php" class="btn btn-outline-secondary rounded-pill px-3 me-2" style="font-weight:600;">
        <i class="bi bi-arrow-left"></i> Volver a Sedes
      </a>
      <h2 class="text-primary mb-0">Sedes</h2>
    </div>
    <a href="crear_sede.php" class="btn btn-primary">Agregar Sede</a>
  </div>
  <?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <div class="table-responsive">
    <table class="table table-bordered">
      <thead class="table-dark"><tr><th>ID</th><th>Nombre</th><th>Dirección</th><th>Teléfono</th><th>Tipo</th><th>Acciones</th></tr></thead>
      <tbody>
      <?php if ($sedes): foreach ($sedes as $s): ?>
        <tr>
          <td><?= htmlspecialchars($s['id']) ?></td>
          <td><?= htmlspecialchars($s['nombre']) ?></td>
          <td><?= htmlspecialchars($s['direccion']) ?></td>
          <td><?= htmlspecialchars($s['telefono']) ?></td>
          <td><?= htmlspecialchars($s['tipo']) ?></td>
          <td>
            <a href="editar_sede.php?id=<?= $s['id'] ?>" class="btn btn-warning btn-sm me-1">Editar</a>
            <a href="admin_sedes.php?delete=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Eliminar?')">Eliminar</a>
          </td>
        </tr>
      <?php endforeach; else: ?>
        <tr><td colspan="6" class="text-center">No hay sedes</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
