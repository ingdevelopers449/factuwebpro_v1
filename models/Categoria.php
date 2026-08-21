<?php
require_once __DIR__ . '/../config/database.php';

class Categoria
{
    private \mysqli $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function obtenerTodas()
    {
        $query = "SELECT c.id_categoria, c.nombre_categoria, c.descripcion, c.estado, c.fecha_creacion,
                         COUNT(p.id_producto) AS total_productos
                  FROM categorias c
                  LEFT JOIN productos p ON c.id_categoria = p.id_categoria
                  GROUP BY c.id_categoria
                  ORDER BY c.nombre_categoria ASC";
        $result = $this->conn->query($query);
        $categorias = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categorias[] = $row;
            }
        }
        return $categorias;
    }

    public function obtenerActivas()
    {
        $query = "SELECT id_categoria, nombre_categoria FROM categorias WHERE estado = 'activa' ORDER BY nombre_categoria ASC";
        $result = $this->conn->query($query);
        $categorias = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categorias[] = $row;
            }
        }
        return $categorias;
    }

    public function obtenerPorId(int $id_categoria)
    {
        $query = "SELECT id_categoria, nombre_categoria, descripcion, estado FROM categorias WHERE id_categoria = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('i', $id_categoria);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }
        return null;
    }

    public function existeNombre(string $nombre, ?int $id_excluir = null)
    {
        $query = "SELECT id_categoria FROM categorias WHERE nombre_categoria = ?";
        $params = [$nombre];
        $types = "s";

        if ($id_excluir) {
            $query .= " AND id_categoria != ?";
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

    public function insertar(string $nombre_categoria, string $descripcion, string $estado, string $fecha_creacion = 'now', string $fecha_actualizacion = 'now')
    {
        $query = "INSERT INTO categorias (nombre_categoria, descripcion, estado, fecha_creacion, fecha_actualizacion) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $fc = ($fecha_creacion === 'now' || empty($fecha_creacion)) ? date('Y-m-d H:i:s') : $fecha_creacion;
            $fa = ($fecha_actualizacion === 'now' || empty($fecha_actualizacion)) ? date('Y-m-d H:i:s') : $fecha_actualizacion;
            $stmt->bind_param('sssss', $nombre_categoria, $descripcion, $estado, $fc, $fa);
            return $stmt->execute() ? $stmt->insert_id : false;
        }
        return false;
    }

    public function actualizar(int $id_categoria, string $nombre_categoria, string $descripcion, string $estado, string $fecha_creacion = 'now', string $fecha_actualizacion = 'now')
    {
        $query = "UPDATE categorias SET nombre_categoria = ?, descripcion = ?, estado = ?, fecha_actualizacion = ? WHERE id_categoria = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $fa = ($fecha_actualizacion === 'now' || empty($fecha_actualizacion)) ? date('Y-m-d H:i:s') : $fecha_actualizacion;
            $stmt->bind_param('ssssi', $nombre_categoria, $descripcion, $estado, $fa, $id_categoria);
            return $stmt->execute();
        }
        return false;
    }

    public function cambiarEstado(int $id_categoria, string $nuevo_estado, string $fecha_actualizacion = 'now')
    {
        $query = "UPDATE categorias SET estado = ?, fecha_actualizacion = ? WHERE id_categoria = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $fa = ($fecha_actualizacion === 'now' || empty($fecha_actualizacion)) ? date('Y-m-d H:i:s') : $fecha_actualizacion;
            $stmt->bind_param('ssi', $nuevo_estado, $fa, $id_categoria);
            return $stmt->execute();
        }
        return false;
    }
}
