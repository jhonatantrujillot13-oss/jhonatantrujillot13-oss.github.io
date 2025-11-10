<?php
session_start();
include 'includes/conexion.php';

// Mostrar técnicos e instructores por defecto
try {
    $stmt = $conn->prepare("SELECT p.id, p.nombre, p.educacion, p.telefono, p.horario, p.foto, s.nombre as sede FROM personal p LEFT JOIN sedes s ON p.sede_id = s.id WHERE p.rol IN ('Tecnico','Instructor') ORDER BY p.nombre ASC");
    $stmt->execute();
    $tecnicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $tecnicos = []; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Técnicos e Instructores</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <style>
    .card-personal { transition: transform .25s ease; }
    .card-personal:hover { transform: translateY(-6px); box-shadow: 0 12px 30px rgba(0,0,0,0.12); }
    .foto-personal { width:100%; height:220px; object-fit:cover; }
  </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container" style="margin-top:120px;">
  <div class="text-center mb-4"><h1 class="text-primary">Técnicos e Instructores</h1><p class="lead">Información general y contacto de los técnicos e instructores vinculados a la institución.</p></div>
  <div class="row g-4">
    <?php if ($tecnicos): foreach ($tecnicos as $t): ?>
      <div class="col-md-4">
        <div class="card card-personal h-100">
          <?php if ($t['foto']): ?><img src="img/<?= htmlspecialchars($t['foto']) ?>" alt="<?= htmlspecialchars($t['nombre']) ?>" class="foto-personal card-img-top" onerror="this.src='img/escudo.jpeg'" /><?php endif; ?>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?= htmlspecialchars($t['nombre']) ?></h5>
            <p class="text-muted mb-1"><strong>Educación:</strong> <?= htmlspecialchars($t['educacion']) ?></p>
            <p class="text-muted mb-1"><strong>Sede:</strong> <?= htmlspecialchars($t['sede']) ?></p>
            <p class="text-muted mb-1"><strong>Teléfono:</strong> <?= htmlspecialchars($t['telefono']) ?></p>
            <p class="text-muted mb-3"><strong>Horario:</strong> <?= htmlspecialchars($t['horario']) ?></p>
            <div class="mt-auto d-flex gap-2">
              <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'Admin'): ?>
                <a href="editar_personal.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-warning">Editar</a>
              <?php endif; ?>
              <a href="mailto:<?= htmlspecialchars($t['telefono']) ?>" class="btn btn-sm btn-primary">Contactar</a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; else: ?>
      <div class="col-12"><div class="alert alert-info">No hay técnicos o instructores registrados.</div></div>
    <?php endif; ?>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
