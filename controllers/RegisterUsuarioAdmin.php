<?php
session_start();
require_once __DIR__ . '/../models/Usuario.php';

function mostrarAlerta($type, $title, $text, $redirectUrl) {
    echo "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Procesando...</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500&display=swap');
            
            body {
                background-color: #0b0f19; /* Color de fondo oscuro del tema */
                font-family: 'Plus Jakarta Sans', sans-serif;
                margin: 0;
                height: 100vh;
            }
            
            .glass-alert {
                background: rgba(17, 24, 39, 0.8) !important;
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.08) !important;
                border-radius: 24px !important;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5) !important;
            }
            
            .alert-title {
                font-family: 'Outfit', sans-serif !important;
                font-weight: 700 !important;
            }
            
            .btn-premium-primary {
                font-family: 'Outfit', sans-serif !important;
                font-weight: 600 !important;
                letter-spacing: 0.3px !important;
                padding: 12px 32px !important;
                border-radius: 12px !important;
                background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
                color: white !important;
                border: none !important;
                box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.4) !important;
                transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
                cursor: pointer;
            }
            
            .btn-premium-primary:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 6px 20px 0 rgba(16, 185, 129, 0.6) !important;
            }
        </style>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: '{$type}',
                title: '{$title}',
                text: '{$text}',
                background: 'transparent',
                color: '#fff',
                backdrop: `rgba(11, 15, 25, 0.85)`,
                customClass: {
                    popup: 'glass-alert',
                    title: 'alert-title',
                    confirmButton: 'btn-premium-primary'
                },
                buttonsStyling: false
            }).then(() => {
                window.location.href = '{$redirectUrl}';
            });
        </script>
    </body>
    </html>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password_hash'] ?? '');
    $rol = $_POST['rol'] ?? '';
    $estado = $_POST['estado'] ?? '';

    if (empty($email) || empty($password) || empty($rol) || empty($estado)) {
        mostrarAlerta('error', 'Campos obligatorios', 'Todos los campos son obligatorios.', '../views/admin/gusuarios.php');
    }

    $usuarioModel = new Usuario();

    if ($usuarioModel->emailExiste($email)) {
        mostrarAlerta('error', 'Correo registrado', 'El email ya está registrado.', '../views/admin/gusuarios.php');
    }

    $registrado = $usuarioModel->registrar($email, $password, $rol, $estado);

    if ($registrado) {
        mostrarAlerta('success', '¡Registro Exitoso!', 'Cuenta creada exitosamente.', '../views/admin/gusuarios.php');
    } else {
        mostrarAlerta('error', 'Error de registro', 'Hubo un error al registrar. Verifica tu conexión o intenta más tarde.', '../views/admin/gusuarios.php');
    }
} else {
    header("Location: ../views/admin/gusuarios.php");
    exit();
}
?>