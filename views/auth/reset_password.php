<?php
$token = $_GET['token'] ?? '';
if (empty($token)) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña - FactuWeb PRO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="icon" type="image/svg+xml" href="../../img/favicon.svg">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-form-container">
            <div class="text-center mb-4">
                <a href="../../public/index.php">
                    <img src="../../img/logo_nuevo.svg" alt="FactuWeb PRO" class="auth-logo">
                </a>
                <h2 class="auth-title mt-3">Crear Nueva Contraseña</h2>
                <p class="auth-subtitle">Ingresa una contraseña segura. Debe tener mínimo 8 caracteres (letras y números).</p>
            </div>

            <form action="../../controllers/auth/AuthController.php?accion=reset_password" method="POST">
                
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="mb-4">
                    <label class="form-label form-label-dark">Nueva Contraseña</label>
                    <div class="input-dark-wrapper position-relative">
                        <div class="input-dark-icon">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" name="password" id="password" class="input-dark-field pe-5" placeholder="Mínimo 8 caracteres" required>
                        <button type="button" class="btn position-absolute end-0 top-50 translate-middle-y border-0 text-secondary p-2 me-1" id="togglePassword" tabindex="-1">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <small class="text-muted mt-2 d-block" style="font-size: 0.8rem;"><i class="fa-solid fa-shield-halved me-1 text-warning"></i> Recomendamos incluir números y letras.</small>
                </div>

                <button type="submit" class="btn btn-auth-submit w-100 mb-4 mt-2">
                    Guardar Contraseña <i class="fa-solid fa-floppy-disk ms-2"></i>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

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
