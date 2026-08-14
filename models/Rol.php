<?php
require_once __DIR__ . '/../config/database.php';

class Rol
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function obtenerTodos()
    {
        $query = 'SELECT id_rol, nombre_rol AS rol FROM roles';
        $result = $this->conn->query($query);
        $roles = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $roles[] = $row;
            }
        }
        return $roles;
    }
}