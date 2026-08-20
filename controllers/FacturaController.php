<?php
session_start();
require_once __DIR__ . '/../models/Factura.php';
require_once __DIR__ . '/../models/Clientes.php';
require_once __DIR__ . '/../models/Productos.php';

class FacturaController
{
    private function setAlert(string $icon, string $title, string $text)
    {
        $_SESSION['alert'] = [
            'icon' => $icon,
            'title' => $title,
            'text' => $text
        ];
    }

    public function init_pos()
    {
        header('Content-Type: application/json');
        
        $clienteModel = new Cliente();
        $productoModel = new Producto();

        $clientes = $clienteModel->obtenerTodos(); 
        
        $todosProductos = $productoModel->obtenerTodos();
        $productosVenta = array_filter($todosProductos, function($p) {
            return strtolower($p['estado_producto']) === 'activo' && $p['stock_actual'] > 0;
        });

        echo json_encode([
            'success' => true,
            'clientes' => array_values($clientes),
            'productos' => array_values($productosVenta)
        ]);
        exit;
    }

    public function procesar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data || empty($data['detalles'])) {
            echo json_encode(['success' => false, 'error' => 'No hay datos o carrito vacío.']);
            exit;
        }

        $id_empresa = $_SESSION['usuario']['id_empresa'] ?? 1;
        $id_usuario = $_SESSION['usuario']['id_usuario'] ?? null;
        $id_cliente = !empty($data['id_cliente']) ? (int)$data['id_cliente'] : null;
        
        $subtotal = (float)$data['subtotal'];
        $total_iva = (float)$data['total_iva'];
        $total_pagar = (float)$data['total_pagar'];
        $detalles = $data['detalles'];

        $facturaModel = new Factura();
        $resultado = $facturaModel->crearFactura($id_empresa, $id_cliente, $id_usuario, $subtotal, $total_iva, $total_pagar, $detalles);

        if ($resultado['success']) {
            $this->setAlert('success', '¡Factura Procesada!', 'Factura ' . $resultado['numero_factura'] . ' generada con éxito.');
        }

        echo json_encode($resultado);
        exit;
    }

    public function crear_cliente_ajax()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }

        $identificacion = $_POST['identificacion'] ?? '';
        $nombre_razon_social = $_POST['nombre_razon_social'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $direccion = $_POST['direccion'] ?? '';

        if (empty($identificacion) || empty($nombre_razon_social)) {
            echo json_encode(['success' => false, 'error' => 'La Identificación y el Nombre son requeridos.']);
            exit;
        }

        $clienteModel = new Cliente();
        $existe = $clienteModel->obtenerPorIdentificacion($identificacion);
        
        if ($existe) {
            echo json_encode(['success' => false, 'error' => 'Ya existe un cliente con esta Identificación/NIT.']);
            exit;
        }

        $id_insertado = $clienteModel->insertar($identificacion, $nombre_razon_social, $email, $direccion, $telefono);
        
        if ($id_insertado) {
            echo json_encode([
                'success' => true, 
                'cliente' => [
                    'id_cliente' => $id_insertado,
                    'identificacion' => $identificacion,
                    'nombre_razon_social' => $nombre_razon_social,
                    'telefono' => $telefono
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al guardar el cliente en la base de datos.']);
        }
        exit;
    }

    public function run()
    {
        $action = $_GET['action'] ?? '';
        
        if ($action === 'procesar') {
            header('Content-Type: application/json');
            $this->procesar();
        } elseif ($action === 'init_pos') {
            $this->init_pos();
        } elseif ($action === 'crear_cliente_ajax') {
            header('Content-Type: application/json');
            $this->crear_cliente_ajax();
        }
    }
}

if (isset($_GET['action'])) {
    $controller = new FacturaController();
    $controller->run();
}
