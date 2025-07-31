<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="index.php">Promoción Social</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav me-3">
        <li class="nav-item"><a class="nav-link" href="/promocion/#nosotros">Nosotros</a></li>
        <li class="nav-item"><a class="nav-link" href="/promocion/#oferta">Oferta Académica</a></li>
        <li class="nav-item"><a class="nav-link" href="/promocion/#actividades">Actividades</a></li>
        <li class="nav-item"><a class="nav-link" href="/promocion/#contacto">Contacto</a></li>
      </ul>

      <div class="d-flex">
        <?php if (isset($_SESSION['usuario'])): ?>
          <span class="me-3 fw-bold text-primary">Hola, <?php echo $_SESSION['usuario']; ?></span>
          <a href="logout.php" class="btn btn-outline-danger">Cerrar sesión</a>
        <?php else: ?>
          <a href="login.php" class="btn btn-outline-primary me-2">Iniciar Sesión</a>
          <a href="registrar.php" class="btn btn-primary">Registrarse</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
