<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'Admin') { header('Location: auth/login.php'); exit(); }
include 'includes/conexion.php';

// delete
if (isset($_GET['delete'])) {
    try { $stmt = $conn->prepare("DELETE FROM personal WHERE id = ?"); $stmt->execute([(int)$_GET['delete']]); header('Location: admin_personal.php'); exit(); } catch (PDOException $e) { $error = 'Error al eliminar.'; }
}

try {
    $stmt = $conn->query("SELECT p.id, p.nombre, p.rol, p.telefono, p.horario, s.nombre as sede_nombre FROM personal p LEFT JOIN sedes s ON p.sede_id = s.id ORDER BY p.nombre ASC");
    $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt2 = $conn->query("SELECT id, nombre FROM sedes ORDER BY nombre ASC"); $sedes = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $personal = []; $sedes = []; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Personal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container" style="margin-top:120px;">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary">Personal (Profesores, Técnicos, Instructores)</h2>
    <a href="crear_personal.php" class="btn btn-primary">Agregar Personal</a>
  </div>
  <?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <div class="table-responsive">
    <table class="table table-bordered">
      <thead class="table-dark"><tr><th>ID</th><th>Nombre</th><th>Rol</th><th>Sede</th><th>Teléfono</th><th>Horario</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php if ($personal): foreach ($personal as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['id']) ?></td>
            <td><?= htmlspecialchars($p['nombre']) ?></td>
            <td><?= htmlspecialchars($p['rol']) ?></td>
            <td><?= htmlspecialchars($p['sede_nombre']) ?></td>
            <td><?= htmlspecialchars($p['telefono']) ?></td>
            <td><?= htmlspecialchars($p['horario']) ?></td>
            <td>
              <a href="editar_personal.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm me-1">Editar</a>
              <a href="admin_personal.php?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Eliminar?')">Eliminar</a>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="7" class="text-center">No hay personal registrado</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
