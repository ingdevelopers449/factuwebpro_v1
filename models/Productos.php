<?php
require_once __DIR__ . '/../config/database.php';

class Producto
{
    private mysqli $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function obtenerTodos(int $limit = 10, int $offset = 0): array
    {
        $query = "SELECT p.*, c.nombre_categoria FROM productos p LEFT JOIN categorias c ON p.id_categoria = c.id_categoria ORDER BY p.nombre_producto ASC LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('ii', $limit, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            $productos = [];
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
            return $productos;
        }
        return [];
    }

    public function contarTodos(): int
    {
        $query = "SELECT COUNT(*) as total FROM productos";
        $result = $this->conn->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            return (int)$row['total'];
        }
        return 0;
    }

    public function buscar(string $termino, int $limit = 10, int $offset = 0): array
    {
        $termino_like = '%' . $termino . '%';
        $query = "SELECT p.*, c.nombre_categoria FROM productos p LEFT JOIN categorias c ON p.id_categoria = c.id_categoria WHERE p.nombre_producto LIKE ? OR p.codigo_barras LIKE ? ORDER BY p.nombre_producto ASC LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('ssii', $termino_like, $termino_like, $limit, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            $productos = [];
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
            return $productos;
        }
        return [];
    }

    public function contarBuscar(string $termino): int
    {
        $termino_like = '%' . $termino . '%';
        $query = "SELECT COUNT(*) as total FROM productos WHERE nombre_producto LIKE ? OR codigo_barras LIKE ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('ss', $termino_like, $termino_like);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return (int)$row['total'];
            }
        }
        return 0;
    }

    public function existeCodigo(string $codigo_barras, ?int $id_excluir = null): bool
    {
        if (empty($codigo_barras)) return false;
        
        $query = "SELECT id_producto FROM productos WHERE codigo_barras = ?";
        $params = [$codigo_barras];
        $types = "s";

        if ($id_excluir) {
            $query .= " AND id_producto != ?";
            $params[] = $id_excluir;
            $types .= "i";
        }

        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }
        return false;
    }

    public function insertar(?string $codigo_barras, string $nombre_producto, float $precio_compra, float $precio_venta, int $stock_actual, float $tarifa_iva, string $estado_producto, ?int $id_categoria, ?string $imagen_url = null)
    {
        $query = "INSERT INTO productos (codigo_barras, nombre_producto, precio_compra, precio_venta, stock_actual, tarifa_iva, estado_producto, id_categoria, imagen_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('ssddidsss', $codigo_barras, $nombre_producto, $precio_compra, $precio_venta, $stock_actual, $tarifa_iva, $estado_producto, $id_categoria, $imagen_url);
            return $stmt->execute() ? $stmt->insert_id : false;
        }
        return false;
    }

    public function actualizar(int $id_producto, ?string $codigo_barras, string $nombre_producto, float $precio_compra, float $precio_venta, int $stock_actual, float $tarifa_iva, string $estado_producto, ?int $id_categoria, ?string $imagen_url = null): bool
    {
        if ($imagen_url !== null) {
            $query = "UPDATE productos SET codigo_barras=?, nombre_producto=?, precio_compra=?, precio_venta=?, stock_actual=?, tarifa_iva=?, estado_producto=?, id_categoria=?, imagen_url=? WHERE id_producto=?";
            $stmt = $this->conn->prepare($query);
            if ($stmt) {
                $stmt->bind_param('ssddidsssi', $codigo_barras, $nombre_producto, $precio_compra, $precio_venta, $stock_actual, $tarifa_iva, $estado_producto, $id_categoria, $imagen_url, $id_producto);
                return $stmt->execute();
            }
        } else {
            $query = "UPDATE productos SET codigo_barras=?, nombre_producto=?, precio_compra=?, precio_venta=?, stock_actual=?, tarifa_iva=?, estado_producto=?, id_categoria=? WHERE id_producto=?";
            $stmt = $this->conn->prepare($query);
            if ($stmt) {
                $stmt->bind_param('ssddidssi', $codigo_barras, $nombre_producto, $precio_compra, $precio_venta, $stock_actual, $tarifa_iva, $estado_producto, $id_categoria, $id_producto);
                return $stmt->execute();
            }
        }
        return false;
    }

    public function alternarEstado(int $id_producto, string $nuevo_estado): bool
    {
        $query = "UPDATE productos SET estado_producto = ? WHERE id_producto = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('si', $nuevo_estado, $id_producto);
        return $stmt->execute();
    }
}