<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - FactuWeb PRO</title>
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
                <h2 class="auth-title mt-3">Recuperar Acceso</h2>
                <p class="auth-subtitle">Ingresa tu correo electrónico y te enviaremos un enlace temporal para crear una nueva contraseña.</p>
            </div>

            <form action="../../controllers/auth/AuthController.php?accion=forgot_password" method="POST">
                
                <div class="mb-4">
                    <label class="form-label form-label-dark">Correo Electrónico registrado</label>
                    <div class="input-dark-wrapper">
                        <div class="input-dark-icon">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <input type="email" name="email" class="input-dark-field" placeholder="tucorreo@ejemplo.com" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-auth-submit w-100 mb-4 mt-2">
                    Enviar Enlace de Recuperación <i class="fa-solid fa-paper-plane ms-2"></i>
                </button>

                <div class="text-center">
                    <a href="login.php" class="text-decoration-none text-muted" style="font-size: 0.9rem;">
                        <i class="fa-solid fa-arrow-left me-1"></i> Volver a Iniciar Sesión
                    </a>
                </div>
            </form>
        </div>
    </div>

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
