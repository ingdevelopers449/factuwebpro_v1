<?php
session_start();
require_once __DIR__ . '/../models/Usuario.php';

function mostrarAlerta(string $type, string $title, string $text, string $redirectUrl) {
    $_SESSION['alert'] = [
        'icon' => $type,
        'title' => $title,
        'text' => $text
    ];
    header("Location: $redirectUrl");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_POST['id_usuario'] ?? null;
    
    if (!$id_usuario) {
        mostrarAlerta('error', 'Error', 'ID de usuario no proporcionado', '../views/admin/gempleados.php');
    }

    $datos = [
        'nombre'               => trim($_POST['nombre'] ?? ''),
        'email'                => trim($_POST['email'] ?? ''),
        'id_rol'               => trim($_POST['id_rol'] ?? ''),
        'estado'               => $_POST['estado'] ?? '',
        'porcentaje_comision'  => (float)($_POST['porcentaje_comision'] ?? 0)
    ];

    if (empty($datos['nombre']) || empty($datos['id_rol']) || empty($datos['estado'])) {
        mostrarAlerta('error', 'Campos obligatorios', 'Todos los campos obligatorios (*) deben ser completados.', '../views/admin/gempleados.php');
    }

    $usuarioModel = new Usuario();
    $resultado = $usuarioModel->actualizar($id_usuario, $datos);

    if ($resultado === true) {
        mostrarAlerta('success', '¡Éxito!', 'Usuario actualizado correctamente.', '../views/admin/gempleados.php');
    } else {
        mostrarAlerta('error', 'Error', 'No se pudo actualizar el usuario. ' . $resultado, '../views/admin/gempleados.php');
    }
} else {
    header('Location: ../views/admin/gempleados.php');
    exit();
}
?>