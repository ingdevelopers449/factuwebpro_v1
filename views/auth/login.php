<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - FactuWeb PRO</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="../../public/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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
                    </label>
                    <div class="input-dark-wrapper position-relative">
                        <div class="input-dark-icon">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" name="password_hash" id="password_hash" class="input-dark-field pe-5" placeholder="••••••••" required>
                        <button type="button" class="btn position-absolute end-0 top-50 translate-middle-y border-0 text-secondary p-2 me-1" id="togglePassword" tabindex="-1">
                            <i class="fa-solid fa-eye"></i>
                        </button>
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
                <button type="submit" class="btn btn-auth-submit w-100 mb-3 mt-2">
                    Iniciar Sesión <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i>
                </button>
            </form>

            <!-- Link recuperación FUERA del form para evitar validación accidental -->
            <div class="text-center mt-2">
                <a href="recovery.php" class="text-decoration-none" style="color: rgba(255,255,255,0.55); font-size: 0.85rem;">
                    <i class="fa-solid fa-key me-1"></i> ¿Olvidaste tu contraseña?
                </a>
            </div>
        </div>
    </div>
    <script>
        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password_hash');

        if(togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }
    </script>

    <!-- SweetAlert Global -->
    <?php session_start(); if (isset($_SESSION['alert'])): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '<?= htmlspecialchars($_SESSION['alert']['icon']) ?>',
                    title: '<?= htmlspecialchars($_SESSION['alert']['title']) ?>',
                    text: '<?= htmlspecialchars($_SESSION['alert']['text']) ?>',
                    background: '#1f2937',
                    color: '#fff',
                    confirmButtonColor: '#f59e0b'
                });
            });
        </script>
        <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>

</body>
</html>
