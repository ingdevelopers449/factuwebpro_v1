<?php
require_once __DIR__ . '/../config/database.php';

class Rentabilidad
{
    private mysqli $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function obtenerConsolidado(string $fecha_inicio, string $fecha_fin): array
    {
        $query = "
            SELECT 
                COALESCE(SUM(d.cantidad * d.precio_unitario_venta), 0) as total_ventas_sin_iva,
                COALESCE(SUM(d.cantidad * d.precio_unitario_costo), 0) as total_costos,
                COALESCE(SUM(d.cantidad * (d.precio_unitario_venta - d.precio_unitario_costo)), 0) as utilidad_neta
            FROM detalle_factura d
            JOIN facturas f ON d.id_factura = f.id_factura
            WHERE DATE(f.fecha_emision) BETWEEN ? AND ?
        ";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('ss', $fecha_inicio, $fecha_fin);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }
        return ['total_ventas_sin_iva' => 0, 'total_costos' => 0, 'utilidad_neta' => 0];
    }

    public function obtenerPorCategoria(string $fecha_inicio, string $fecha_fin): array
    {
        $query = "
            SELECT 
                COALESCE(c.nombre_categoria, 'Sin Categoría') as nombre_categoria,
                COALESCE(SUM(d.cantidad * d.precio_unitario_venta), 0) as ventas,
                COALESCE(SUM(d.cantidad * d.precio_unitario_costo), 0) as costos,
                COALESCE(SUM(d.cantidad * (d.precio_unitario_venta - d.precio_unitario_costo)), 0) as utilidad
            FROM detalle_factura d
            JOIN facturas f ON d.id_factura = f.id_factura
            JOIN productos p ON d.id_producto = p.id_producto
            LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
            WHERE DATE(f.fecha_emision) BETWEEN ? AND ?
            GROUP BY c.id_categoria, c.nombre_categoria
            ORDER BY utilidad DESC
        ";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('ss', $fecha_inicio, $fecha_fin);
            $stmt->execute();
            $result = $stmt->get_result();
            $datos = [];
            while ($row = $result->fetch_assoc()) {
                $datos[] = $row;
            }
            return $datos;
        }
        return [];
    }

    public function obtenerHistorialGlobal(string $fecha_inicio, string $fecha_fin): array
    {
        $query = "
            SELECT 
                f.id_factura,
                f.prefijo_resolucion,
                f.consecutivo,
                f.fecha_emision,
                f.total_pagar,
                COALESCE(c.nombre_razon_social, 'Consumidor Final') as cliente,
                u.nombre as vendedor,
                (SELECT SUM(d.cantidad * (d.precio_unitario_venta - d.precio_unitario_costo)) 
                 FROM detalle_factura d WHERE d.id_factura = f.id_factura) as utilidad_factura
            FROM facturas f
            LEFT JOIN clientes c ON f.id_cliente = c.id_cliente
            LEFT JOIN usuarios u ON f.id_usuario = u.id_usuario
            WHERE DATE(f.fecha_emision) BETWEEN ? AND ?
            ORDER BY f.fecha_emision DESC
        ";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('ss', $fecha_inicio, $fecha_fin);
            $stmt->execute();
            $result = $stmt->get_result();
            $facturas = [];
            while ($row = $result->fetch_assoc()) {
                $facturas[] = $row;
            }
            return $facturas;
        }
        return [];
    }

    public function obtenerDetalleUtilidad(int $id_factura): array
    {
        $query = "
            SELECT 
                d.cantidad,
                d.precio_unitario_venta,
                d.precio_unitario_costo,
                p.nombre_producto,
                (d.cantidad * d.precio_unitario_venta) as subtotal_venta,
                (d.cantidad * d.precio_unitario_costo) as subtotal_costo,
                (d.cantidad * (d.precio_unitario_venta - d.precio_unitario_costo)) as utilidad_linea
            FROM detalle_factura d
            JOIN productos p ON d.id_producto = p.id_producto
            WHERE d.id_factura = ?
        ";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('i', $id_factura);
            $stmt->execute();
            $result = $stmt->get_result();
            $detalles = [];
            while ($row = $result->fetch_assoc()) {
                $detalles[] = $row;
            }
            return $detalles;
        }
        return [];
    }
}
