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

    public function obtenerSiguienteNumero(int $id_empresa): string
    {
        $queryRes = "SELECT prefijo, contador_actual FROM resolucion_dian WHERE id_empresa = ? AND estado = 'activa' LIMIT 1";
        $stmtRes = $this->conn->prepare($queryRes);
        if ($stmtRes) {
            $stmtRes->bind_param('i', $id_empresa);
            $stmtRes->execute();
            $resultRes = $stmtRes->get_result();
            if ($row = $resultRes->fetch_assoc()) {
                return $row['prefijo'] . str_pad($row['contador_actual'], 4, '0', STR_PAD_LEFT);
            }
        }
        return "N/A (Sin Resolución)";
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

            // Obtener el porcentaje_comision del usuario
            $porcentaje_comision = 0.00;
            if ($id_usuario) {
                $queryUsr = "SELECT porcentaje_comision FROM usuarios WHERE id_usuario = ?";
                $stmtUsr = $this->conn->prepare($queryUsr);
                $stmtUsr->bind_param('i', $id_usuario);
                $stmtUsr->execute();
                $resUsr = $stmtUsr->get_result();
                if ($rowUsr = $resUsr->fetch_assoc()) {
                    $porcentaje_comision = (float)$rowUsr['porcentaje_comision'];
                }
            }

            // Simular validación DIAN y rechazo (HU-004.14)
            $estado_dian = 'aceptada';
            $motivo_rechazo = null;

            if ($id_cliente) {
                $queryCli = "SELECT identificacion FROM clientes WHERE id_cliente = ?";
                $stmtCli = $this->conn->prepare($queryCli);
                $stmtCli->bind_param('i', $id_cliente);
                $stmtCli->execute();
                $resCli = $stmtCli->get_result();
                if ($rowCli = $resCli->fetch_assoc()) {
                    $identificacion = $rowCli['identificacion'];
                    // Si el NIT/Cédula tiene un formato inválido (ej. menos de 5 caracteres o caracteres extraños), simular rechazo
                    if (strlen($identificacion) < 5 || preg_match('/[^a-zA-Z0-9-]/', $identificacion)) {
                        $estado_dian = 'rechazada';
                        $motivo_rechazo = 'Regla 90: El documento de identidad o NIT no cumple con los estándares exigidos por la DIAN.';
                    }
                }
            }

            // Simular Integración DIAN (Generación de CUFE y QR)
            $cufe_data = $id_empresa . $prefijo_resolucion . $consecutivo . $total_pagar . date('YmdHis');
            $cufe = hash('sha384', $cufe_data); // Simulamos el hash real de la DIAN
            $codigo_qr = "https://catalogo-vpfe.dian.gov.co/document/searchqr?documentkey=" . $cufe;
            $fecha_validacion_dian = date('Y-m-d H:i:s');

            // 3. Insertar Factura Maestra con Datos DIAN
            $queryFac = "INSERT INTO facturas (id_empresa, id_resolucion, prefijo_resolucion, consecutivo, id_cliente, id_usuario, subtotal, total_iva, total_pagar, cufe, codigo_qr, estado_dian, motivo_rechazo, fecha_validacion_dian, porcentaje_comision_aplicado) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtFac = $this->conn->prepare($queryFac);
            $stmtFac->bind_param('iisiiidddsssssd', $id_empresa, $id_resolucion, $prefijo_resolucion, $consecutivo, $id_cliente, $id_usuario, $subtotal, $total_iva, $total_pagar, $cufe, $codigo_qr, $estado_dian, $motivo_rechazo, $fecha_validacion_dian, $porcentaje_comision);
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

    public function obtenerFacturaPorId(int $id_factura): ?array
    {
        $queryFac = "
            SELECT f.*, 
                   c.nombre_razon_social as cliente_nombre, 
                   c.identificacion as cliente_identificacion,
                   e.razon_social as nombre_empresa,
                   e.nit as empresa_nit,
                   e.direccion as empresa_direccion,
                   e.telefono as empresa_telefono,
                   e.logo_url as empresa_logo,
                   u.nombre as vendedor
            FROM facturas f
            LEFT JOIN clientes c ON f.id_cliente = c.id_cliente
            LEFT JOIN empresa e ON f.id_empresa = e.id_empresa
            LEFT JOIN usuarios u ON f.id_usuario = u.id_usuario
            WHERE f.id_factura = ?
        ";
        $stmtFac = $this->conn->prepare($queryFac);
        if ($stmtFac) {
            $stmtFac->bind_param('i', $id_factura);
            $stmtFac->execute();
            $result = $stmtFac->get_result();
            if ($row = $result->fetch_assoc()) {
                return $row;
            }
        }
        return null;
    }

    public function obtenerDetallesFactura(int $id_factura): array
    {
        $queryDet = "
            SELECT d.*, p.nombre_producto, p.codigo_barras
            FROM detalle_factura d
            JOIN productos p ON d.id_producto = p.id_producto
            WHERE d.id_factura = ?
        ";
        $stmtDet = $this->conn->prepare($queryDet);
        if ($stmtDet) {
            $stmtDet->bind_param('i', $id_factura);
            $stmtDet->execute();
            $result = $stmtDet->get_result();
            $detalles = [];
            while ($row = $result->fetch_assoc()) {
                $detalles[] = $row;
            }
            return $detalles;
        }
        return [];
    }

    public function obtenerVentasPorUsuario(int $id_usuario, string $fecha_inicio = null, string $fecha_fin = null): array
    {
        $query = "
            SELECT f.id_factura, f.prefijo_resolucion, f.consecutivo, f.fecha_emision, f.total_pagar, 
                   c.nombre_razon_social as cliente_nombre, c.identificacion as cliente_identificacion
            FROM facturas f
            LEFT JOIN clientes c ON f.id_cliente = c.id_cliente
            WHERE f.id_usuario = ?
        ";
        
        $params = [$id_usuario];
        $types = "i";

        if ($fecha_inicio && $fecha_fin) {
            $query .= " AND DATE(f.fecha_emision) BETWEEN ? AND ?";
            $params[] = $fecha_inicio;
            $params[] = $fecha_fin;
            $types .= "ss";
        } elseif ($fecha_inicio) {
            $query .= " AND DATE(f.fecha_emision) >= ?";
            $params[] = $fecha_inicio;
            $types .= "s";
        }

        $query .= " ORDER BY f.fecha_emision DESC";

        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $ventas = [];
            while ($row = $result->fetch_assoc()) {
                $ventas[] = $row;
            }
            return $ventas;
        }
        return [];
    }
}
