<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'Admin') {
    header('Location: auth/login.php');
    exit();
}
include 'includes/conexion.php';

// Handle delete
if (isset($_GET['delete'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM manuales WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        header('Location: admin_manuales.php');
        exit();
    } catch (PDOException $e) {
        $error = 'Error al eliminar el manual.';
    }
}

try {
    $stmt = $conn->query("SELECT id, titulo, descripcion, archivo, created_at FROM manuales ORDER BY created_at DESC");
    $manuales = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $manuales = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Manuales - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container" style="margin-top:120px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary">Gestionar Manuales</h2>
            <a href="crear_manual.php" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Agregar Manual</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Archivo</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($manuales)): ?>
                        <?php foreach ($manuales as $m): ?>
                            <tr>
                                <td><?= htmlspecialchars($m['id']) ?></td>
                                <td><?= htmlspecialchars($m['titulo']) ?></td>
                                <td>
                                    <?php if ($m['archivo']): ?>
                                        <a href="<?= htmlspecialchars($m['archivo']) ?>" target="_blank">Ver / Descargar</a>
                                    <?php else: ?>
                                        <span class="text-muted">Sin archivo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($m['created_at']) ?></td>
                                <td>
                                    <a href="editar_manual.php?id=<?= $m['id'] ?>" class="btn btn-warning btn-sm me-1"><i class="bi bi-pencil"></i></a>
                                    <a href="admin_manuales.php?delete=<?= $m['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar manual?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay manuales</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
