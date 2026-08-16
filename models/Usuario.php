<?php
require_once __DIR__ . '/../config/database.php';

class Usuario
{
    private \mysqli $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function registrar(int $id_empresa, string $nombre, string $email, string $password, int $id_rol, string $estado)
    {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $query = 'INSERT INTO usuarios (id_empresa, nombre, email, password_hash, id_rol, estado) VALUES (?, ?, ?, ?, ?, ?)';

        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('isssis', $id_empresa, $nombre, $email, $hashed_password, $id_rol, $estado);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    public function emailExiste(string $email)
    {
        $query = 'SELECT id_usuario FROM usuarios WHERE email = ?';
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->num_rows;
            $stmt->close();
            return $num_rows > 0;
        }
        return false;
    }

    public function obtenerPorEmail(string $email)
    {
        $query = 'SELECT id_usuario, email, password_hash, id_rol, estado FROM usuarios WHERE email = ?';
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $usuario = $result->fetch_assoc();
            $stmt->close();
            return $usuario;
        }
        return null;
    }

    public function obtenerTodos()
    {
        $query = 'SELECT u.id_usuario, u.nombre, u.email, u.id_rol, r.nombre_rol, u.estado 
                  FROM usuarios u 
                  LEFT JOIN roles r ON u.id_rol = r.id_rol';
        $result = $this->conn->query($query);
        $usuarios = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }
        }
        return $usuarios;
    }

    public function obtenerEstados()
    {
        return [
            'activo' => 'Activo',
            'inactivo' => 'Inactivo'
        ];
    }

    public function actualizar(int $id_usuario, array $datos)
    {
        $query = 'UPDATE usuarios SET nombre = ?, id_rol = ?, estado = ?';
        $types = 'sis';
        $params = [$datos['nombre'], $datos['id_rol'], $datos['estado']];

        if (!empty($datos['email'])) {
            $query .= ', email = ?';
            $types .= 's';
            $params[] = $datos['email'];
        }

        $query .= ' WHERE id_usuario = ?';
        $types .= 'i';
        $params[] = $id_usuario;

        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param($types, ...$params);
            $result = $stmt->execute();
            if (!$result) {
                $error = $stmt->error;
                $stmt->close();
                return 'Error en la base de datos: ' . $error;
            }
            $stmt->close();
            return true;
        }
        return 'Error al preparar la consulta de actualización.';
    }

    public function eliminar(int $id_usuario)
    {
        $query = 'DELETE FROM usuarios WHERE id_usuario = ?';
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('i', $id_usuario);
            $result = $stmt->execute();
            if (!$result) {
                $error = $stmt->error;
                $stmt->close();
                return 'Error en la base de datos al eliminar: ' . $error;
            }
            $stmt->close();
            return true;
        }
        return 'Error al preparar la consulta de eliminación.';
    }
}
?>