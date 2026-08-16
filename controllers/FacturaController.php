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

    public function index()
    {
        $clienteModel = new Cliente();
        $productoModel = new Producto();

        // Obtener datos activos para el POS
        $clientes = $clienteModel->obtenerTodos(); // Idealmente solo activos
        
        // Obtener solo productos activos y con stock para la venta
        // Podemos usar obtenerTodos y filtrar, o modificar el modelo. Aquí filtramos por simplicidad y robustez.
        $todosProductos = $productoModel->obtenerTodos();
        $productosVenta = array_filter($todosProductos, function($p) {
            return strtolower($p['estado_producto']) === 'activo' && $p['stock_actual'] > 0;
        });

        $current_page = 'facturas.php';

        return [
            'clientes' => $clientes,
            'productos' => array_values($productosVenta), // reindexar
        ];
    }

    public function procesar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }

        // Leer datos JSON enviados por fetch/axios
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data || empty($data['detalles'])) {
            echo json_encode(['success' => false, 'error' => 'No hay datos o carrito vacío.']);
            exit;
        }

        $id_empresa = $_SESSION['usuario']['id_empresa'] ?? 1; // Asumiendo empresa 1 si no está en sesión por ahora
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

    public function run()
    {
        $action = $_GET['action'] ?? '';
        
        if ($action === 'procesar') {
            header('Content-Type: application/json');
            $this->procesar();
        }
    }
}

// Si es una petición AJAX/POST directa a la acción
if (isset($_GET['action'])) {
    $controller = new FacturaController();
    $controller->run();
}
