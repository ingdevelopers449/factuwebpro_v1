<?php
require_once __DIR__ . '/../config/database.php';

class Factura
{
    private \mysqli $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    /**
     * Procesa la creación completa de una factura (Maestro-Detalle) usando Transacciones
     */
    public function crearFactura(int $id_empresa, ?int $id_cliente, ?int $id_usuario, float $subtotal, float $total_iva, float $total_pagar, array $detalles): array
    {
        // 1. Iniciar Transacción
        $this->conn->begin_transaction();

        try {
            // 2. Obtener resolución activa
            $queryRes = "SELECT id_resolucion, prefijo, contador_actual, rango_final FROM resolucion_dian WHERE id_empresa = ? AND estado = 'activa' LIMIT 1";
            $stmtRes = $this->conn->prepare($queryRes);
            $stmtRes->bind_param('i', $id_empresa);
            $stmtRes->execute();
            $resultRes = $stmtRes->get_result();

            if ($resultRes->num_rows === 0) {
                throw new Exception("No hay una resolución DIAN activa para facturar.");
            }

            $resolucion = $resultRes->fetch_assoc();
            $id_resolucion = $resolucion['id_resolucion'];
            $prefijo_resolucion = $resolucion['prefijo'];
            $consecutivo = $resolucion['contador_actual'];

            if ($consecutivo > $resolucion['rango_final']) {
                throw new Exception("La resolución DIAN ha alcanzado su rango final. No se puede facturar.");
            }

            // 3. Insertar Factura Maestra
            $queryFac = "INSERT INTO facturas (id_empresa, id_resolucion, prefijo_resolucion, consecutivo, id_cliente, id_usuario, subtotal, total_iva, total_pagar) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtFac = $this->conn->prepare($queryFac);
            $stmtFac->bind_param('iisiiiddd', $id_empresa, $id_resolucion, $prefijo_resolucion, $consecutivo, $id_cliente, $id_usuario, $subtotal, $total_iva, $total_pagar);
            $stmtFac->execute();
            $id_factura = $stmtFac->insert_id;

            // 4. Insertar Detalles y Descontar Stock
            $queryDet = "INSERT INTO detalle_factura (id_factura, id_producto, cantidad, precio_unitario_venta, precio_unitario_costo, subtotal_linea) 
                         VALUES (?, ?, ?, ?, ?, ?)";
            $stmtDet = $this->conn->prepare($queryDet);

            $queryStock = "UPDATE productos SET stock_actual = stock_actual - ? WHERE id_producto = ?";
            $stmtStock = $this->conn->prepare($queryStock);

            $queryCosto = "SELECT precio_compra FROM productos WHERE id_producto = ?";
            $stmtCosto = $this->conn->prepare($queryCosto);

            foreach ($detalles as $detalle) {
                $id_producto = (int)$detalle['id_producto'];
                $cantidad = (int)$detalle['cantidad'];
                $precio_unitario_venta = (float)$detalle['precio'];
                $subtotal_linea = $cantidad * $precio_unitario_venta;

                // Obtener costo real del producto de la base de datos (seguridad)
                $stmtCosto->bind_param('i', $id_producto);
                $stmtCosto->execute();
                $resCosto = $stmtCosto->get_result();
                $precio_unitario_costo = 0;
                if ($rowCosto = $resCosto->fetch_assoc()) {
                    $precio_unitario_costo = (float)$rowCosto['precio_compra'];
                }

                // Insertar Detalle
                $stmtDet->bind_param('iiiddd', $id_factura, $id_producto, $cantidad, $precio_unitario_venta, $precio_unitario_costo, $subtotal_linea);
                $stmtDet->execute();

                // Descontar Stock
                $stmtStock->bind_param('ii', $cantidad, $id_producto);
                $stmtStock->execute();
            }

            // 5. Actualizar consecutivo de la resolución
            $nuevo_consecutivo = $consecutivo + 1;
            $queryUpdateRes = "UPDATE resolucion_dian SET contador_actual = ? WHERE id_resolucion = ?";
            $stmtUpdateRes = $this->conn->prepare($queryUpdateRes);
            $stmtUpdateRes->bind_param('ii', $nuevo_consecutivo, $id_resolucion);
            $stmtUpdateRes->execute();

            // 6. Confirmar Transacción
            $this->conn->commit();
            return [
                'success' => true,
                'id_factura' => $id_factura,
                'numero_factura' => $prefijo_resolucion . $consecutivo
            ];

        } catch (Exception $e) {
            // Revertir todo si hay error
            $this->conn->rollback();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
