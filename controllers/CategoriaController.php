<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Categoria.php';

class CategoriaController
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
        $modelo = new Categoria();
        return $modelo->obtenerTodas();
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../views/admin/categorias.php');
            exit;
        }

        $id_categoria = isset($_POST['id_categoria']) ? (int)$_POST['id_categoria'] : 0;
        $nombre = trim($_POST['nombre_categoria'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $estado = $_POST['estado'] ?? 'activa';

        if (empty($nombre)) {
            $this->setAlert('warning', 'Validación', 'El nombre de la categoría es obligatorio.');
            header('Location: ../views/admin/categorias.php');
            exit;
        }

        $modelo = new Categoria();

        // Validar si el nombre ya existe
        if ($modelo->existeNombre($nombre, $id_categoria > 0 ? $id_categoria : null)) {
            $this->setAlert('error', 'Error', 'Ya existe una categoría con ese nombre.');
            header('Location: ../views/admin/categorias.php');
            exit;
        }

        if ($id_categoria > 0) {
            // Actualizar
            $resultado = $modelo->actualizar($id_categoria, $nombre, $descripcion, $estado);
            if ($resultado) {
                $this->setAlert('success', '¡Actualizado!', 'La categoría se actualizó correctamente.');
            } else {
                $this->setAlert('error', 'Error', 'No se pudo actualizar la categoría.');
            }
        } else {
            // Insertar
            $resultado = $modelo->insertar($nombre, $descripcion, $estado);
            if ($resultado) {
                $this->setAlert('success', '¡Guardado!', 'La categoría se creó correctamente.');
            } else {
                $this->setAlert('error', 'Error', 'No se pudo crear la categoría.');
            }
        }

        header('Location: ../views/admin/categorias.php');
        exit;
    }

    public function cambiarEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../views/admin/categorias.php');
            exit;
        }

        $id_categoria = (int)$_POST['id_categoria'];
        $estado_actual = $_POST['estado_actual'];
        $nuevo_estado = ($estado_actual === 'activa') ? 'inactiva' : 'activa';

        $modelo = new Categoria();
        $resultado = $modelo->cambiarEstado($id_categoria, $nuevo_estado);

        if ($resultado) {
            $this->setAlert('success', 'Estado modificado', 'El estado de la categoría se actualizó.');
        } else {
            $this->setAlert('error', 'Error', 'No se pudo cambiar el estado.');
        }

        header('Location: ../views/admin/categorias.php');
        exit;
    }

    public function run()
    {
        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'guardar':
                $this->guardar();
                break;
            case 'cambiar_estado':
                $this->cambiarEstado();
                break;
            default:
                header('Location: ../views/admin/categorias.php');
                exit;
        }
    }
}

// Si se accede directamente para ejecutar una acción POST
if (isset($_GET['action'])) {
    $controller = new CategoriaController();
    $controller->run();
}
