<?php
session_start();
require_once __DIR__ . '/../models/Usuario.php';
class DeletedUsuarioController {
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../views/admin/gempleados.php');
            exit;
        }

        $id_usuario = $_POST['id_usuario'] ?? null;
        if (!$id_usuario) {
            $this->setAlert('error', 'Error', 'ID de usuario no proporcionado');
            header('Location: ../views/admin/gempleados.php');
            exit;
        }

        $resultado = $this->usuarioModel->eliminar($id_usuario);

        if ($resultado === true) {
            $this->setAlert('success', 'Éxito', 'Usuario eliminado correctamente');
        } else {
            $this->setAlert('error', 'Error', $resultado);
        }

        header('Location: ../views/admin/gempleados.php');
        exit;
    }
    private function setAlert(string $icon, string $title, string $text)
    {
        $_SESSION['alert'] = [
            'icon' => $icon,
            'title' => $title,
            'text' => $text
        ];
    }
}

// Ejecutar el controlador
$controller = new DeletedUsuarioController();
$controller->delete();