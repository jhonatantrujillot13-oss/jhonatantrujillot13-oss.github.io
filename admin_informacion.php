<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'Admin') {
    header('Location: auth/login.php');
    exit();
}
include 'includes/conexion.php';

$mensaje = '';

// listar
try {
    $stmt = $conn->query("SELECT id, slug, titulo, contenido, updated_at FROM informacion ORDER BY id ASC");
    $infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $infos = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Información</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container" style="margin-top:120px;">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary">Gestión de Información</h2>
    <a href="crear_informacion.php" class="btn btn-primary">Agregar sección</a>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Slug</th>
          <th>Título</th>
          <th>Actualizado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($infos)): foreach ($infos as $i): ?>
        <tr>
          <td><?= htmlspecialchars($i['id']) ?></td>
          <td><?= htmlspecialchars($i['slug']) ?></td>
          <td><?= htmlspecialchars($i['titulo']) ?></td>
          <td><?= htmlspecialchars($i['updated_at']) ?></td>
          <td>
            <a href="editar_informacion.php?id=<?= $i['id'] ?>" class="btn btn-warning btn-sm me-1"><i class="bi bi-pencil"></i></a>
            <a href="admin_informacion.php?delete=<?= $i['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Eliminar?')"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="5" class="text-center">No hay entradas</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
