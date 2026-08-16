<?php
require_once __DIR__ . '/../config/database.php';

class ResolucionDian
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function obtenerTodos()
    {
        $query = 'SELECT * FROM resolucion_dian';
        $result = $this->conn->query($query);
        $resoluciones = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $resoluciones[] = $row;
            }
        }
        return $resoluciones;
    }

    public function insertar($id_empresa, $numero_resolucion, $fecha_vigencia, $prefijo, $rango_inicial, $rango_final, $contador_actual, $estado)
    {
        $query = "INSERT INTO resolucion_dian (id_empresa, numero_resolucion, fecha_vigencia, prefijo, rango_inicial, rango_final, contador_actual, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('isssiiis', $id_empresa, $numero_resolucion, $fecha_vigencia, $prefijo, $rango_inicial, $rango_final, $contador_actual, $estado);
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function actualizar($id_empresa, $numero_resolucion, $fecha_vigencia, $prefijo, $rango_inicial, $rango_final, $contador_actual, $estado)
    {
        $query = "UPDATE resolucion_dian SET numero_resolucion = ?, fecha_vigencia = ?, prefijo = ?, rango_inicial = ?, rango_final = ?, contador_actual = ?, estado = ? WHERE id_empresa = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssiiisi', $numero_resolucion, $fecha_vigencia, $prefijo, $rango_inicial, $rango_final, $contador_actual, $estado, $id_empresa);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}