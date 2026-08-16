<?php
session_start();
require_once __DIR__ . '/../models/Productos.php';

class ProductosController
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
        $productoModel = new Producto();
        
        $termino = $_GET['q'] ?? '';
        
        if (!empty($termino)) {
            $productos = $productoModel->buscar($termino);
        } else {
            $productos = $productoModel->obtenerTodos();
        }

        $current_page = 'productos.php';

        return [
            'productos' => $productos,
            'termino' => $termino
        ];
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../views/admin/productos.php');
            exit;
        }

        $id_producto = !empty($_POST['id_producto']) ? (int)$_POST['id_producto'] : null;
        $codigo_barras = $_POST['codigo_barras'] ?? '';
        $nombre_producto = $_POST['nombre_producto'] ?? '';
        $precio_compra = !empty($_POST['precio_compra']) ? (float)$_POST['precio_compra'] : 0.0;
        $precio_venta = !empty($_POST['precio_venta']) ? (float)$_POST['precio_venta'] : 0.0;
        $stock_actual = !empty($_POST['stock_actual']) ? (int)$_POST['stock_actual'] : 0;
        $tarifa_iva = !empty($_POST['tarifa_iva']) ? (float)$_POST['tarifa_iva'] : 19.00;
        $estado_producto = $_POST['estado_producto'] ?? 'activo';

        if (empty($nombre_producto) || empty($precio_compra) || empty($precio_venta)) {
            $this->setAlert('error', 'Campos Obligatorios', 'El Nombre del producto y los Precios son requeridos.');
            header('Location: ../views/admin/productos.php');
            exit;
        }

        $productoModel = new Producto();

        // Validar que el código de barras no exista (si se proporcionó uno)
        if (!empty($codigo_barras)) {
            $existe = $productoModel->obtenerPorCodigo($codigo_barras, $id_producto);
            if ($existe) {
                $this->setAlert('error', 'Error', 'Ya existe un producto registrado con ese Código de barras.');
                header('Location: ../views/admin/productos.php');
                exit;
            }
        }

        if ($id_producto) {
            // Actualizar
            $productoModel->actualizar($id_producto, $codigo_barras, $nombre_producto, $precio_compra, $precio_venta, $stock_actual, $tarifa_iva, $estado_producto);
            $this->setAlert('success', '¡Actualizado!', 'El producto se ha actualizado correctamente.');
        } else {
            // Insertar
            $productoModel->insertar($codigo_barras, $nombre_producto, $precio_compra, $precio_venta, $stock_actual, $tarifa_iva, $estado_producto);
            $this->setAlert('success', '¡Registrado!', 'El producto se ha registrado correctamente.');
        }

        header('Location: ../views/admin/productos.php');
        exit;
    }

    public function alternarEstado()
    {
        $id_producto = $_GET['id_producto'] ?? null;
        $estado_actual = $_GET['estado'] ?? 'activo';
        
        if ($id_producto) {
            $nuevo_estado = ($estado_actual === 'activo') ? 'inactivo' : 'activo';
            $productoModel = new Producto();
            $productoModel->cambiarEstado($id_producto, $nuevo_estado);
            
            if ($nuevo_estado === 'inactivo') {
                $this->setAlert('success', '¡Desactivado!', 'El producto ha sido desactivado del catálogo.');
            } else {
                $this->setAlert('success', '¡Activado!', 'El producto vuelve a estar disponible.');
            }
        }

        header('Location: ../views/admin/productos.php');
        exit;
    }

    public function run()
    {
        $action = $_GET['action'] ?? '';
        
        if ($action === 'guardar') {
            $this->guardar();
        } elseif ($action === 'alternar_estado') {
            $this->alternarEstado();
        }
    }
}

// Ejecutar controlador
$controller = new ProductosController();
$controller->run();
