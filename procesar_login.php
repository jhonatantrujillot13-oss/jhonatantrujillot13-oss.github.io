<?php
include 'conexion.php';
session_start();

$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];

$sql = "SELECT id, nombre, contrasena FROM usuarios WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $usuario = $result->fetch_assoc();
  if (password_verify($contrasena, $usuario['contrasena'])) {
    $_SESSION['usuario'] = $usuario['nombre'];
    header("Location: index.php");
    exit;
  } else {
    header("Location: login.php?error=credenciales");
  }
} else {
  header("Location: login.php?error=credenciales");
}

$stmt->close();
$conn->close();
?>
