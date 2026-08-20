<?php
session_start();
require_once __DIR__ . '/../models/Rentabilidad.php';

class RentabilidadController
{
    public function __construct()
    {
        // Solo el administrador (Rol 1) puede acceder a la rentabilidad
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 1) {
            header('Location: ../seller/dashboard.php');
            exit;
        }
    }

    public function index()
    {
        // Valores por defecto: Mes actual
        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-t');

        $rentabilidadModel = new Rentabilidad();

        $consolidado = $rentabilidadModel->obtenerConsolidado($desde, $hasta);
        $rentabilidad_categorias = $rentabilidadModel->obtenerPorCategoria($desde, $hasta);
        $historial = $rentabilidadModel->obtenerHistorialGlobal($desde, $hasta);

        $total_ventas = (float)$consolidado['total_ventas_sin_iva'];
        $total_costos = (float)$consolidado['total_costos'];
        $utilidad_neta = (float)$consolidado['utilidad_neta'];
        $margen = $total_ventas > 0 ? ($utilidad_neta / $total_ventas) * 100 : 0;

        return [
            'desde' => $desde,
            'hasta' => $hasta,
            'consolidado' => $consolidado,
            'rentabilidad_categorias' => $rentabilidad_categorias,
            'historial' => $historial,
            'total_ventas' => $total_ventas,
            'total_costos' => $total_costos,
            'utilidad_neta' => $utilidad_neta,
            'margen' => $margen
        ];
    }

    public function getDetalleFacturaAjax()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }

        $id_factura = $_GET['id_factura'] ?? 0;
        if (!$id_factura) {
            echo json_encode(['success' => false, 'error' => 'ID de factura inválido']);
            exit;
        }

        $rentabilidadModel = new Rentabilidad();
        $detalles = $rentabilidadModel->obtenerDetalleUtilidad((int)$id_factura);

        echo json_encode(['success' => true, 'detalles' => $detalles]);
        exit;
    }
}

// Enrutador para AJAX
if (isset($_GET['action'])) {
    $controller = new RentabilidadController();
    if ($_GET['action'] === 'detalle_ajax') {
        header('Content-Type: application/json');
        $controller->getDetalleFacturaAjax();
    }
}
