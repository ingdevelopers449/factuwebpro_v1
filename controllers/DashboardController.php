<?php
require_once __DIR__ . '/../models/Factura.php';
require_once __DIR__ . '/../config/database.php';

class DashboardController
{
    public function index(int $pagina_facturas = 1, int $por_pagina = 8): array
    {
        global $conn;

        $pagina_facturas = max(1, $pagina_facturas);
        $por_pagina = max(1, $por_pagina);
        $offset_facturas = ($pagina_facturas - 1) * $por_pagina;

        // --- KPI: Ventas del día (total_pagar) ---
        $hoy = date('Y-m-d');
        $stmt = $conn->prepare("SELECT COALESCE(SUM(total_pagar),0) AS ventas_hoy, COUNT(*) AS facturas_hoy FROM facturas WHERE DATE(fecha_emision) = ?");
        $stmt->bind_param('s', $hoy);
        $stmt->execute();
        $kpi_dia = $stmt->get_result()->fetch_assoc();

        // --- KPI: Ventas del mes ---
        $mes = date('Y-m');
        $stmt2 = $conn->prepare("SELECT COALESCE(SUM(total_pagar),0) AS ventas_mes FROM facturas WHERE DATE_FORMAT(fecha_emision,'%Y-%m') = ?");
        $stmt2->bind_param('s', $mes);
        $stmt2->execute();
        $kpi_mes = $stmt2->get_result()->fetch_assoc();

        // --- KPI: Productos con stock bajo (< 5) ---
        $stmt3 = $conn->prepare("SELECT COUNT(*) AS productos_bajos FROM productos WHERE stock_actual < 5 AND estado_producto = 'activo'");
        $stmt3->execute();
        $kpi_stock = $stmt3->get_result()->fetch_assoc();

        // --- KPI: Total clientes registrados ---
        $stmt4 = $conn->prepare("SELECT COUNT(*) AS total_clientes FROM clientes");
        $stmt4->execute();
        $kpi_clientes = $stmt4->get_result()->fetch_assoc();

        // --- Facturas recientes paginadas ---
        $stmtTotalFacturas = $conn->prepare("SELECT COUNT(*) AS total FROM facturas");
        $stmtTotalFacturas->execute();
        $total_facturas = (int)$stmtTotalFacturas->get_result()->fetch_assoc()['total'];

        $stmt5 = $conn->prepare("
            SELECT f.id_factura, f.prefijo_resolucion, f.consecutivo,
                   f.fecha_emision, f.total_pagar, f.estado_dian,
                   COALESCE(c.nombre_razon_social, 'Consumidor Final') AS cliente_nombre,
                   u.nombre AS vendedor
            FROM facturas f
            LEFT JOIN clientes c ON f.id_cliente = c.id_cliente
            LEFT JOIN usuarios u ON f.id_usuario = u.id_usuario
            ORDER BY f.fecha_emision DESC
            LIMIT ? OFFSET ?
        ");
        $stmt5->bind_param('ii', $por_pagina, $offset_facturas);
        $stmt5->execute();
        $ultimas_facturas = $stmt5->get_result()->fetch_all(MYSQLI_ASSOC);

        // --- Gráfica: Ventas últimos 7 días ---
        $stmt6 = $conn->prepare("
            SELECT DATE(fecha_emision) AS dia, COALESCE(SUM(total_pagar),0) AS total
            FROM facturas
            WHERE fecha_emision >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY DATE(fecha_emision)
            ORDER BY dia ASC
        ");
        $stmt6->execute();
        $rows = $stmt6->get_result()->fetch_all(MYSQLI_ASSOC);

        // Rellenar días sin ventas con 0
        $grafica_labels = [];
        $grafica_valores = [];
        $dias_map = [];
        foreach ($rows as $r) { $dias_map[$r['dia']] = $r['total']; }
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $grafica_labels[] = date('d/m', strtotime($d));
            $grafica_valores[] = (float)($dias_map[$d] ?? 0);
        }

        // --- Top 5 productos más vendidos ---
        $stmt7 = $conn->prepare("
            SELECT p.nombre_producto, SUM(df.cantidad) AS total_vendido
            FROM detalle_factura df
            JOIN productos p ON df.id_producto = p.id_producto
            GROUP BY df.id_producto
            ORDER BY total_vendido DESC
            LIMIT 5
        ");
        $stmt7->execute();
        $top_productos = $stmt7->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'ventas_hoy'      => (float)$kpi_dia['ventas_hoy'],
            'facturas_hoy'    => (int)$kpi_dia['facturas_hoy'],
            'ventas_mes'      => (float)$kpi_mes['ventas_mes'],
            'productos_bajos' => (int)$kpi_stock['productos_bajos'],
            'total_clientes'  => (int)$kpi_clientes['total_clientes'],
            'ultimas_facturas'=> $ultimas_facturas,
            'pagina_facturas' => $pagina_facturas,
            'total_facturas'  => $total_facturas,
            'total_paginas_facturas' => (int)ceil($total_facturas / $por_pagina),
            'grafica_labels'  => $grafica_labels,
            'grafica_valores' => $grafica_valores,
            'top_productos'   => $top_productos,
        ];
    } 
}
