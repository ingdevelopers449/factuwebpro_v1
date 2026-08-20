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
        $query = "SELECT id_categoria, nombre_categoria, descripcion, estado, fecha_creacion FROM categorias ORDER BY nombre_categoria ASC";
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

    public function insertar(string $nombre_categoria, string $descripcion, string $estado)
    {
        $query = "INSERT INTO categorias (nombre_categoria, descripcion, estado) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('sss', $nombre_categoria, $descripcion, $estado);
            return $stmt->execute() ? $stmt->insert_id : false;
        }
        return false;
    }

    public function actualizar(int $id_categoria, string $nombre_categoria, string $descripcion, string $estado)
    {
        $query = "UPDATE categorias SET nombre_categoria = ?, descripcion = ?, estado = ? WHERE id_categoria = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('sssi', $nombre_categoria, $descripcion, $estado, $id_categoria);
            return $stmt->execute();
        }
        return false;
    }

    public function cambiarEstado(int $id_categoria, string $nuevo_estado)
    {
        $query = "UPDATE categorias SET estado = ? WHERE id_categoria = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('si', $nuevo_estado, $id_categoria);
            return $stmt->execute();
        }
        return false;
    }
}
