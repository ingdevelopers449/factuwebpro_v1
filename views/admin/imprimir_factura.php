<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario']) || !isset($factura) || !isset($detalles)) {
    die("Acceso denegado o datos no disponibles.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura <?= $factura['prefijo_resolucion'] . $factura['consecutivo'] ?></title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body class="ticket-pos">

    <div class="text-center mb-2">
        <h2 class="mb-1" style="font-size: 16px; margin-top: 0;"><?= htmlspecialchars($factura['nombre_empresa'] ?? 'Mi Empresa') ?></h2>
        <div class="mb-1">NIT: <?= htmlspecialchars($factura['empresa_nit'] ?? '000000000-0') ?></div>
        <div class="mb-1"><?= htmlspecialchars($factura['empresa_direccion'] ?? 'Dirección no registrada') ?></div>
        <div class="mb-2">Tel: <?= htmlspecialchars($factura['empresa_telefono'] ?? 'N/A') ?></div>
        
        <hr>
        
        <div class="fw-bold fs-14">FACTURA DE VENTA (POS)</div>
        <div class="fw-bold" style="font-size: 14px; margin-top: 5px;">No. <?= $factura['prefijo_resolucion'] . $factura['consecutivo'] ?></div>
        <div>Fecha: <?= date('d/m/Y H:i:s', strtotime($factura['fecha_emision'])) ?></div>
    </div>

    <hr>

    <div class="mb-2">
        <div><strong>Cliente:</strong> <?= htmlspecialchars($factura['cliente_nombre'] ?? 'Consumidor Final') ?></div>
        <div><strong>CC/NIT:</strong> <?= htmlspecialchars($factura['cliente_identificacion'] ?? '222222222222') ?></div>
        <div><strong>Cajero:</strong> <?= htmlspecialchars($factura['vendedor'] ?? 'Admin') ?></div>
    </div>

    <hr>

    <table>
        <thead>
            <tr>
                <th class="text-left">Cant</th>
                <th class="text-left">Producto</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($detalles as $item): ?>
            <tr>
                <td class="text-left"><?= $item['cantidad'] ?></td>
                <td class="text-left">
                    <span class="producto-nombre"><?= htmlspecialchars($item['nombre_producto']) ?></span>
                    <small><?= $formatMoney->format($item['precio_unitario_venta']) ?></small>
                </td>
                <td class="text-right"><?= $formatMoney->format($item['subtotal_linea']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <hr>

    <table>
        <tr>
            <td class="text-right fw-bold">Subtotal:</td>
            <td class="text-right" style="width: 80px;"><?= $formatMoney->format($factura['subtotal']) ?></td>
        </tr>
        <tr>
            <td class="text-right fw-bold">IVA:</td>
            <td class="text-right"><?= $formatMoney->format($factura['total_iva']) ?></td>
        </tr>
        <tr>
            <td class="text-right fw-bold" style="font-size: 14px; padding-top: 5px;">TOTAL A PAGAR:</td>
            <td class="text-right fw-bold" style="font-size: 14px; padding-top: 5px;"><?= $formatMoney->format($factura['total_pagar']) ?></td>
        </tr>
    </table>

    <hr>

    <div class="text-center mt-2" style="font-size: 11px;">
        <p class="mb-1">¡Gracias por su compra!</p>
        <p>Software FactuWeb PRO</p>
    </div>

    <div class="text-center mt-2 no-print">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #2563eb; color: white; border: none; border-radius: 5px;">Imprimir / Guardar PDF</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; background: #e5e7eb; color: #374151; border: none; border-radius: 5px; margin-left: 10px;">Cerrar</button>
    </div>

    <!-- Script para descargar/imprimir automáticamente al abrir -->
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
