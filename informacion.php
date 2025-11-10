<?php
session_start();
include 'includes/conexion.php';

try {
  $stmt = $conn->query("SELECT id, slug, titulo, contenido FROM informacion ORDER BY id ASC");
  $infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $infos = [];
}
// Traer algunos profesores destacados para mostrar en la página
try {
  $pstmt = $conn->prepare("SELECT p.id, p.nombre, p.educacion, p.telefono, p.horario, p.foto, p.sede_id, s.nombre AS sede FROM personal p LEFT JOIN sedes s ON p.sede_id = s.id WHERE p.rol = 'Profesor' ORDER BY p.nombre ASC LIMIT 6");
  $pstmt->execute();
  $profesores = $pstmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $profesores = []; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Información - I.E. Promoción Social</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/notifications.css" rel="stylesheet">
    <link href="css/informacion.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="info-page">
<?php include 'includes/header.php'; ?>
<section class="hero-section">
  <div class="container text-center text-white position-relative">
    <h1 class="hero-title">Información</h1>
    <p class="hero-subtitle">Guía práctica sobre matrículas, traslados, retiros y trámites importantes de la institución.</p>
  </div>
</section>

<div class="container my-5">
  <div class="row">
    <div class="col-lg-4">
      <div class="list-group sticky-top info-list-group" style="top:120px;">
        <?php foreach ($infos as $inf): ?>
          <a class="list-group-item list-group-item-action" href="#<?= htmlspecialchars($inf['slug']) ?>"><?= htmlspecialchars($inf['titulo']) ?></a>
        <?php endforeach; ?>
        <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'Admin'): ?>
        <?php endif; ?>
      </div>
    </div>
    <div class="col-lg-8">

      <?php if (!empty($infos)): ?>
        <?php foreach ($infos as $inf): ?>
          <section id="<?= htmlspecialchars($inf['slug']) ?>" class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="info-section-title"><?= htmlspecialchars($inf['titulo']) ?></span>

            </div>
            <div class="info-card">
              <div class="info-content">
                <?= nl2br(htmlspecialchars($inf['contenido'])) ?>
              </div>
            </div>
          </section>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="alert alert-info">No hay información disponible.</div>
      <?php endif; ?>

      <!-- Profesores destacados -->
      <section class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="text-primary">Profesores destacados</h3>
          <div>
            <?php if (!(isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'Admin')): ?>
              <a href="tecnicos.php" class="btn btn-sm btn-outline-secondary">Ver personal</a>
            <?php endif; ?>
          </div>
        </div>

        <?php if (!empty($profesores)): ?>
        <div class="row g-3">
          <?php 
          // Simulación: agregar campo calificacion_promedio (en producción, traer de la BD)
          foreach ($profesores as &$prof) {
            $prof['calificacion_promedio'] = isset($prof['calificacion_promedio']) ? $prof['calificacion_promedio'] : rand(3,5) + (rand(0,9)/10); // Simulado
          }
          // Ordenar por calificación promedio descendente
          usort($profesores, function($a, $b) { return $b['calificacion_promedio'] <=> $a['calificacion_promedio']; });
          unset($prof);
          ?>
          <?php foreach ($profesores as $prof): ?>
              <div class="col-md-6 col-lg-4">
                <div class="profesor-card shadow-sm rounded-4 p-3 mb-4 bg-white position-relative h-100">
                  <?php if ($prof['foto']): ?>
                    <img src="img/<?= htmlspecialchars($prof['foto']) ?>" class="profesor-img mx-auto mt-3" alt="<?= htmlspecialchars($prof['nombre']) ?>" onerror="this.src='img/escudo.jpeg'">
                  <?php endif; ?>
                  <div class="text-center mt-2 mb-1">
                    <span class="profesor-nombre fw-bold" style="font-size:1.25rem; letter-spacing:0.5px; color:#222;"> <?= htmlspecialchars($prof['nombre']) ?> </span>
                    <div class="profesor-educacion text-secondary mb-1" style="font-size:1rem;"> <?= htmlspecialchars($prof['educacion']) ?> </div>
                    <div class="profesor-estrellas mb-2" 
                         data-id="<?= $prof['id'] ?>" 
                         data-rating="<?= $prof['calificacion_promedio'] ?>"
                         <?= isset($_SESSION['calificaciones'][$prof['id']]) ? 'class="ya-calificado"' : '' ?>>
                      <?php $cal = round($prof['calificacion_promedio'] * 2) / 2; ?>
                      <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if ($cal >= $i): ?>
                          <i class="bi bi-star-fill text-warning star" data-value="<?= $i ?>"></i>
                        <?php elseif ($cal >= $i-0.5): ?>
                          <i class="bi bi-star-half text-warning star" data-value="<?= $i ?>"></i>
                        <?php else: ?>
                          <i class="bi bi-star text-warning star" data-value="<?= $i ?>"></i>
                        <?php endif; ?>
                      <?php endfor; ?>
                      <span class="ms-1 text-muted promedio-calificacion" style="font-size:0.95rem;">
                        (<?= number_format($prof['calificacion_promedio'],1) ?>)
                      </span>
                    </div>
                  </div>
                  <div class="text-center mb-2">
                    <span class="d-block text-muted" style="font-size:0.98rem;"><strong>Sede:</strong> <?= htmlspecialchars($prof['sede'] ?? 'N/A') ?></span>
                    <span class="d-block text-muted" style="font-size:0.98rem;"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($prof['telefono'] ?? '') ?></span>
                  </div>
                  <div class="d-flex justify-content-between gap-2 mt-3">
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'Admin'): ?>
                      <a href="admin.php#personal" class="btn btn-outline-warning flex-fill">
                        <i class="bi bi-person-gear me-1"></i>Gestionar
                      </a>
                    <?php endif; ?>
                    <a href="sedes_detalle.php?id=<?= $prof['sede_id'] ?? '' ?>" class="btn btn-outline-primary flex-fill">
                      <i class="bi bi-building me-1"></i>Ver sede
                    </a>
                  </div>
                </div>
              </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
          <div class="alert alert-secondary">No hay profesores destacados por el momento.</div>
        <?php endif; ?>
      </section>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/custom.js"></script>
</body>
</html>
