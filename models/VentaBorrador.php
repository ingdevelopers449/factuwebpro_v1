<?php
require_once __DIR__ . '/../config/database.php';

class VentaBorrador
{
    private mysqli $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function obtenerBorrador(int $id_usuario): ?array
    {
        // 1. Obtener la cabecera del borrador
        $query = "SELECT id_borrador, id_cliente, fecha_actualizacion FROM ventas_borrador WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return null;
        
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $borrador = $row;
            $borrador['detalles'] = [];

            // 2. Obtener los detalles
            $queryDetalles = "SELECT d.id_producto, d.cantidad, p.nombre_producto, p.precio_venta as precio, p.tarifa_iva as iva, p.stock_actual as stock_max
                              FROM detalle_borrador d
                              JOIN productos p ON d.id_producto = p.id_producto
                              WHERE d.id_borrador = ?";
            $stmtDet = $this->conn->prepare($queryDetalles);
            if ($stmtDet) {
                $stmtDet->bind_param('i', $borrador['id_borrador']);
                $stmtDet->execute();
                $resDet = $stmtDet->get_result();
                while ($det = $resDet->fetch_assoc()) {
                    // Convert numeric values properly
                    $det['id_producto'] = (int)$det['id_producto'];
                    $det['cantidad'] = (int)$det['cantidad'];
                    $det['precio'] = (float)$det['precio'];
                    $det['iva'] = (float)$det['iva'];
                    $det['stock_max'] = (int)$det['stock_max'];
                    $borrador['detalles'][] = $det;
                }
            }
            return $borrador;
        }

        return null; // No hay borrador
    }

    public function guardarBorrador(int $id_usuario, ?int $id_cliente, array $detalles): bool
    {
        $this->conn->begin_transaction();

        try {
            // 1. Guardar o actualizar cabecera (UPSERT)
            $queryCabecera = "INSERT INTO ventas_borrador (id_usuario, id_cliente) VALUES (?, ?)
                              ON DUPLICATE KEY UPDATE id_cliente = VALUES(id_cliente), fecha_actualizacion = CURRENT_TIMESTAMP";
            $stmt = $this->conn->prepare($queryCabecera);
            $stmt->bind_param('ii', $id_usuario, $id_cliente);
            $stmt->execute();

            // 2. Obtener el id_borrador
            $queryId = "SELECT id_borrador FROM ventas_borrador WHERE id_usuario = ?";
            $stmtId = $this->conn->prepare($queryId);
            $stmtId->bind_param('i', $id_usuario);
            $stmtId->execute();
            $resId = $stmtId->get_result();
            $rowId = $resId->fetch_assoc();
            $id_borrador = $rowId['id_borrador'];

            // 3. Limpiar detalles antiguos
            $queryDel = "DELETE FROM detalle_borrador WHERE id_borrador = ?";
            $stmtDel = $this->conn->prepare($queryDel);
            $stmtDel->bind_param('i', $id_borrador);
            $stmtDel->execute();

            // 4. Insertar detalles nuevos
            if (!empty($detalles)) {
                $queryIns = "INSERT INTO detalle_borrador (id_borrador, id_producto, cantidad) VALUES (?, ?, ?)";
                $stmtIns = $this->conn->prepare($queryIns);
                foreach ($detalles as $det) {
                    $id_prod = (int)$det['id_producto'];
                    $cant = (int)$det['cantidad'];
                    $stmtIns->bind_param('iii', $id_borrador, $id_prod, $cant);
                    $stmtIns->execute();
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function limpiarBorrador(int $id_usuario): bool
    {
        $query = "DELETE FROM ventas_borrador WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('i', $id_usuario);
            return $stmt->execute();
        }
        return false;
    }
}
