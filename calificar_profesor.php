<?php
session_start();
include 'includes/conexion.php';

// Verificar que sea una petición POST y que el usuario esté autenticado
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['usuario_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$profesor_id = filter_input(INPUT_POST, 'profesor_id', FILTER_VALIDATE_INT);
$calificacion = filter_input(INPUT_POST, 'calificacion', FILTER_VALIDATE_FLOAT);
$usuario_id = $_SESSION['usuario_id'];

if (!$profesor_id || $calificacion < 1 || $calificacion > 5) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

try {
    // Verificar si el usuario ya calificó a este profesor
    $stmt = $conn->prepare("SELECT id FROM calificaciones_profesores WHERE usuario_id = ? AND profesor_id = ?");
    $stmt->execute([$usuario_id, $profesor_id]);
    $existente = $stmt->fetch();

    if ($existente) {
        // Actualizar calificación existente
        $stmt = $conn->prepare("UPDATE calificaciones_profesores SET calificacion = ?, fecha_actualizacion = NOW() WHERE usuario_id = ? AND profesor_id = ?");
        $stmt->execute([$calificacion, $usuario_id, $profesor_id]);
    } else {
        // Insertar nueva calificación
        $stmt = $conn->prepare("INSERT INTO calificaciones_profesores (profesor_id, usuario_id, calificacion, fecha_creacion) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$profesor_id, $usuario_id, $calificacion]);
    }

    // Obtener el promedio actualizado
    $stmt = $conn->prepare("SELECT AVG(calificacion) as promedio FROM calificaciones_profesores WHERE profesor_id = ?");
    $stmt->execute([$profesor_id]);
    $resultado = $stmt->fetch();
    $promedio = round($resultado['promedio'] * 2) / 2; // Redondear a 0.5 más cercano

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'promedio' => $promedio,
        'mensaje' => '¡Gracias por tu calificación!'
    ]);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error al procesar la calificación']);
}
?>