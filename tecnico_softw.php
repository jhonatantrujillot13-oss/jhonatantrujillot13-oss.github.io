

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Técnico en Programación de Software</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body {
      background-color: #f4f6f9;
    }
    .hero {
      background: linear-gradient(to right, #0d6efd, #6610f2);
      color: white;
      padding: 60px 20px;
      border-radius: 0 0 30px 30px;
    }
    .card-img-top {
      height: 250px;
      object-fit: cover;
    }
  </style>
</head>
<body>

  <?php include("includes/header.php"); ?>

  <!-- Hero Section -->
  <section class="hero text-center animate__animated animate__fadeInDown">
    <div class="container">
      <h1 class="display-5 fw-bold">Técnico en Programación de Software</h1>
      <p class="lead">Prepárate para el futuro digital con una formación práctica y moderna en desarrollo de software.</p>
      <a href="#info" class="btn btn-light btn-lg mt-3"><i class="bi bi-arrow-down-circle"></i> Más información</a>
    </div>
  </section>

  <!-- Info Section -->
  <section id="info" class="container my-5">
    <div class="row g-4">
      <div class="col-md-6 animate__animated animate__fadeInLeft">
        <img src="img/software.jpg" alt="Estudiantes programando" class="img-fluid rounded shadow">
      </div>
      <div class="col-md-6 animate__animated animate__fadeInRight">
        <h2 class="fw-bold text-primary"><i class="bi bi-laptop"></i> ¿Qué aprenderás?</h2>
        <p>Este programa técnico, en convenio con el <strong>SENA</strong>, te brinda conocimientos en:</p>
        <ul class="list-group list-group-flush">
          <li class="list-group-item">✅ Lógica de programación</li>
          <li class="list-group-item">✅ Desarrollo de aplicaciones web y móviles</li>
          <li class="list-group-item">✅ Bases de datos y SQL</li>
          <li class="list-group-item">✅ Herramientas modernas como Git, VS Code y más</li>
        </ul>
      </div>
        <div class="col-md-6 animate__animated animate__fadeInRight">
        <h2 class="fw-bold text-primary"><i class="bi bi-laptop"></i> ¿cómo y cuanto tiempo dura?</h2>
        <p>Este programa técnico, en convenio con el <strong>SENA</strong>, tiene una duración de 2 años y se ofrece de manera presencial durante el año escolar de decimo y once, para obtener tu titulo apenas salgas del colegio. Además de contar con un beneficio por parte del SENA ya sea para homologar algunas áreas de tu carrera (dependiendo la carrera y la universidad) o para acceder de manera más fácil al tecnólogo en el SENA.</p>
      </div>
    </div>
  </section>

  <!-- Benefits Section -->
  <section class="bg-white py-5">
    <div class="container text-center">
      <h2 class="fw-bold text-success mb-4"><i class="bi bi-award"></i> Beneficios del programa</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card shadow h-100">
            <div class="card-body">
              <i class="bi bi-briefcase-fill text-primary fs-2"></i>
              <h5 class="card-title mt-3">Doble titulación</h5>
              <p class="card-text">Podrás obtener tu título de técnico en programación de software y, al mismo tiempo, la Título de Bachiller.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow h-100">
            <div class="card-body">
              <i class="bi bi-globe text-primary fs-2"></i>
              <h5 class="card-title mt-3">Facilidades a largo plazo.</h5>
              <p class="card-text">Podrás obtener algunas facilidades o ventajas ya sea en tu carrera profesional o en el acceso de continuar con el tecnólogo en el SENA.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow h-100">
            <div class="card-body">
              <i class="bi bi-lightbulb text-primary fs-2"></i>
              <h5 class="card-title mt-3">Experiencia práctica desde joven</h5>
              <p class="card-text">El programa técnico del SENA se enfoca en el aprendizaje aplicado. Aprendes haciendo, con herramientas reales como: HTML, CSS, JavaScript, bases de datos,etc.Proyectos reales que puedes mostrar en tu portafolio. Esto te da una base sólida para trabajar como desarrollador junior o continuar estudios superiores con ventaja.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="text-center py-4 bg-light mt-5">
    <p class="mb-0">© 2025 Programa Técnico en Software - SENA </p>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
