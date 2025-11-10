<!-- Navbar principal -->
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="img/escudo.jpeg" alt="Logo" style="height: 80px;">
      <span class="ms-3 fw-bold fs-5" style="font-family: 'Segoe UI', sans-serif;">INSTITUCIÓN EDUCATIVA PROMOCIÓN SOCIAL</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="index.php" style="color: #333; font-weight: 500;">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="noticias.php" style="color: #333; font-weight: 500;">Noticias</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="sedes.php" style="color: #333; font-weight: 500;">Sedes</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="informacion.php" style="color: #333; font-weight: 500;">Información</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php#tecnicos" style="color: #333; font-weight: 500;">Técnicos</a>
        </li>
      </ul>

      <div class="d-flex align-items-center ms-3">
        <?php if(isset($_SESSION['usuario_id'])): ?>
          <!-- Usuario logueado -->
          <div class="dropdown">
            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle me-1"></i>
              <?php echo htmlspecialchars($_SESSION['nombre']); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <?php if($_SESSION['tipo_usuario'] == 'Admin'): ?>
                <li><a class="dropdown-item" href="admin.php"><i class="bi bi-gear me-2"></i>Panel Admin</a></li>
              <?php else: ?>
                <li><a class="dropdown-item" href="perfil.php"><i class="bi bi-person me-2"></i>Mi Perfil</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
            </ul>
          </div>
        <?php else: ?>
          <!-- Usuario no logueado -->
          <a href="auth/login.php" class="btn btn-sm btn-outline-primary me-2">
            <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión
          </a>
          <a href="auth/registro.php" class="btn btn-sm btn-primary">
            <i class="bi bi-person-plus me-1"></i>Registrarse
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
