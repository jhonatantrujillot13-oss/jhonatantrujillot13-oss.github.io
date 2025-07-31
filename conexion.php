<?php
$host = 'localhost';
$usuario = 'root';
$contrasena = ''; // o la que uses
$bd = 'colegio';

$conn = new mysqli($host, $usuario, $contrasena, $bd);
if ($conn->connect_error) {
  die("Error de conexión: " . $conn->connect_error);
}
?>
