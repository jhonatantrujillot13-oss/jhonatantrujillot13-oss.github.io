<?php
session_start();
include 'includes/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Debes iniciar sesión para comentar']);
    exit();
}

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}

// Obtener datos del formulario
$noticia_id = isset($_POST['noticia_id']) ? (int)$_POST['noticia_id'] : 0;
$contenido = isset($_POST['contenido']) ? trim($_POST['contenido']) : '';
$usuario_id = $_SESSION['usuario_id'];

// Validar datos
if ($noticia_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de noticia inválido']);
    exit();
}

if (empty($contenido)) {
    http_response_code(400);
    echo json_encode(['error' => 'El contenido del comentario no puede estar vacío']);
    exit();
}

if (strlen($contenido) > 1000) {
    http_response_code(400);
    echo json_encode(['error' => 'El comentario no puede exceder los 1000 caracteres']);
    exit();
}

try {
    // Verificar que la noticia existe
    $stmt = $conn->prepare("SELECT id FROM noticias WHERE id = ?");
    $stmt->execute([$noticia_id]);
    
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => 'Noticia no encontrada']);
        exit();
    }
    
    // Verificar que el usuario existe
    $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        http_response_code(404);
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit();
    }
    
    // Insertar el comentario
    $stmt = $conn->prepare("INSERT INTO comentarios (noticia_id, usuario_id, contenido) VALUES (?, ?, ?)");
    $stmt->execute([$noticia_id, $usuario_id, $contenido]);
    
    $comentario_id = $conn->lastInsertId();
    
    // Obtener el comentario recién insertado con información del usuario
    $stmt = $conn->prepare("
        SELECT c.id, c.contenido, c.fecha_comentario, u.nombre as nombre_usuario 
        FROM comentarios c 
        JOIN usuarios u ON c.usuario_id = u.id 
        WHERE c.id = ?
    ");
    $stmt->execute([$comentario_id]);
    $comentario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Devolver respuesta exitosa
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Comentario agregado exitosamente',
        'comentario' => [
            'id' => $comentario['id'],
            'contenido' => htmlspecialchars($comentario['contenido']),
            'fecha_comentario' => $comentario['fecha_comentario'],
            'nombre_usuario' => htmlspecialchars($comentario['nombre_usuario'])
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor']);
}
?>
