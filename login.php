<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión | Promoción Social</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Fondo y estilos personalizados -->
  <style>
    body {
      background-image: url('img/colegio.jpeg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      min-height: 100vh;
    }
     .card {
      background-color: rgba(255, 255, 255, 0.95); /* más opacidad pero no 100% */
    }
  </style>
</head>
<body>

  <?php include 'nav.php'; ?>

  <div class="container d-flex justify-content-center align-items-center" style="min-height: 85vh;">
    <div class="card shadow-lg p-4 rounded-4 bg-white bg-opacity-75" style="max-width: 420px; width: 100%;">
      <div class="text-center mb-4">
        <h3 class="text-primary fw-bold">Iniciar Sesión</h3>
        <p class="text-muted small mb-0">Accede a tu cuenta para continuar</p>
      </div>
      <form action="procesar_login.php" method="POST">
        <div class="mb-3">
          <label for="correo" class="form-label">Correo electrónico</label>
          <input type="email" class="form-control" id="correo" name="correo" placeholder="nombre@correo.com" required>
        </div>
        <div class="mb-3">
          <label for="contrasena" class="form-label">Contraseña</label>
          <input type="password" class="form-control" id="contrasena" name="contrasena" placeholder="********" required>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Entrar</button>
        </div>
      </form>
      <div class="text-center mt-3">
        <p class="small">¿No tienes cuenta? <a href="registrar.php" class="text-decoration-none text-primary">Regístrate aquí</a></p>
      </div>
    </div>
  </div>

  <footer class="text-center text-muted py-3 small bg-white bg-opacity-75 mt-4">
    &copy; <?php echo date('Y'); ?> Colegio Promoción Social. Todos los derechos reservados.
  </footer>

</body>
</html>
