<?php
session_start();
require_once __DIR__ . '/../models/Usuario.php';

function mostrarAlerta($type, $title, $text, $redirectUrl) {
    $_SESSION['alert'] = [
        'icon' => $type,
        'title' => $title,
        'text' => $text
    ];
    header("Location: $redirectUrl");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $rol = $_POST['id_rol'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $porcentaje_comision = (float)($_POST['porcentaje_comision'] ?? 0);
    
    // Obtener id_empresa de la sesión actual
    $id_empresa = $_SESSION['usuario']['id_empresa'] ?? 1;

    if (empty($nombre) || empty($email) || empty($password) || empty($rol) || empty($estado)) {
        mostrarAlerta('error', 'Campos obligatorios', 'Todos los campos son obligatorios.', '../views/admin/gempleados.php');
    }

    $usuarioModel = new Usuario();

    if ($usuarioModel->emailExiste($email)) {
        mostrarAlerta('error', 'Correo registrado', 'El email ya está registrado.', '../views/admin/gempleados.php');
    }

    $registrado = $usuarioModel->registrar($id_empresa, $nombre, $email, $password, $rol, $estado, $porcentaje_comision);

    if ($registrado) {
        mostrarAlerta('success', '¡Registro Exitoso!', 'Cuenta creada exitosamente.', '../views/admin/gempleados.php');
    } else {
        mostrarAlerta('error', 'Error de registro', 'Hubo un error al registrar. Verifica tu conexión o intenta más tarde.', '../views/admin/gempleados.php');
    }
} else {
    header("Location: ../views/admin/gempleados.php");
    exit();
}
?>