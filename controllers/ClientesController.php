<?php
session_start();
require_once __DIR__ . '/../models/Clientes.php';

class ClientesController
{
    private function setAlert(string $icon, string $title, string $text)
    {
        $_SESSION['alert'] = [
            'icon' => $icon,
            'title' => $title,
            'text' => $text
        ];
    }

    public function index()
    {
        $clienteModel = new Cliente();
        
        $termino = $_GET['q'] ?? '';
        
        if (!empty($termino)) {
            $clientes = $clienteModel->buscar($termino);
        } else {
            $clientes = $clienteModel->obtenerTodos();
        }

        // Definir página actual para el menú lateral
        $current_page = 'clientes.php';

        return [
            'clientes' => $clientes,
            'termino' => $termino
        ];
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../views/admin/clientes.php');
            exit;
        }

        $id_cliente = !empty($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : null;
        $identificacion = $_POST['identificacion'] ?? '';
        $nombre_razon_social = $_POST['nombre_razon_social'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $direccion = $_POST['direccion'] ?? '';

        if (empty($identificacion) || empty($nombre_razon_social)) {
            $this->setAlert('error', 'Campos Obligatorios', 'La Identificación y el Nombre son requeridos.');
            header('Location: ../views/admin/clientes.php');
            exit;
        }

        $clienteModel = new Cliente();

        // Validar que la identificación no exista (excluyendo al cliente actual si estamos editando)
        $existe = $clienteModel->obtenerPorIdentificacion($identificacion, $id_cliente);
        
        if ($existe) {
            $this->setAlert('error', 'Error', 'Ya existe un cliente registrado con esa Identificación/NIT.');
            header('Location: ../views/admin/clientes.php');
            exit;
        }

        if ($id_cliente) {
            // Actualizar
            $clienteModel->actualizar($id_cliente, $identificacion, $nombre_razon_social, $email, $direccion, $telefono);
            $this->setAlert('success', '¡Actualizado!', 'El cliente se ha actualizado correctamente.');
        } else {
            // Insertar
            $clienteModel->insertar($identificacion, $nombre_razon_social, $email, $direccion, $telefono);
            $this->setAlert('success', '¡Registrado!', 'El cliente se ha registrado correctamente.');
        }

        header('Location: ../views/admin/clientes.php');
        exit;
    }

    public function eliminar()
    {
        $id_cliente = $_POST['id_cliente'] ?? $_GET['id_cliente'] ?? null;
        
        if ($id_cliente) {
            $clienteModel = new Cliente();
            $clienteModel->eliminar($id_cliente);
            $this->setAlert('success', '¡Eliminado!', 'El cliente ha sido eliminado.');
        }

        header('Location: ../views/admin/clientes.php');
        exit;
    }

    public function run()
    {
        $action = $_GET['action'] ?? '';
        
        if ($action === 'guardar') {
            $this->guardar();
        } elseif ($action === 'eliminar') {
            $this->eliminar();
        }
    }
}

// Ejecutar controlador si hay acción por URL (POST o acciones directas)
$controller = new ClientesController();
$controller->run();
