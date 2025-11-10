<?php
session_start();
include 'includes/conexion.php';

// Fetch latest news
try {
    $stmt = $conn->prepare("SELECT id, titulo, contenido, imagen, fecha_publicacion FROM noticias ORDER BY fecha_publicacion DESC LIMIT 6");
    $stmt->execute();
    $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $noticias = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>I.E. Promoción Social - Palermo, Huila</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
    .collage-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 40px;
  }

  .collage-item {
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
  }

  .collage-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
  }

  .collage-item:hover img {
    transform: scale(1.05);
  }

  .caption {
    position: absolute;
    bottom: 0;
    width: 100%;
    background: rgba(13, 110, 253, 0.8);
    color: #fff;
    text-align: center;
    padding: 10px;
    font-weight: 600;
    font-size: 1rem;
  }
  </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Navbar (incluido desde header.php) -->
<?php include 'includes/header.php'; ?>

<!-- Hero con imagen de fondo y degradado -->
<section class="hero d-flex align-items-center justify-content-center" style="min-height: 400px; margin-top: 120px;">
  <div class="container text-center py-5">
    <h1 class="display-4 fw-bold text-light text-shadow mb-3 animate__fadeInDown">¡Bienvenidos a la I.E. Promoción Social!</h1>
    <p class="lead text-light text-shadow mb-4 animate__fadeInUp">Educando con valores, excelencia y compromiso en Palermo, Huila.</p>
    <div class="animate__fadeInUp" style="animation-delay: 0.3s;">
      <a href="#noticias" class="btn btn-light btn-lg px-4 me-3">
        <i class="bi bi-newspaper me-2"></i>Ver Noticias
      </a>
      <a href="sedes.php" class="btn btn-outline-light btn-lg px-4">
        <i class="bi bi-building me-2"></i>Nuestras Sedes
      </a>
    </div>
  </div>
</section>
<div class="row">
  <div class="col-md-12">
    <section class="container my-5 py-4" style="max-width: 880px;">
      <div class="row align-items-center g-4">
        <div class="col-md-6 animate__animated animate__fadeInLeft">
          <img src="img/colegio-1.jpg" alt="Historia Colegio" class="img-fluid rounded shadow-lg" style="min-height: 260px; object-fit: cover;">
        </div>
        <div class="col-md-6 animate__animated animate__fadeInRight">
          <h4 class="fw-bold mb-3 text-primary">Nuestra Historia</h4>
          <h6 class="lead">La Institución Educativa Promoción Social de Palermo, Huila, fue fundada con el propósito de brindar educación integral y de calidad a la comunidad. A lo largo de los años, ha formado generaciones de estudiantes comprometidos con los valores humanos, la excelencia académica y el desarrollo social. Su historia es testimonio de esfuerzo, dedicación y transformación, consolidándose como un pilar educativo en la región.
          </h6>
        </div>
      </div>
    </section>
  </div>
</div>

<!-- Card de Símbolos Institucionales -->
<div class="row">
  
  <div class="col-lg-6">
    <div class="container my-4">
      <div class="row justify-content-center">
        <div class="col-md-10">
          <div class="card shadow-lg border-0">
            <div class="row g-0 align-items-center">
              <div class="col-md-4 text-center p-3">
                <img src="img/Bandera.jpg" alt="Símbolos Institucionales" class="img-fluid rounded shadow" style="max-width: 110px; height: auto;">
              </div>
              <div class="col-md-8">
                <div class="card-body">
                  <h4 class="card-title fw-bold text-primary mb-2">Símbolos</h4>
                  <p class="card-text">Nuestros símbolos institucionales representan la identidad, los valores y la historia de la I.E. Promoción Social. El escudo, la bandera y el himno reflejan el compromiso, la unidad y el orgullo de toda la comunidad educativa.</p>
                  <a href="simbolos.php" class="btn btn-outline-primary mt-2"><i class="bi bi-eye me-1"></i>Conocer símbolos</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="container my-4">
      <div class="row justify-content-center">
        <div class="col-md-10">
          <div class="card shadow-lg border-0">
            <div class="row g-0 align-items-center">
              <div class="col-md-4 text-center p-3">
                <img src="img/manualcomvi.jpeg" alt="Manual de Convivencia" class="img-fluid rounded shadow" style="max-height: 160px; object-fit: contain;">
              </div>
              <div class="col-md-8">
                <div class="card-body">
                  <h4 class="card-title fw-bold text-primary mb-2">Manual de Convivencia</h4>
                  <p class="card-text">El Manual de Convivencia es la guía fundamental que orienta la vida escolar, promoviendo el respeto, la sana convivencia y la formación integral de todos los miembros de la comunidad educativa. Consulta aquí las normas, derechos y deberes que nos identifican.</p>
                  <?php
                  // Obtener el manual más reciente desde la tabla `manuales` si existe
                  try {
                      $stmtManual = $conn->prepare("SELECT id, titulo, archivo FROM manuales ORDER BY created_at DESC LIMIT 1");
                      $stmtManual->execute();
                      $manual = $stmtManual->fetch(PDO::FETCH_ASSOC);
                  } catch (PDOException $e) {
                      $manual = null;
                  }

                  if ($manual && !empty($manual['archivo'])): ?>
                    <a href="<?= htmlspecialchars($manual['archivo']) ?>" class="btn btn-primary mt-2" target="_blank"><i class="bi bi-file-earmark-text me-1"></i>Descargar <?= htmlspecialchars($manual['titulo']) ?></a>
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'Admin'): ?>
                      <a href="admin_manuales.php" class="btn btn-outline-secondary mt-2 ms-2"><i class="bi bi-gear me-1"></i>Gestionar Manuales</a>
                    <?php endif; ?>
                  <?php else: ?>
                    <a href="#" class="btn btn-secondary mt-2 disabled"><i class="bi bi-file-earmark-text me-1"></i>Manual no disponible</a>
                    <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'Admin'): ?>
                      <a href="crear_manual.php" class="btn btn-outline-secondary mt-2 ms-2"><i class="bi bi-plus-circle me-1"></i>Agregar Manual</a>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div id="tecnicos" class="row justify-content-center my-5">
  <div class="col-lg-5">
    <!-- técnico software -->
    <section class="p-4" style="background-color: #f8f9fa; border-radius: 10px;">
      <div class="row align-items-center g-4">
        <div class="col-md-12 animate__animated animate__fadeInLeft">
          <img src="img/software.jpg" alt="Instalaciones Colegio" class="img-fluid rounded shadow-lg" style="min-height: 260px; object-fit: cover;">
        </div>
        <div class="col-md-12 animate__animated animate__fadeInRight">
          <h5 class="card-title fw-bold text-primary">
            <i class="bi bi-code-slash"></i> Técnico en Programación de Software
            <span class="badge bg-success ms-2">SENA</span>
          </h5>
          <p class="card-text">Formación en desarrollo de aplicaciones, lógica de programación, <br> bases de datos y herramientas modernas de software, en convenio con el SENA.</p>
         <a href="tecnico_softw.php" class="btn btn-outline-primary mt-2"><i class="bi bi-info-circle me-1"></i>Conocer más</a>
        </div>
      </div>
    </section>
  </div>

  <div class="col-lg-5">
    <!-- técnico contabilidad -->
    <section class="p-4" style="background-color: #f8f9fa; border-radius: 10px;">
      <div class="row align-items-center g-4">
        <div class="col-md-12 animate__animated animate__fadeInLeft">
          <img src="img/contabilidad.jpg" alt="Instalaciones Colegio" class="img-fluid rounded shadow-lg" style="min-height: 260px; object-fit: cover;">
        </div>
        <div class="col-md-12 animate__animated animate__fadeInRight">
          <h5 class="card-title fw-bold text-primary">
            <i class="bi bi-code-slash"></i> Técnico en Contabilidad
            <span class="badge bg-success ms-2">SENA</span>
          </h5>
          <p class="card-text">Formación en procesos contables, manejo de software financiero y <br> gestión administrativa, en convenio con el SENA.</p>
           <a href="tecnico_conta.php" class="btn btn-outline-primary mt-2"><i class="bi bi-info-circle me-1"></i>Conocer más</a>
        </div>
      </div>
    </section>
  </div>
</div>



  
    <section class="container my-5 py-4" style="max-width: 950px;">
      <div class="row align-items-center g-4">
        <div class="col-md-6 animate__animated animate__fadeInLeft">
          <img src="img/I.E.2.jpeg" alt="Instalaciones Colegio" class="img-fluid rounded shadow-lg" style="min-height: 260px; object-fit: cover;">
        </div>
        <div class="col-md-6 animate__animated animate__fadeInRight">
          <h4 class="fw-bold mb-3 text-primary">Nuestras Instalaciones</h4>
          <h6 class="lead">La institución cuenta con modernas instalaciones que favorecen el aprendizaje y el desarrollo integral de los estudiantes. Aulas equipadas, laboratorios y espacios verdes conforman un entorno seguro y acogedor para toda la comunidad educativa.</h6>
          <a href="instalaciones.php" class="btn btn-outline-primary mt-2"><i class="bi bi-info-circle me-1"></i>Conocer más</a>
        </div>
      </div>
    </section>
  


<!-- Noticias -->
<section class="section-padding bg-light" id="noticias">
  <div class="container">
    <div class="row justify-content-center mb-5">
      <div class="col-lg-8 text-center">
        <h2 class="display-5 fw-bold text-primary mb-3">
          <i class="bi bi-megaphone-fill me-2"></i>
          Últimas Noticias y Eventos
        </h2>
        <p class="lead text-muted">Mantente informado sobre las actividades y eventos más importantes de nuestra institución.</p>
      </div>
    </div>
    
    <div class="row">
      <?php if (!empty($noticias)): ?>
        <?php foreach ($noticias as $n): ?>
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card h-100 shadow-custom">
            <div class="position-relative">
              <img src="img/<?php echo htmlspecialchars($n['imagen']); ?>" alt="<?php echo htmlspecialchars($n['titulo']); ?>" class="card-img-top" style="height: 220px; object-fit: cover;" onerror="this.src='img/escudo.jpeg'">
              <div class="position-absolute top-0 end-0 m-2">
                <span class="badge bg-primary">
                  <i class="bi bi-calendar-event me-1"></i>
                  <?= date('d/m/Y', strtotime($n['fecha_publicacion'])) ?>
                </span>
              </div>
            </div>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title fw-bold text-primary"><?= htmlspecialchars($n['titulo']) ?></h5>
              <p class="card-text flex-grow-1 text-muted"><?= htmlspecialchars(substr($n['contenido'], 0, 120)) ?>...</p>
              <div class="d-flex gap-2 mt-auto">
                <a href="ver_noticia.php?id=<?= $n['id'] ?>" class="btn btn-primary flex-fill">
                  <i class="bi bi-arrow-right me-1"></i>Leer más
                </a>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                  <button class="btn btn-outline-primary" onclick="abrirComentarioRapido(<?= $n['id'] ?>, '<?= htmlspecialchars($n['titulo']) ?>')" title="Comentar">
                    <i class="bi bi-chat-dots"></i>
                  </button>
                <?php else: ?>
                  <a href="auth/login.php" class="btn btn-outline-primary" title="Inicia sesión para comentar">
                    <i class="bi bi-chat-dots"></i>
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center">
          <div class="card shadow-custom">
            <div class="card-body py-5">
              <i class="bi bi-newspaper text-muted" style="font-size: 4rem;"></i>
              <h4 class="text-muted mt-3">No hay noticias disponibles</h4>
              <p class="text-muted">Pronto publicaremos nuevas noticias y eventos.</p>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
    
    <div class="text-center mt-4">
      <a href="noticias.php" class="btn btn-outline-primary btn-lg">
        <i class="bi bi-arrow-right me-2"></i>Ver Todas las Noticias
      </a>
    </div>
  </div>
</section>

<!-- Galería Fotográfica -->
<section class="section-padding" id="galeria">
  <div class="container">
    <div class="row justify-content-center mb-5">
      <div class="col-lg-8 text-center">
        <h2 class="display-5 fw-bold text-primary mb-3">
          <i class="bi bi-images me-2"></i>Galería Fotográfica
        </h2>
        <p class="lead text-muted">Conoce nuestras instalaciones y momentos especiales de la institución.</p>
      </div>
    </div>
    
    <div class="collage-container">
  <div class="collage-item">
    <img src="img/I.E.jpeg" alt="Fachada Colegio">
    </div>
  <div class="collage-item">
    <img src="img/I.E.2.jpeg" alt="Instalaciones">

  </div>
  <div class="collage-item">
    <img src="img/colegio-1.jpg" alt="Historia">
  </div>
</div>
    <div class="text-center mt-5">
      <a href="instalaciones.php" class="btn btn-outline-primary btn-lg">
        <i class="bi bi-building me-2"></i>Ver Más Instalaciones
      </a>
    </div>
  </div>
</section>

<!-- Contacto y Footer -->
<?php include 'includes/footer.php'; ?>

<!-- Botón flotante para volver arriba -->
<button type="button" id="btnScrollTop" class="btn btn-primary btn-lg rounded-circle shadow position-fixed" style="bottom: 30px; right: 30px; display: none; z-index: 1050;">
  <i class="bi bi-arrow-up"></i>
</button>

<!-- Modal para comentario rápido -->
<div class="modal fade" id="comentarioRapidoModal" tabindex="-1" aria-labelledby="comentarioRapidoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="comentarioRapidoModalLabel">
          <i class="bi bi-chat-dots me-2"></i>Comentar en noticia
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-bold text-primary">Noticia:</label>
          <p class="text-muted mb-0" id="noticiaTitulo"></p>
        </div>
        <form id="comentarioRapidoForm">
          <input type="hidden" id="noticiaId" name="noticia_id">
          <div class="mb-3">
            <label for="contenidoRapido" class="form-label">Tu comentario:</label>
            <textarea class="form-control" id="contenidoRapido" name="contenido" rows="4" placeholder="Escribe tu comentario aquí..." maxlength="1000" required></textarea>
            <div class="form-text">
              <span id="charCountRapido">0</span>/1000 caracteres
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i>Cancelar
        </button>
        <button type="button" class="btn btn-primary" id="enviarComentarioRapido">
          <i class="bi bi-send me-1"></i>Publicar Comentario
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/custom.js"></script>
<script>
// Variables globales para el modal
let comentarioRapidoModal;
let noticiaActualId = null;

document.addEventListener('DOMContentLoaded', function() {
    comentarioRapidoModal = new bootstrap.Modal(document.getElementById('comentarioRapidoModal'));
    
    // Contador de caracteres para comentario rápido
    const textareaRapido = document.getElementById('contenidoRapido');
    const charCountRapido = document.getElementById('charCountRapido');
    
    if (textareaRapido) {
        textareaRapido.addEventListener('input', function() {
            const length = this.value.length;
            charCountRapido.textContent = length;
            
            if (length > 900) {
                charCountRapido.classList.add('text-warning');
            } else {
                charCountRapido.classList.remove('text-warning');
            }
            
            if (length > 1000) {
                charCountRapido.classList.add('text-danger');
            } else {
                charCountRapido.classList.remove('text-danger');
            }
        });
    }
    
    // Manejo del envío de comentario rápido
    document.getElementById('enviarComentarioRapido').addEventListener('click', function() {
        enviarComentarioRapido();
    });
});

function abrirComentarioRapido(noticiaId, titulo) {
    noticiaActualId = noticiaId;
    document.getElementById('noticiaId').value = noticiaId;
    document.getElementById('noticiaTitulo').textContent = titulo;
    document.getElementById('contenidoRapido').value = '';
    document.getElementById('charCountRapido').textContent = '0';
    document.getElementById('charCountRapido').className = '';
    comentarioRapidoModal.show();
}

function enviarComentarioRapido() {
    const form = document.getElementById('comentarioRapidoForm');
    const formData = new FormData(form);
    const contenido = formData.get('contenido').trim();
    
    if (!contenido) {
        document.getElementById('contenidoRapido').classList.add('is-invalid');
        return;
    }
    
    document.getElementById('contenidoRapido').classList.remove('is-invalid');
    document.getElementById('enviarComentarioRapido').disabled = true;
    document.getElementById('enviarComentarioRapido').innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Publicando...';
    
    fetch('comentario_rapido.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar modal
            comentarioRapidoModal.hide();
            
            // Mostrar notificación de éxito
            mostrarNotificacion('Comentario publicado exitosamente', 'success');
            
            // Opcional: redirigir a la noticia para ver el comentario
            setTimeout(() => {
                window.location.href = `ver_noticia.php?id=${noticiaActualId}`;
            }, 1500);
        } else {
            throw new Error(data.error || 'Error al publicar comentario');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion(error.message || 'Error al publicar comentario', 'danger');
    })
    .finally(() => {
        document.getElementById('enviarComentarioRapido').disabled = false;
        document.getElementById('enviarComentarioRapido').innerHTML = '<i class="bi bi-send me-1"></i>Publicar Comentario';
    });
}

// Función para mostrar notificaciones
function showNotification(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Mostrar mensajes según los parámetros de la URL
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('login') && urlParams.get('login') === 'success') {
        showNotification('¡Bienvenido! Has iniciado sesión correctamente');
    }
    
    if (urlParams.has('logout') && urlParams.get('logout') === 'success') {
        showNotification('Has cerrado sesión correctamente', 'info');
    }
});
</script>
</body>
</html>
