<?php
include 'conexion.php';

$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nombre, $correo, $contrasena);

if ($stmt->execute()) {
  header("Location: login.php?registro=exitoso");
} else {
  echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
