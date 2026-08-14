<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - FactuWeb PRO</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6.5.0 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../public/css/style.css">
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="../../img/favicon.svg">
</head>
<body>

    <div class="auth-wrapper">
        <div class="auth-form-container">
            <!-- Header -->
            <div class="text-center">
                <a href="../../public/index.php">
                    <img src="../../img/logo_nuevo.svg" alt="FactuWeb PRO" class="auth-logo">
                </a>
                <h2 class="auth-title">¡Bienvenido de nuevo!</h2>
                <p class="auth-subtitle">Ingresa tus credenciales para acceder al sistema</p>
            </div>

            <form action="../../controllers/auth/AuthController.php?accion=login" method="POST">
                
                <!-- Email -->
                <div class="mb-4">
                    <label class="form-label form-label-dark">Correo Electrónico</label>
                    <div class="input-dark-wrapper">
                        <div class="input-dark-icon">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <input type="email" name="email" class="input-dark-field" placeholder="nombre@ejemplo.com" required>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label class="form-label form-label-dark">
                        <span>Contraseña</span>
                        <a href="#">¿Olvidaste tu contraseña?</a>
                    </label>
                    <div class="input-dark-wrapper">
                        <div class="input-dark-icon">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" name="password_hash" class="input-dark-field" placeholder="••••••••" required>
                    </div>
                </div>

                <!-- Checkbox -->
                <div class="form-check mb-4 mt-3">
                    <input class="form-check-input bg-dark border-secondary shadow-none" type="checkbox" id="rememberMe">
                    <label class="form-check-label check-dark-label" for="rememberMe">
                        Recordarme en este dispositivo
                    </label>
                </div>

                <!-- Botón Ingresar -->
                <button type="submit" class="btn btn-auth-submit w-100 mb-4 mt-2">
                    Iniciar Sesión <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i>
                </button>
            </form>
        </div>
    </div>

</body>
</html>
