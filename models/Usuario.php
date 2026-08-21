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

    public function registrar(int $id_empresa, string $nombre, string $email, string $password, int $id_rol, string $estado, float $porcentaje_comision = 0.00)
    {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $query = 'INSERT INTO usuarios (id_empresa, nombre, email, password_hash, id_rol, estado, porcentaje_comision) VALUES (?, ?, ?, ?, ?, ?, ?)';

        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('isssisd', $id_empresa, $nombre, $email, $hashed_password, $id_rol, $estado, $porcentaje_comision);
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
        $query = 'SELECT id_usuario, id_empresa, nombre, email, password_hash, id_rol, estado, intentos_fallidos, ultimo_acceso FROM usuarios WHERE email = ?';
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

    public function registrarIntentoFallido(int $id_usuario, int $intentos_actuales)
    {
        $nuevos_intentos = $intentos_actuales + 1;
        $estado = ($nuevos_intentos >= 3) ? 'bloqueado' : 'activo';
        
        $query = 'UPDATE usuarios SET intentos_fallidos = ?, estado = ?, ultimo_acceso = NOW() WHERE id_usuario = ?';
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('isi', $nuevos_intentos, $estado, $id_usuario);
            $stmt->execute();
            $stmt->close();
        }
    }

    public function guardarTokenRecuperacion(string $email, string $token, string $expiracion)
    {
        $query = 'UPDATE usuarios SET token_recuperacion = ?, token_expiracion = ? WHERE email = ?';
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('sss', $token, $expiracion, $email);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    public function obtenerPorToken(string $token)
    {
        $query = 'SELECT id_usuario, email, token_expiracion FROM usuarios WHERE token_recuperacion = ?';
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $result = $stmt->get_result();
            $usuario = $result->fetch_assoc();
            $stmt->close();
            return $usuario;
        }
        return null;
    }

    public function actualizarPassword(int $id_usuario, string $nueva_password)
    {
        $hashed = password_hash($nueva_password, PASSWORD_BCRYPT);
        // Al actualizar, se invalida el token
        $query = 'UPDATE usuarios SET password_hash = ?, token_recuperacion = NULL, token_expiracion = NULL WHERE id_usuario = ?';
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('si', $hashed, $id_usuario);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    public function resetearIntentosYActualizarAcceso(int $id_usuario)
    {
        $query = 'UPDATE usuarios SET intentos_fallidos = 0, estado = "activo", ultimo_acceso = NOW() WHERE id_usuario = ?';
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('i', $id_usuario);
            $stmt->execute();
            $stmt->close();
        }
    }

    public function obtenerTodos()
    {
        $query = 'SELECT u.id_usuario, u.nombre, u.email, u.id_rol, r.nombre_rol, u.estado, u.porcentaje_comision
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
        $query = 'UPDATE usuarios SET nombre = ?, id_rol = ?, estado = ?, porcentaje_comision = ?';
        $types = 'sisd';
        $params = [$datos['nombre'], $datos['id_rol'], $datos['estado'], $datos['porcentaje_comision'] ?? 0.00];

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