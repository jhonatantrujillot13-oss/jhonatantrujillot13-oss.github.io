<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrarse | Promoción Social</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('img/colegio.jpeg') no-repeat center center fixed;
      background-size: cover;
      position: relative;
      margin: 0;
    }

    /* Overlay oscuro para hacer la imagen más visible */
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 100vw;
      background-color: rgba(0, 0, 0, 0.5); /* oscurece la imagen */
      z-index: 0;
    }

    main {
      position: relative;
      z-index: 1;
    }

    .card {
      background-color: rgba(255, 255, 255, 0.95); /* más opacidad pero no 100% */
    }
  </style>
</head>
<body class="d-flex flex-column min-vh-100">
  <?php include 'nav.php'; ?>

  <main class="flex-fill d-flex align-items-center justify-content-center">
    <div class="card shadow p-4 w-100" style="max-width: 500px;">
      <h4 class="text-center mb-4 text-primary">Crear una cuenta</h4>
      
      <?php if (isset($_SESSION['registro_error'])): ?>
        <div class="alert alert-danger">
          <?php 
            echo $_SESSION['registro_error'];
            unset($_SESSION['registro_error']); 
          ?>
        </div>
      <?php endif; ?>

      <form action="procesar_registro.php" method="POST" novalidate>
        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre completo</label>
          <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
          <label for="correo" class="form-label">Correo electrónico</label>
          <input type="email" class="form-control" id="correo" name="correo" required>
        </div>
        <div class="mb-3">
          <label for="contrasena" class="form-label">Contraseña</label>
          <input type="password" class="form-control" id="contrasena" name="contrasena" required minlength="6">
        </div>
        <button type="submit" class="btn btn-primary w-100">Registrarse</button>
      </form>

      <p class="text-center mt-3 small">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    </div>
  </main>

  <footer class="text-center text-light py-3 mt-auto small" style="z-index: 1;">
    &copy; <?php echo date("Y"); ?> Colegio Promoción Social
  </footer>
</body>
</html>
