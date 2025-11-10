<?php
session_start();
include("../includes/conexion.php");

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $password = $_POST["password"];
    $confirmar_password = $_POST["confirmar_password"];

    // Validaciones
    if (empty($nombre) || empty($correo) || empty($password) || empty($confirmar_password)) {
        $mensaje = "Todos los campos son obligatorios.";
        $tipo_mensaje = 'danger';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "El formato del correo electrónico no es válido.";
        $tipo_mensaje = 'danger';
    } elseif (strlen($password) < 6) {
        $mensaje = "La contraseña debe tener al menos 6 caracteres.";
        $tipo_mensaje = 'danger';
    } elseif ($password !== $confirmar_password) {
        $mensaje = "Las contraseñas no coinciden.";
        $tipo_mensaje = 'danger';
    } else {
        try {
            // Verificar si el correo ya existe
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
            $stmt->execute([$correo]);
            
            if ($stmt->fetch()) {
                $mensaje = "El correo electrónico ya está registrado.";
                $tipo_mensaje = 'danger';
            } else {
                // Hash de la contraseña
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Insertar nuevo usuario
                $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, password, tipo_usuario, fecha_registro) VALUES (?, ?, ?, 'Usuario', NOW())");
                $stmt->execute([$nombre, $correo, $password_hash]);
                
                // Redirigir al login con mensaje de éxito
                header("Location: login.php?registro=exitoso");
                exit();
            }
        } catch (PDOException $e) {
            $mensaje = "Error en el sistema. Intente más tarde.";
            $tipo_mensaje = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - I.E. Promoción Social</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
<style>
  body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .register-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            padding: 50px;
            width: 100%;
            max-width: 500px;
    position: relative;
            overflow: hidden;
        }
        
        .register-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            animation: shimmer 3s infinite;
            z-index: 1;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }
        
        .register-content {
    position: relative;
    z-index: 2;
  }
        
        .register-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .register-icon {
            display: inline-block;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
        }
        
        .register-icon i {
            font-size: 2.5rem;
            color: white;
        }
        
  .register-title {
    font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        
        .register-subtitle {
            color: #666;
            font-size: 1rem;
        }
        
        .alert-custom {
            border-radius: 15px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }
        
        .input-group {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .input-group:focus-within {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .input-group-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 15px 20px;
            font-size: 1.2rem;
        }
        
        .form-control {
            border: none;
            padding: 15px 20px;
            font-size: 1rem;
            background: white;
            outline: none;
        }
        
        .form-control:focus {
            box-shadow: none;
            background: #f8f9fa;
        }
        
        .form-text {
            color: #666;
            font-size: 0.85rem;
            margin-top: 5px;
            padding-left: 15px;
        }
        
        .register-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 15px;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .register-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .register-footer {
            text-align: center;
            margin-top: 30px;
        }
        
        .login-link {
            background: white;
            border: 2px solid #667eea;
            border-radius: 25px;
            padding: 10px 25px;
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            margin: 10px 5px;
        }
        
        .login-link:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .back-link {
            background: #f8f9fa;
            border: 2px solid #6c757d;
            border-radius: 25px;
            padding: 10px 25px;
            color: #6c757d;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            margin: 10px 5px;
        }
        
        .back-link:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(108, 117, 125, 0.3);
        }
        
        .divider {
            margin: 20px 0;
    text-align: center;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #dee2e6;
        }
        
        .divider span {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 15px;
            color: #666;
            font-size: 0.9rem;
        }
        
        .password-strength {
            margin-top: 10px;
            padding: 10px;
            border-radius: 10px;
            font-size: 0.85rem;
            display: none;
        }
        
        .strength-weak {
            background: #ffe6e6;
            color: #d63384;
            border: 1px solid #f5c2c7;
        }
        
        .strength-medium {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .strength-strong {
            background: #d1edff;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        @media (max-width: 576px) {
            .register-container {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .register-title {
                font-size: 1.5rem;
            }
            
  .register-icon {
                width: 60px;
                height: 60px;
            }
            
            .register-icon i {
                font-size: 2rem;
            }
  }
</style>
</head>
<body>

<div class="register-container">
    <div class="register-content">
        <div class="register-header">
            <div class="register-icon">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <h1 class="register-title">Crear Cuenta</h1>
            <p class="register-subtitle">Únete a la comunidad de la I.E. Promoción Social</p>
        </div>
        
        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje ?> alert-custom alert-dismissible fade show" role="alert">
                <i class="bi bi-<?= $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="registro.php" autocomplete="off" id="registerForm">
            <div class="form-group">
                <label for="nombre" class="form-label">
                    <i class="bi bi-person me-2"></i>Nombre completo
                </label>
            <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" name="nombre" id="nombre" class="form-control" value="<?= htmlspecialchars($nombre ?? '') ?>" required autofocus placeholder="Tu nombre completo">
            </div>
        </div>
            
            <div class="form-group">
                <label for="correo" class="form-label">
                    <i class="bi bi-envelope me-2"></i>Correo electrónico
                </label>
            <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" name="correo" id="correo" class="form-control" value="<?= htmlspecialchars($correo ?? '') ?>" required placeholder="tu@email.com">
            </div>
        </div>
            
            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="bi bi-lock me-2"></i>Contraseña
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" class="form-control" required placeholder="Mínimo 6 caracteres">
                </div>
                <div class="form-text">
                    <i class="bi bi-info-circle me-1"></i>Mínimo 6 caracteres
                </div>
                <div class="password-strength" id="passwordStrength"></div>
            </div>
            
            <div class="form-group">
                <label for="confirmar_password" class="form-label">
                    <i class="bi bi-lock-fill me-2"></i>Confirmar contraseña
                </label>
            <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock-fill"></i>
                    </span>
                    <input type="password" name="confirmar_password" id="confirmar_password" class="form-control" required placeholder="Repite tu contraseña">
            </div>
        </div>
            
            <button type="submit" class="register-btn">
                <i class="bi bi-person-plus me-2"></i>Crear Cuenta
            </button>
    </form>

        <div class="register-footer">
            <div class="divider">
                <span>¿Ya tienes una cuenta?</span>
            </div>
            <a href="login.php" class="login-link">
                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
            </a>
            
            <div class="divider">
                <span>O</span>
            </div>
            
            <a href="../index.php" class="back-link">
                <i class="bi bi-arrow-left me-2"></i>Volver al inicio
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmar_password');
    const strengthDiv = document.getElementById('passwordStrength');
    
    // Validación de fortaleza de contraseña
    password.addEventListener('input', function() {
        const value = this.value;
        let strength = 0;
        let message = '';
        let className = '';
        
        if (value.length >= 6) strength++;
        if (value.match(/[a-z]/)) strength++;
        if (value.match(/[A-Z]/)) strength++;
        if (value.match(/[0-9]/)) strength++;
        if (value.match(/[^a-zA-Z0-9]/)) strength++;
        
        if (strength < 2) {
            message = 'Contraseña débil';
            className = 'strength-weak';
        } else if (strength < 4) {
            message = 'Contraseña media';
            className = 'strength-medium';
        } else {
            message = 'Contraseña fuerte';
            className = 'strength-strong';
        }
        
        if (value.length > 0) {
            strengthDiv.textContent = message;
            strengthDiv.className = `password-strength ${className}`;
            strengthDiv.style.display = 'block';
        } else {
            strengthDiv.style.display = 'none';
        }
    });
    
    // Validación de confirmación de contraseña
    confirmPassword.addEventListener('input', function() {
        if (this.value !== password.value && this.value.length > 0) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });
    
    // Validación del formulario
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
            return false;
        }
    });
});
</script>
</body>
</html>
</script>
</body>
</html>
