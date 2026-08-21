<?php
session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Usuario.php';

class AuthController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../../views/auth/login.php');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password_hash'] ?? '');

        if (empty($email) || empty($password)) {
            $_SESSION['alert'] = [
                'icon' => 'warning',
                'title' => 'Campos incompletos',
                'text' => 'Debe ingresar correo y contraseña'
            ];
            header('Location: ../../views/auth/login.php');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Correo inválido',
                'text' => 'Ingrese un correo electrónico válido'
            ];
            header('Location: ../../views/auth/login.php');
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->obtenerPorEmail($email);

        if (!$usuario || $usuario['estado'] === 'inactivo') {
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Usuario no encontrado',
                'text' => 'El correo no está registrado o está inactivo'
            ];
            header('Location: ../../views/auth/login.php');
            exit;
        }

        // Check if user is blocked
        if ($usuario['estado'] === 'bloqueado') {
            $ultimo_acceso = strtotime($usuario['ultimo_acceso']);
            $ahora = time();
            $diferencia_minutos = round(($ahora - $ultimo_acceso) / 60);

            if ($diferencia_minutos < 15) {
                $minutos_restantes = 15 - $diferencia_minutos;
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Cuenta bloqueada',
                    'text' => 'Demasiados intentos fallidos. Intente nuevamente en ' . $minutos_restantes . ' minutos.'
                ];
                header('Location: ../../views/auth/login.php');
                exit;
            } else {
                // Time passed, unblock user for this new attempt
                $usuarioModel->resetearIntentosYActualizarAcceso($usuario['id_usuario']);
                $usuario['intentos_fallidos'] = 0;
                $usuario['estado'] = 'activo';
            }
        }

        if (!password_verify($password, $usuario['password_hash'])) {
            $usuarioModel->registrarIntentoFallido($usuario['id_usuario'], $usuario['intentos_fallidos'] ?? 0);

            $intentos_restantes = 2 - ($usuario['intentos_fallidos'] ?? 0);
            if ($intentos_restantes <= 0) {
                $mensaje = 'Su cuenta ha sido bloqueada por seguridad. Espere 15 minutos.';
            } else {
                $mensaje = 'Contraseña incorrecta. Le quedan ' . $intentos_restantes . ' intentos.';
            }

            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Error de Autenticación',
                'text' => $mensaje
            ];
            header('Location: ../../views/auth/login.php');
            exit;
        }

        // Login successful
        $usuarioModel->resetearIntentosYActualizarAcceso($usuario['id_usuario']);

        session_regenerate_id(true);

        $_SESSION['usuario'] = [
            'id_usuario' => $usuario['id_usuario'],
            'id_empresa' => $usuario['id_empresa'],
            'nombre' => $usuario['nombre'],
            'email' => $usuario['email'],
            'id_rol' => $usuario['id_rol']
        ];

        switch ($usuario['id_rol']) {
            case '1':
                header('Location: ../../views/admin/dashboard.php');
                exit;

            case '2':
                header('Location: ../../views/seller/mis_ventas.php');
                exit;

            default:
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Rol no válido',
                    'text' => 'No se pudo determinar el acceso del usuario'
                ];
                header('Location: ../../views/auth/login.php');
                exit;
        }
    }

    public function logout()
    {  // cerrar sesion
        session_unset();
        session_destroy();
        header('Location: ../../views/auth/login.php');
        exit;
    }

    public function forgot_password()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../../views/auth/recovery.php');
            exit;
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['alert'] = [
                'icon' => 'warning',
                'title' => 'Correo inválido',
                'text' => 'Por favor, ingrese un correo válido.'
            ];
            header('Location: ../../views/auth/recovery.php');
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->obtenerPorEmail($email);

        if (!$usuario || $usuario['estado'] === 'inactivo') {
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Correo no registrado',
                'text' => 'El correo ingresado no pertenece a ninguna cuenta registrada.'
            ];
            header('Location: ../../views/auth/recovery.php');
            exit;
        }

        $token = bin2hex(random_bytes(32));
        $expiracion = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        if ($usuarioModel->guardarTokenRecuperacion($email, $token, $expiracion)) {
            // Simulamos envío de correo guardando el link temporal (en producción se usa PHPMailer)
            $link = 'http://localhost/factuwebpro/views/auth/reset_password.php?token=' . $token;

            $_SESSION['alert'] = [
                'icon' => 'success',
                'title' => 'Enlace enviado',
                'text' => 'Se ha enviado un enlace de recuperación a tu correo. (Simulado: ' . $link . ')'
            ];
        } else {
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo generar el token de recuperación.'
            ];
        }

        header('Location: ../../views/auth/recovery.php');
        exit;
    }

    public function reset_password()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../../views/auth/login.php');
            exit;
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($token) || empty($password)) {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Error', 'text' => 'Datos incompletos.'];
            header('Location: ../../views/auth/reset_password.php?token=' . $token);
            exit;
        }

        if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $_SESSION['alert'] = [
                'icon' => 'warning',
                'title' => 'Contraseña débil',
                'text' => 'La contraseña debe tener mínimo 8 caracteres, incluyendo letras y números.'
            ];
            header('Location: ../../views/auth/reset_password.php?token=' . $token);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->obtenerPorToken($token);

        if (!$usuario) {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Token inválido', 'text' => 'El enlace no es válido.'];
            header('Location: ../../views/auth/login.php');
            exit;
        }

        if (strtotime($usuario['token_expiracion']) < time()) {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Enlace expirado', 'text' => 'El enlace ha expirado, solicita uno nuevo.'];
            header('Location: ../../views/auth/recovery.php');
            exit;
        }

        if ($usuarioModel->actualizarPassword($usuario['id_usuario'], $password)) {
            $_SESSION['alert'] = ['icon' => 'success', 'title' => '¡Éxito!', 'text' => 'Contraseña actualizada correctamente.'];
            header('Location: ../../views/auth/login.php');
        } else {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Error', 'text' => 'Hubo un error al actualizar la clave.'];
            header('Location: ../../views/auth/reset_password.php?token=' . $token);
        }
        exit;
    }
}

$controller = new AuthController();

$accion = $_GET['accion'] ?? 'login';

if ($accion === 'logout') {
    $controller->logout();
} elseif ($accion === 'forgot_password') {
    $controller->forgot_password();
} elseif ($accion === 'reset_password') {
    $controller->reset_password();
} else {
    $controller->login();
}
?>