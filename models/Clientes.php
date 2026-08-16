<?php
require_once __DIR__ . '/../config/database.php';

class Cliente
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function obtenerTodos()
    {
        $query = 'SELECT id_cliente, identificacion, nombre_razon_social, email, direccion, telefono FROM clientes ORDER BY id_cliente DESC';
        $result = $this->conn->query($query);
        $clientes = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $clientes[] = $row;
            }
        }
        return $clientes;
    }

    public function buscar($termino)
    {
        $query = 'SELECT id_cliente, identificacion, nombre_razon_social, email, direccion, telefono FROM clientes WHERE identificacion LIKE ? OR nombre_razon_social LIKE ? ORDER BY id_cliente DESC';
        $stmt = $this->conn->prepare($query);
        $likeTermino = "%" . $termino . "%";
        $stmt->bind_param('ss', $likeTermino, $likeTermino);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $clientes = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $clientes[] = $row;
            }
        }
        return $clientes;
    }

    public function obtenerPorIdentificacion($identificacion, $exclude_id = null)
    {
        if ($exclude_id) {
            $query = 'SELECT id_cliente FROM clientes WHERE identificacion = ? AND id_cliente != ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('si', $identificacion, $exclude_id);
        } else {
            $query = 'SELECT id_cliente FROM clientes WHERE identificacion = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('s', $identificacion);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function obtenerPorId($id_cliente)
    {
        $query = 'SELECT id_cliente, identificacion, nombre_razon_social, email, direccion, telefono FROM clientes WHERE id_cliente = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id_cliente);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function insertar($identificacion, $nombre_razon_social, $email, $direccion, $telefono)
    {
        $query = "INSERT INTO clientes (identificacion, nombre_razon_social, email, direccion, telefono) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssss', $identificacion, $nombre_razon_social, $email, $direccion, $telefono);
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function actualizar($id_cliente, $identificacion, $nombre_razon_social, $email, $direccion, $telefono)
    {
        $query = "UPDATE clientes SET identificacion = ?, nombre_razon_social = ?, email = ?, direccion = ?, telefono = ? WHERE id_cliente = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssssi', $identificacion, $nombre_razon_social, $email, $direccion, $telefono, $id_cliente);
        $stmt->execute();
        return $stmt->affected_rows;
    }
    
    public function eliminar($id_cliente)
    {
        $query = "DELETE FROM clientes WHERE id_cliente = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id_cliente);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}