<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=institucion_PromSocial", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>