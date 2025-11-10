
<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'Admin') {
    header('Location: auth/login.php');
    exit();
}
include 'includes/conexion.php';

// CRUD para secciones de información
if (isset($_POST['add_info_titulo'], $_POST['add_info_slug'], $_POST['add_info_contenido'])) {
    try {
        $stmt = $conn->prepare("INSERT INTO informacion (titulo, slug, contenido) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['add_info_titulo'],
            $_POST['add_info_slug'],
            $_POST['add_info_contenido']
        ]);
        header('Location: admin.php#informacion');
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Error al agregar la sección.');</script>";
    }
}

if (isset($_POST['edit_info_id'], $_POST['edit_info_titulo'], $_POST['edit_info_slug'], $_POST['edit_info_contenido'])) {
    try {
        $stmt = $conn->prepare("UPDATE informacion SET titulo = ?, slug = ?, contenido = ? WHERE id = ?");
        $stmt->execute([
            $_POST['edit_info_titulo'],
            $_POST['edit_info_slug'],
            $_POST['edit_info_contenido'],
            $_POST['edit_info_id']
        ]);
        header('Location: admin.php#informacion');
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Error al actualizar la sección.');</script>";
    }
}

if (isset($_GET['delete_info'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM informacion WHERE id = ?");
        $stmt->execute([$_GET['delete_info']]);
        header('Location: admin.php#informacion');
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Error al eliminar la sección.');</script>";
    }
}

// Handle delete news request
if (isset($_GET['delete'])) {
    try {
    $stmt = $conn->prepare("DELETE FROM noticias WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: admin.php');
    exit();
    } catch (PDOException $e) {
        echo "<script>alert('Error al eliminar la noticia.');</script>";
    }
}

// Handle comment status update
if (isset($_GET['comment_action']) && isset($_GET['comment_id'])) {
    $action = $_GET['comment_action'];
    $comment_id = $_GET['comment_id'];
    
    if (in_array($action, ['approve', 'reject', 'delete'])) {
        try {
            switch ($action) {
                case 'approve':
                    $stmt = $conn->prepare("UPDATE comentarios SET estado = 'Aprobado' WHERE id = ?");
                    break;
                case 'reject':
                    $stmt = $conn->prepare("UPDATE comentarios SET estado = 'Rechazado' WHERE id = ?");
                    break;
                case 'delete':
                    $stmt = $conn->prepare("DELETE FROM comentarios WHERE id = ?");
                    break;
            }
            $stmt->execute([$comment_id]);
            header('Location: admin.php');
            exit();
        } catch (PDOException $e) {
            echo "<script>alert('Error al procesar el comentario.');</script>";
        }
    }
}

// Fetch users
try {
    $stmt = $conn->query("SELECT id, nombre, correo, tipo_usuario, fecha_registro FROM usuarios");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $usuarios = [];
}

// Fetch news
try {
    $stmt = $conn->query("SELECT id, titulo, contenido, imagen, fecha_publicacion FROM noticias ORDER BY fecha_publicacion DESC");
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $noticias = [];
}

// Fetch comments with user and news information
try {
    $stmt = $conn->query("
        SELECT c.id, c.contenido, c.fecha_comentario, c.estado,
               u.nombre as nombre_usuario, u.correo as correo_usuario,
               n.titulo as titulo_noticia, n.id as noticia_id
        FROM comentarios c 
        JOIN usuarios u ON c.usuario_id = u.id 
        JOIN noticias n ON c.noticia_id = n.id 
        ORDER BY c.fecha_comentario DESC
    ");
    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $comentarios = [];
}

// Count comments by status
$comentarios_pendientes = 0;
$comentarios_aprobados = 0;
$comentarios_rechazados = 0;

foreach ($comentarios as $comentario) {
    switch ($comentario['estado']) {
        case 'Pendiente':
            $comentarios_pendientes++;
            break;
        case 'Aprobado':
            $comentarios_aprobados++;
            break;
        case 'Rechazado':
            $comentarios_rechazados++;
            break;
    }
}

// Fetch personal
try {
    $stmt = $conn->query("SELECT p.id, p.nombre, p.rol, p.telefono, p.horario, p.foto, s.nombre as sede_nombre FROM personal p LEFT JOIN sedes s ON p.sede_id = s.id ORDER BY p.nombre ASC LIMIT 20");
    $personal_panel = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $personal_panel = [];
}

// Fetch secciones de información
try {
    $stmt = $conn->query("SELECT id, slug, titulo, contenido FROM informacion ORDER BY id ASC");
    $info_secciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $info_secciones = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - I.E. Promoción Social</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0"><i class="bi bi-gear-fill me-2"></i>Panel Administrativo</h1>
                    <p class="mb-0">Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?></p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="index.php" class="btn btn-outline-light me-2">
                        <i class="bi bi-house me-1"></i>Inicio
                    </a>
                    <a href="auth/logout.php" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right me-1"></i>Cerrar sesión
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Dashboard Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <a href="#usuarios" class="text-decoration-none">
                    <div class="card bg-primary text-white hover-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0"><?= count($usuarios) ?></h4>
                                    <p class="mb-0">Usuarios</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-people-fill fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="#noticias" class="text-decoration-none">
                    <div class="card bg-success text-white hover-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0"><?= count($noticias) ?></h4>
                                    <p class="mb-0">Noticias</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-newspaper fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="#comentarios" class="text-decoration-none">
                    <div class="card bg-warning text-white hover-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0"><?= $comentarios_pendientes ?></h4>
                                    <p class="mb-0">Comentarios Pendientes</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-chat-dots fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="#comentarios" class="text-decoration-none">
                    <div class="card bg-info text-white hover-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0"><?= count($comentarios) ?></h4>
                                    <p class="mb-0">Total Comentarios</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-chat-square-text fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="#personal" class="text-decoration-none">
                    <div class="card bg-secondary text-white hover-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0"><?= count($personal_panel) ?></h4>
                                    <p class="mb-0">Personal registrado</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-person-badge fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Personal Section -->
        <div id="personal" class="row mb-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-primary mb-0"><i class="bi bi-person-badge me-2"></i>Personal</h2>
                    <div>
                        <a href="crear_personal.php" class="btn btn-success me-2"><i class="bi bi-plus-circle me-1"></i>Agregar Personal</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Rol</th>
                                <th>Sede</th>
                                <th>Teléfono</th>
                                <th>Horario</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($personal_panel)): ?>
                            <?php foreach ($personal_panel as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['id']) ?></td>
                                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                                    <td><?= htmlspecialchars($p['rol']) ?></td>
                                    <td><?= htmlspecialchars($p['sede_nombre'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($p['telefono']) ?></td>
                                    <td><?= htmlspecialchars($p['horario']) ?></td>
                                    <td>
                                        <a href="editar_personal.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm me-1">Editar</a>
                                        <a href="admin_personal.php?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar personal?')">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center">No hay personal registrado</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div id="comentarios" class="row mb-5">
            <div class="col-12">
                <h2 class="text-primary mb-4">
                    <i class="bi bi-chat-dots me-2"></i>Gestión de Comentarios
                </h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Noticia</th>
                                <th>Comentario</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($comentarios)): ?>
                                <?php foreach ($comentarios as $c): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($c['id']) ?></td>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($c['nombre_usuario']) ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($c['correo_usuario']) ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="ver_noticia.php?id=<?= $c['noticia_id'] ?>" target="_blank" class="text-decoration-none">
                                                <?= htmlspecialchars($c['titulo_noticia']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($c['contenido']) ?>">
                                                <?= htmlspecialchars($c['contenido']) ?>
                                            </div>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($c['fecha_comentario'])) ?></td>
                                        <td>
                                            <?php
                                            $badge_class = '';
                                            switch ($c['estado']) {
                                                case 'Aprobado':
                                                    $badge_class = 'bg-success';
                                                    break;
                                                case 'Pendiente':
                                                    $badge_class = 'bg-warning';
                                                    break;
                                                case 'Rechazado':
                                                    $badge_class = 'bg-danger';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?= $badge_class ?>">
                                                <?= htmlspecialchars($c['estado']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($c['estado'] == 'Pendiente'): ?>
                                                <a href="admin.php?comment_action=approve&comment_id=<?= $c['id'] ?>" class="btn btn-success btn-sm me-1" title="Aprobar">
                                                    <i class="bi bi-check-circle"></i>
                                                </a>
                                                <a href="admin.php?comment_action=reject&comment_id=<?= $c['id'] ?>" class="btn btn-warning btn-sm me-1" title="Rechazar">
                                                    <i class="bi bi-x-circle"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="admin.php?comment_action=delete&comment_id=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este comentario?')" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No hay comentarios</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Users Section -->
        <div id="usuarios" class="row mb-5">
            <div class="col-12">
                <h2 class="text-primary mb-4"><i class="bi bi-people-fill me-2"></i>Usuarios Registrados</h2>
                <div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                                <th>Correo</th>
                <th>Tipo de Usuario</th>
                <th>Fecha de Registro</th>
            </tr>
        </thead>
        <tbody>
                            <?php if (!empty($usuarios)): ?>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                                        <td><?= htmlspecialchars($u['id']) ?></td>
                                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                                        <td><?= htmlspecialchars($u['correo']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $u['tipo_usuario'] == 'Admin' ? 'danger' : 'primary' ?>">
                                                <?= htmlspecialchars($u['tipo_usuario']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($u['fecha_registro']) ?></td>
                </tr>
            <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay usuarios registrados</td>
                                </tr>
                            <?php endif; ?>
        </tbody>
    </table>
                </div>
            </div>
        </div>

        <!-- Sección de Información -->
        <div id="informacion" class="row mb-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-primary mb-0">
                        <i class="bi bi-info-circle-fill me-2"></i>Gestión de Información
                    </h2>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregarInfo">
                        <i class="bi bi-plus-circle me-1"></i>Agregar Sección
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Slug</th>
                                <th>Contenido</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($info_secciones)): ?>
                                <?php foreach ($info_secciones as $info): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($info['id']) ?></td>
                                        <td><?= htmlspecialchars($info['titulo']) ?></td>
                                        <td><?= htmlspecialchars($info['slug']) ?></td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 300px;">
                                                <?= htmlspecialchars(substr($info['contenido'], 0, 100)) ?>...
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm me-1" data-bs-toggle="modal" 
                                                    data-bs-target="#modalEditarInfo<?= $info['id'] ?>">
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="if(confirm('¿Estás seguro de eliminar esta sección?')) 
                                                window.location.href='admin.php?delete_info=<?= $info['id'] ?>'">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal Editar -->
                                    <div class="modal fade" id="modalEditarInfo<?= $info['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning text-dark">
                                                    <h5 class="modal-title">
                                                        <i class="bi bi-pencil-square me-2"></i>Editar Sección
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="post" action="admin.php">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="edit_info_id" value="<?= $info['id'] ?>">
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Título</label>
                                                            <input type="text" class="form-control" name="edit_info_titulo" 
                                                                   value="<?= htmlspecialchars($info['titulo']) ?>" required>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Slug</label>
                                                            <input type="text" class="form-control" name="edit_info_slug" 
                                                                   value="<?= htmlspecialchars($info['slug']) ?>" required>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Contenido</label>
                                                            <textarea class="form-control" name="edit_info_contenido" 
                                                                      rows="10" required><?= htmlspecialchars($info['contenido']) ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-warning">
                                                            <i class="bi bi-save me-1"></i>Guardar Cambios
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        No hay secciones de información registradas
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Agregar Sección -->
        <div class="modal fade" id="modalAgregarInfo" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-plus-circle me-2"></i>Agregar Nueva Sección
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="post" action="admin.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Título</label>
                                <input type="text" class="form-control" name="add_info_titulo" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Slug</label>
                                <input type="text" class="form-control" name="add_info_slug" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Contenido</label>
                                <textarea class="form-control" name="add_info_contenido" rows="10" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus-circle me-1"></i>Agregar Sección
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- News Section -->
        <div id="noticias" class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-primary mb-0"><i class="bi bi-newspaper me-2"></i>Noticias</h2>
                    <div>
                        <a href="crear_noticia.php" class="btn btn-primary me-2">
                        <i class="bi bi-plus-circle me-1"></i>Publicar Nueva Noticia
                    </a>
                        <a href="admin_manuales.php" class="btn btn-secondary">
                            <i class="bi bi-file-earmark-text me-1"></i>Gestionar Manuales
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Contenido</th>
                <th>Imagen</th>
                <th>Fecha de Publicación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
                            <?php if (!empty($noticias)): ?>
            <?php foreach ($noticias as $n): ?>
                <tr>
                                        <td><?= htmlspecialchars($n['id']) ?></td>
                                        <td><?= htmlspecialchars($n['titulo']) ?></td>
                                        <td><?= htmlspecialchars(substr($n['contenido'], 0, 50)) ?>...</td>
                                        <td>
                                            <?php if ($n['imagen']): ?>
                                                <img src="img/<?= htmlspecialchars($n['imagen']) ?>" alt="Imagen" class="noticia-img-zoom" style="height: 50px; width: 50px; object-fit: cover;" onerror="this.src='img/escudo.jpeg'">
                                            <?php else: ?>
                                                <span class="text-muted">Sin imagen</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($n['fecha_publicacion']) ?></td>
                                        <td>
                                            <a href="editar_noticia.php?id=<?= $n['id'] ?>" class="btn btn-warning btn-sm me-1">
                                                <i class="bi bi-pencil me-1"></i>Editar
                                            </a>
                                            <a href="admin.php?delete=<?= $n['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar esta noticia?')">
                                                <i class="bi bi-trash me-1"></i>Eliminar
                                            </a>
                    </td>
                </tr>
            <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay noticias publicadas</td>
                                </tr>
                            <?php endif; ?>
        </tbody>
    </table>
</div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>