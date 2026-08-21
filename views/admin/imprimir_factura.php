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
        <?php if (!empty($factura['empresa_logo'])): ?>
            <img src="../../public/uploads/<?= htmlspecialchars($factura['empresa_logo']) ?>" alt="Logo Empresa" style="max-width: 120px; max-height: 60px; margin-bottom: 5px;">
        <?php endif; ?>
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
        <p class="mb-1">Documento Electrónico de Venta</p>
        
        <?php if (!empty($factura['cufe'])): ?>
            <hr>
            <div class="mb-1"><strong>CUFE:</strong></div>
            <div style="word-break: break-all; font-size: 9px; line-height: 1.2; margin-bottom: 10px;">
                <?= htmlspecialchars($factura['cufe']) ?>
            </div>
            
            <?php if (!empty($factura['codigo_qr'])): ?>
                <!-- Generamos el QR usando la API pública gratuita -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode($factura['codigo_qr']) ?>" alt="QR Code DIAN" style="width: 120px; height: 120px;">
            <?php endif; ?>
            
            <p style="font-size: 9px; margin-top: 5px;">Validado por la DIAN el <?= date('d/m/Y H:i:s', strtotime($factura['fecha_validacion_dian'])) ?></p>
        <?php else: ?>
            <p>Software FactuWeb PRO</p>
        <?php endif; ?>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center; background: #f3f4f6; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
        <h4 style="margin:0 0 10px 0; font-size: 14px; color: #111827;">🎙️ Lectura Asistida (Accesibilidad)</h4>
        <div style="margin-bottom: 12px;">
            <label for="speechSpeed" style="font-size: 12px; color: #4b5563;">Velocidad de voz:</label>
            <input type="range" id="speechSpeed" min="0.5" max="2" step="0.1" value="1" style="vertical-align: middle;">
        </div>
        <button id="btnReadSummary" style="padding: 8px 12px; margin: 2px; cursor: pointer; background: #ea580c; color: white; border: none; border-radius: 5px; font-weight: bold;" aria-label="Leer resumen de totales y estado legal">
            ▶️ Escuchar Resumen
        </button>
        <button id="btnReadItems" style="padding: 8px 12px; margin: 2px; cursor: pointer; background: #12102f; color: white; border: none; border-radius: 5px; font-weight: bold;" aria-label="Leer detalle de productos">
            📦 Leer Ítems
        </button>
        <button id="btnStopSpeech" style="padding: 8px 12px; margin: 2px; cursor: pointer; background: #dc2626; color: white; border: none; border-radius: 5px; font-weight: bold;" aria-label="Detener lectura">
            ⏹️ Detener
        </button>
    </div>

    <div class="text-center mt-3 no-print">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #2563eb; color: white; border: none; border-radius: 5px;">Imprimir / Guardar PDF</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; background: #e5e7eb; color: #374151; border: none; border-radius: 5px; margin-left: 10px;">Cerrar</button>
    </div>

    <!-- Script de Síntesis de Voz (Lectura Asistida HU-012) -->
    <script>
        const synth = window.speechSynthesis;
        const speedControl = document.getElementById('speechSpeed');

        function speak(text) {
            synth.cancel(); // Stop any ongoing speech
            const utterThis = new SpeechSynthesisUtterance(text);
            utterThis.lang = 'es-CO'; // Español Colombia (o genérico 'es')
            utterThis.rate = parseFloat(speedControl.value);
            synth.speak(utterThis);
        }

        document.getElementById('btnReadSummary').addEventListener('click', () => {
            const total = "<?= number_format($factura['total_pagar'], 0, '', '') ?>";
            const subtotal = "<?= number_format($factura['subtotal'], 0, '', '') ?>";
            const iva = "<?= number_format($factura['total_iva'], 0, '', '') ?>";
            const isDian = <?= !empty($factura['cufe']) ? 'true' : 'false' ?>;
            
            let text = `Resumen de su compra en <?= addslashes($factura['nombre_empresa'] ?? 'Nuestra Empresa') ?>. `;
            text += `El total a pagar es de ${total} pesos. `;
            text += `El subtotal es ${subtotal} pesos, y el impuesto IVA es de ${iva} pesos. `;
            if (isDian) {
                text += `Este documento es una factura electrónica de venta, validada legalmente por la DIAN bajo el número consecutivo <?= addslashes($factura['prefijo_resolucion'] . $factura['consecutivo']) ?>.`;
            }
            speak(text);
        });

        document.getElementById('btnReadItems').addEventListener('click', () => {
            let text = `Usted ha comprado los siguientes artículos: `;
            <?php foreach($detalles as $item): ?>
            text += `<?= $item['cantidad'] ?> unidades de <?= addslashes($item['nombre_producto']) ?>. `;
            <?php endforeach; ?>
            speak(text);
        });

        document.getElementById('btnStopSpeech').addEventListener('click', () => {
            synth.cancel();
        });
    </script>

    <!-- Script para descargar/imprimir automáticamente al abrir -->
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
