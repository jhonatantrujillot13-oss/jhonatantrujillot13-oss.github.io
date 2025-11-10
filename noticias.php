<?php
session_start();
include 'includes/conexion.php';

// Obtener categorías para el filtro
$categorias = [];
try {
    $catStmt = $conn->prepare("SELECT id, nombre FROM categorias_noticias ORDER BY nombre ASC");
    $catStmt->execute();
    $categorias = $catStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

// Filtrar por categoría si se selecciona
$categoria_id = isset($_GET['categoria_id']) ? intval($_GET['categoria_id']) : 0;
try {
    $sql = "SELECT n.id, n.titulo, n.contenido, n.imagen, n.fecha_publicacion, c.nombre AS categoria FROM noticias n LEFT JOIN categorias_noticias c ON n.categoria_id = c.id";
    $params = [];
    if ($categoria_id > 0) {
        $sql .= " WHERE n.categoria_id = ?";
        $params[] = $categoria_id;
    }
    $sql .= " ORDER BY n.fecha_publicacion DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $noticias = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias - I.E. Promoción Social</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="../css/noticias.css" rel="stylesheet">
<style>
  body {
            background: #ffffff;
    min-height: 100vh;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            padding: 80px 0 60px;
            margin-bottom: 60px;
    position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('img/I.E.2.jpeg') center center/cover no-repeat;
            opacity: 0.1;
            z-index: 1;
        }
        
        .hero-content {
    position: relative;
    z-index: 2;
  }
        
        .search-container {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 50px;
        }
        
        .search-form {
            background: white;
            border-radius: 50px;
            padding: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 2px solid #667eea;
            transition: all 0.3s ease;
        }
        
        .search-form:focus-within {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
        }
        
        .search-input {
            border: none;
            border-radius: 25px;
            padding: 12px 20px;
            font-size: 16px;
            outline: none;
            width: 100%;
        }
        
        .search-btn {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(116, 185, 255, 0.4);
        }
        
        .news-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease;
            margin-bottom: 30px;
            border: none;
            position: relative;
        }
        
        .news-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        .news-image {
            height: 280px;
            object-fit: cover;
            transition: all 0.4s ease;
        }
        
        .news-card:hover .news-image {
            transform: scale(1.05);
        }
        
        .news-content {
            padding: 30px;
        }
        
        .news-title {
            color: #0984e3;
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 15px;
            position: relative;
            line-height: 1.3;
        }
        
        .news-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            border-radius: 2px;
        }
        
        .news-excerpt {
            color: #666;
            line-height: 1.6;
            font-size: 1rem;
            margin-bottom: 20px;
        }
        
        .news-meta {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        
        .news-date {
            color: #0984e3;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .news-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .btn-read-more {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            flex: 1;
        }
        
        .btn-read-more:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(116, 185, 255, 0.4);
            color: white;
        }
        
        .btn-comment {
            background: white;
            border: 2px solid #0984e3;
            border-radius: 25px;
            padding: 12px 15px;
            color: #0984e3;
            font-weight: 600;
            transition: all 0.3s ease;
            min-width: 50px;
        }
        
        .btn-comment:hover {
            background: #0984e3;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(116, 185, 255, 0.3);
        }
        
        .empty-state {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .empty-icon {
            font-size: 5rem;
            color: #0984e3;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 60px 0 40px;
            }
            
            .search-container {
                padding: 20px;
                margin-bottom: 30px;
            }
            
            .news-content {
                padding: 20px;
            }
            
            .news-actions {
                flex-direction: column;
            }
            
            .btn-read-more, .btn-comment {
                width: 100%;
            }
        }
</style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="hero-section">
    <div class="container">
        <div class="hero-content text-center">
            <h1 class="display-4 text-white fw-bold mb-3">
                <i class="bi bi-newspaper me-3"></i>Noticias y Eventos
            </h1>
            <p class="lead text-white-50 mb-0">
                Mantente informado sobre las últimas noticias y eventos de nuestra institución
            </p>
        </div>
    </div>
  </div>
 

<div class="container">
        <!-- Filtro visual mejorado -->
        <form method="get" id="filterForm" class="mb-4">
            <div class="filter-bar">
                <div class="filter-icon"><i class="bi bi-funnel-fill"></i></div>
                <div class="flex-grow-1">
                    <label class="form-label mb-0 small text-muted">Filtrar noticias</label>
                    <select name="categoria_id" id="categoria_id" class="form-select">
                            <option value="0">Todas las categorías</option>
                            <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $categoria_id == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['nombre']) ?></option>
                            <?php endforeach; ?>
                    </select>
                </div>
                <div class="d-flex align-items-center">
                    <button type="submit" class="btn btn-primary me-2">Aplicar</button>
                    <a href="noticias.php" id="clearFilter" class="btn btn-outline-secondary btn-clear">Limpiar</a>
                </div>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function(){
                const sel = document.getElementById('categoria_id');
                const clear = document.getElementById('clearFilter');
                if (sel) {
                    sel.addEventListener('change', function(){
                        // Auto submit al cambiar para mejorar UX
                        document.getElementById('filterForm').submit();
                    });
                }
                // Mostrar/ocultar botón limpiar según selección
                function toggleClear(){
                    if (!sel) return;
                    if (sel.value && sel.value !== '0') clear.style.display = 'inline-block'; else clear.style.display = 'none';
                }
                toggleClear();
                if (sel) sel.addEventListener('change', toggleClear);
            });
        </script>

 <script>
  document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('searchInput');
    const noticias = document.querySelectorAll('.noticia');

    input.addEventListener('input', () => {
      const query = input.value.trim().toLowerCase();
      let resultados = 0;

      noticias.forEach(noticia => {
        const titulo = noticia.dataset.title?.toLowerCase() || '';
        const fecha = noticia.dataset.date?.toLowerCase() || '';

        if (titulo.includes(query) || fecha.includes(query)) {
          noticia.style.display = 'block';
          noticia.style.opacity = '1';
          resultados++;
        } else {
          noticia.style.opacity = '0';
          setTimeout(() => noticia.style.display = 'none', 300);
        }
      });

      mostrarMensaje(resultados);
    });

    function mostrarMensaje(resultados) {
      let mensaje = document.getElementById('mensajeBusqueda');
      if (!mensaje) {
        mensaje = document.createElement('div');
        mensaje.id = 'mensajeBusqueda';
        mensaje.className = 'text-center mt-3 text-muted';
        input.parentElement.appendChild(mensaje);
      }

      mensaje.textContent = resultados > 0
        ? `🔍 ${resultados} resultado(s) encontrados.`
        : '⚠️ No se encontraron coincidencias.';
    }
  });
</script>


    <!-- Lista de noticias -->
    <div class="row">
        <?php if (!empty($noticias)): ?>
            <?php foreach ($noticias as $noticia): ?>
                <div class="col-lg-6">
                    <div class="news-card">
                        <img src="img/<?= htmlspecialchars($noticia['imagen']) ?>" class="news-image w-100 noticia-img-zoom" alt="<?= htmlspecialchars($noticia['titulo']) ?>" onerror="this.src='img/escudo.jpeg'">
                        <div class="news-content">
                            <h3 class="news-title"><?= htmlspecialchars($noticia['titulo']) ?></h3>
                            <p class="news-excerpt"><?= htmlspecialchars(substr($noticia['contenido'], 0, 150)) ?>...</p>
                            <div class="news-meta">
                                <div class="news-date mb-1">
                                    <i class="bi bi-calendar-event me-2"></i>
                                    <?= date('d/m/Y', strtotime($noticia['fecha_publicacion'])) ?>
                                </div>
                                <?php if (!empty($noticia['categoria'])): ?>
                                <div class="badge bg-info text-dark"><i class="bi bi-tag"></i> <?= htmlspecialchars($noticia['categoria']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="news-actions">
                                <a href="ver_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-read-more">
                                    <i class="bi bi-eye me-2"></i>Leer más
                                </a>
                                <?php if (isset($_SESSION['usuario_id'])): ?>
                                    <button class="btn btn-comment" onclick="abrirComentarioRapido(<?= $noticia['id'] ?>, '<?= htmlspecialchars($noticia['titulo']) ?>')" title="Comentar">
                                        <i class="bi bi-chat-dots"></i>
                                    </button>
                                <?php else: ?>
                                    <a href="auth/login.php" class="btn btn-comment" title="Inicia sesión para comentar">
                                        <i class="bi bi-chat-dots"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="bi bi-newspaper"></i>
                    </div>
                    <h3 class="text-primary mb-3">No hay noticias disponibles</h3>
                    <p class="text-muted mb-0">Pronto publicaremos nuevas noticias y eventos para mantenerte informado.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

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

function mostrarNotificacion(mensaje, tipo) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

</script>
</body>
</html>
<?php include("includes/footer.php"); ?>
