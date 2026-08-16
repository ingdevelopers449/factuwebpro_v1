<?php
require_once __DIR__ . '/../config/database.php';

class Empresa
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function obtenerTodos()
    {
        $query = 'SELECT id_empresa, nit, razon_social, direccion, telefono, email as correo, logo_url as logo FROM empresa';
        $result = $this->conn->query($query);
        $empresas = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $empresas[] = $row;
            }
        }
        return $empresas;
    }

    public function insertar($nit, $razon_social, $direccion, $telefono, $correo, $logo)
    {
        $query = "INSERT INTO empresa (nit, razon_social, direccion, telefono, email, logo_url) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssssss', $nit, $razon_social, $direccion, $telefono, $correo, $logo);
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function actualizar($nit, $razon_social, $direccion, $telefono, $correo, $logo)
    {
        $query = "UPDATE empresa SET nit = ?, razon_social = ?, direccion = ?, telefono = ?, email = ?, logo_url = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssssss', $nit, $razon_social, $direccion, $telefono, $correo, $logo);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}