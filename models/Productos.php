<?php
require_once __DIR__ . '/../config/database.php';

class Producto
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function obtenerTodos()
    {
        $query = 'SELECT id_producto, codigo_barras, nombre_producto, precio_compra, precio_venta, stock_actual, estado_producto, tarifa_iva FROM productos ORDER BY id_producto DESC';
        $result = $this->conn->query($query);
        $productos = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
        }
        return $productos;
    }

    public function buscar($termino)
    {
        $query = 'SELECT id_producto, codigo_barras, nombre_producto, precio_compra, precio_venta, stock_actual, estado_producto, tarifa_iva FROM productos WHERE codigo_barras LIKE ? OR nombre_producto LIKE ? ORDER BY id_producto DESC';
        $stmt = $this->conn->prepare($query);
        $likeTermino = "%" . $termino . "%";
        $stmt->bind_param('ss', $likeTermino, $likeTermino);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $productos = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
        }
        return $productos;
    }

    public function obtenerPorCodigo($codigo_barras, $exclude_id = null)
    {
        if (empty($codigo_barras)) return null;

        if ($exclude_id) {
            $query = 'SELECT id_producto FROM productos WHERE codigo_barras = ? AND id_producto != ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('si', $codigo_barras, $exclude_id);
        } else {
            $query = 'SELECT id_producto FROM productos WHERE codigo_barras = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('s', $codigo_barras);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function insertar($codigo_barras, $nombre_producto, $precio_compra, $precio_venta, $stock_actual, $tarifa_iva, $estado_producto)
    {
        $query = "INSERT INTO productos (codigo_barras, nombre_producto, precio_compra, precio_venta, stock_actual, tarifa_iva, estado_producto) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssddids', $codigo_barras, $nombre_producto, $precio_compra, $precio_venta, $stock_actual, $tarifa_iva, $estado_producto);
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function actualizar($id_producto, $codigo_barras, $nombre_producto, $precio_compra, $precio_venta, $stock_actual, $tarifa_iva, $estado_producto)
    {
        $query = "UPDATE productos SET codigo_barras = ?, nombre_producto = ?, precio_compra = ?, precio_venta = ?, stock_actual = ?, tarifa_iva = ?, estado_producto = ? WHERE id_producto = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssddidsi', $codigo_barras, $nombre_producto, $precio_compra, $precio_venta, $stock_actual, $tarifa_iva, $estado_producto, $id_producto);
        $stmt->execute();
        return $stmt->affected_rows;
    }
    
    public function cambiarEstado($id_producto, $nuevo_estado)
    {
        $query = "UPDATE productos SET estado_producto = ? WHERE id_producto = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('si', $nuevo_estado, $id_producto);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}