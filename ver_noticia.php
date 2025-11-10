<?php
session_start();
include 'includes/conexion.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM noticias WHERE id = ?");
    $stmt->execute([$id]);
    $noticia = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$noticia) {
        header('Location: index.php');
        exit();
    }
    
    // Obtener comentarios de la noticia
    $stmt = $conn->prepare("
        SELECT c.id, c.contenido, c.fecha_comentario, u.nombre as nombre_usuario 
        FROM comentarios c 
        JOIN usuarios u ON c.usuario_id = u.id 
        WHERE c.noticia_id = ? AND c.estado = 'Aprobado' 
        ORDER BY c.fecha_comentario DESC
    ");
    $stmt->execute([$id]);
    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Contar total de comentarios
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM comentarios WHERE noticia_id = ? AND estado = 'Aprobado'");
    $stmt->execute([$id]);
    $total_comentarios = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
} catch (PDOException $e) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($noticia['titulo']) ?> - I.E. Promoción Social</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container" style="margin-top: 120px; max-width: 900px;">
    <div class="row justify-content-center">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="noticias.php">Noticias</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($noticia['titulo']) ?></li>
                </ol>
            </nav>
            
            <div class="card shadow-lg border-0 rounded-4">
                <?php if ($noticia['imagen']): ?>
                    <img src="img/<?= htmlspecialchars($noticia['imagen']) ?>" class="card-img-top news-image" alt="<?= htmlspecialchars($noticia['titulo']) ?>" onerror="this.src='img/escudo.jpeg'">
                <?php endif; ?>
                
                <div class="card-body p-5">
                    <div class="mb-4">
                        <h1 class="card-title text-primary fw-bold mb-3"><?= htmlspecialchars($noticia['titulo']) ?></h1>
                        <div class="d-flex align-items-center text-muted mb-4">
                            <i class="bi bi-calendar-event me-2"></i>
                            <span>Publicado el <?= date('d/m/Y H:i', strtotime($noticia['fecha_publicacion'])) ?></span>
                        </div>
                    </div>
                    
                    <div class="news-content">
                        <?= nl2br(htmlspecialchars($noticia['contenido'])) ?>
                    </div>
                    
                    <div class="mt-5 pt-4 border-top">
                        <a href="noticias.php" class="btn btn-outline-primary me-2">
                            <i class="bi bi-arrow-left me-1"></i>Volver a Noticias
                        </a>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-house me-1"></i>Ir al Inicio
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Sección de Comentarios -->
            <div class="card shadow-lg border-0 rounded-4 mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-dots me-2"></i>
                        Comentarios (<?= $total_comentarios ?>)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Formulario para agregar comentario -->
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-pencil-square me-1"></i>
                                Agregar un comentario
                            </h6>
                            <form id="comentarioForm" class="needs-validation" novalidate>
                                <input type="hidden" name="noticia_id" value="<?= $noticia['id'] ?>">
                                <div class="mb-3">
                                    <textarea 
                                        class="form-control" 
                                        name="contenido" 
                                        id="contenido" 
                                        rows="4" 
                                        placeholder="Escribe tu comentario aquí..." 
                                        maxlength="1000" 
                                        required
                                    ></textarea>
                                    <div class="form-text">
                                        <span id="charCount">0</span>/1000 caracteres
                                    </div>
                                    <div class="invalid-feedback">
                                        Por favor escribe un comentario.
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="bi bi-send me-1"></i>
                                    Publicar Comentario
                                </button>
                            </form>
                        </div>
                        <hr>
                    <?php else: ?>
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <a href="auth/login.php" class="alert-link">Inicia sesión</a> para poder comentar en esta noticia.
                        </div>
                    <?php endif; ?>
                    
                    <!-- Lista de comentarios -->
                    <div id="comentariosLista">
                        <?php if (empty($comentarios)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-chat-dots display-4"></i>
                                <p class="mt-2">No hay comentarios aún. ¡Sé el primero en comentar!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($comentarios as $comentario): ?>
                                <div class="comentario-item mb-3 p-3 border rounded-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <strong class="text-primary"><?= htmlspecialchars($comentario['nombre_usuario']) ?></strong>
                                            <small class="text-muted ms-2">
                                                <i class="bi bi-clock me-1"></i>
                                                <?= date('d/m/Y H:i', strtotime($comentario['fecha_comentario'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="comentario-contenido">
                                        <?= nl2br(htmlspecialchars($comentario['contenido'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('comentarioForm');
    const textarea = document.getElementById('contenido');
    const charCount = document.getElementById('charCount');
    const submitBtn = document.getElementById('submitBtn');
    const comentariosLista = document.getElementById('comentariosLista');
    
    // Contador de caracteres
    if (textarea) {
        textarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            if (length > 900) {
                charCount.classList.add('text-warning');
            } else {
                charCount.classList.remove('text-warning');
            }
            
            if (length > 1000) {
                charCount.classList.add('text-danger');
            } else {
                charCount.classList.remove('text-danger');
            }
        });
    }
    
    // Manejo del formulario
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const contenido = formData.get('contenido').trim();
            
            if (!contenido) {
                textarea.classList.add('is-invalid');
                return;
            }
            
            textarea.classList.remove('is-invalid');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Publicando...';
            
            fetch('agregar_comentario.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Limpiar formulario
                    form.reset();
                    charCount.textContent = '0';
                    
                    // Agregar nuevo comentario a la lista
                    const nuevoComentario = crearComentarioHTML(data.comentario);
                    
                    // Si no hay comentarios, reemplazar el mensaje
                    if (comentariosLista.querySelector('.text-muted')) {
                        comentariosLista.innerHTML = '';
                    }
                    
                    // Insertar al principio
                    comentariosLista.insertBefore(nuevoComentario, comentariosLista.firstChild);
                    
                    // Actualizar contador
                    const header = document.querySelector('.card-header h5');
                    const currentCount = parseInt(header.textContent.match(/\((\d+)\)/)[1]);
                    header.innerHTML = `<i class="bi bi-chat-dots me-2"></i>Comentarios (${currentCount + 1})`;
                    
                    // Mostrar mensaje de éxito
                    mostrarNotificacion('Comentario publicado exitosamente', 'success');
                } else {
                    throw new Error(data.error || 'Error al publicar comentario');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion(error.message || 'Error al publicar comentario', 'danger');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-send me-1"></i>Publicar Comentario';
            });
        });
    }
    
    function crearComentarioHTML(comentario) {
        const div = document.createElement('div');
        div.className = 'comentario-item mb-3 p-3 border rounded-3 bg-light';
        div.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <strong class="text-primary">${comentario.nombre_usuario}</strong>
                    <small class="text-muted ms-2">
                        <i class="bi bi-clock me-1"></i>
                        ${new Date(comentario.fecha_comentario).toLocaleString('es-ES')}
                    </small>
                </div>
            </div>
            <div class="comentario-contenido">
                ${comentario.contenido.replace(/\n/g, '<br>')}
            </div>
        `;
        return div;
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
});
</script>
</body>
</html>
