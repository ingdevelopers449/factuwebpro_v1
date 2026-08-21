<?php 
// Vista: Mis Ventas (Historial Empleado)
$current_page = 'mis_ventas.php';
require_once '../layouts/header.php'; 
require_once '../../models/Factura.php';

// Solo permitir acceso si hay un usuario logueado
$id_usuario = $_SESSION['usuario']['id_usuario'] ?? null;
if (!$id_usuario) {
    echo "No tiene permisos para ver esta sección.";
    exit;
}

$facturaModel = new Factura();

// Fechas por defecto: hoy
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

$ventas = $facturaModel->obtenerVentasPorUsuario($id_usuario, $fecha_inicio, $fecha_fin);

$total_ventas = 0;
foreach ($ventas as $venta) {
    $total_ventas += $venta['total_pagar'];
}

// Comisión del 5% como ejemplo (podría ser parametrizable)
$comision_estimada = $total_ventas * 0.05;

$formatMoney = new NumberFormatter('es_CO', NumberFormatter::CURRENCY);
$formatMoney->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 glass-header p-4 rounded-4 shadow-sm">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1"><i class="fa-solid fa-list-check me-2" style="color: #f59e0b;"></i> <span style="color: #12102f;">Mis Ventas</span></h2>
            <p class="text-muted small mb-0">Historial de facturas generadas por ti</p>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <button class="btn btn-outline-secondary rounded-pill shadow-sm" onclick="window.print()">
                <i class="fa-solid fa-print me-1"></i> Imprimir Reporte
            </button>
            <a href="facturas.php" class="btn btn-super-cta text-white rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-plus me-1"></i> Nueva Venta
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 bg-white">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted fw-bold small">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control bg-light" value="<?= htmlspecialchars($fecha_inicio) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-bold small">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control bg-light" value="<?= htmlspecialchars($fecha_fin) ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm"><i class="fa-solid fa-filter me-2"></i> Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Indicadores -->
    <div class="row mb-4 g-3">
        <div class="col-md-6">
            <div class="card border-0 rounded-4 shadow-sm h-100 position-relative overflow-hidden" style="background: linear-gradient(135deg, #12102f 0%, #1e1b4b 100%);">
                <div class="position-absolute" style="right: -20px; top: -20px; opacity: 0.1;">
                    <i class="fa-solid fa-cash-register" style="font-size: 8rem; color: #fff;"></i>
                </div>
                <div class="card-body p-4">
                    <h6 class="text-white-50 fw-bold mb-1 text-uppercase" style="letter-spacing: 1px;">Total Vendido (Periodo)</h6>
                    <h2 class="text-white fw-bold mb-0"><?= $formatMoney->format($total_ventas) ?></h2>
                    <p class="text-white-50 small mt-2 mb-0"><i class="fa-solid fa-chart-line text-success me-1"></i> <?= count($ventas) ?> facturas emitidas</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 rounded-4 shadow-sm h-100 position-relative overflow-hidden" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);">
                <div class="position-absolute" style="right: -20px; top: -20px; opacity: 0.1;">
                    <i class="fa-solid fa-hand-holding-dollar" style="font-size: 8rem; color: #fff;"></i>
                </div>
                <div class="card-body p-4">
                    <h6 class="text-white-50 fw-bold mb-1 text-uppercase" style="letter-spacing: 1px;">Comisión Estimada (5%)</h6>
                    <h2 class="text-white fw-bold mb-0"><?= $formatMoney->format($comision_estimada) ?></h2>
                    <p class="text-white-50 small mt-2 mb-0"><i class="fa-solid fa-check-circle text-white me-1"></i> Tu incentivo por ventas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card border-0 rounded-4 shadow-sm bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3 border-bottom-0">N° Factura</th>
                            <th class="py-3 border-bottom-0">Fecha</th>
                            <th class="py-3 border-bottom-0">Cliente</th>
                            <th class="py-3 border-bottom-0">CC/NIT</th>
                            <th class="text-end pe-4 py-3 border-bottom-0">Total</th>
                            <th class="text-center py-3 border-bottom-0">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php if (empty($ventas)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fs-1 mb-3 d-block text-black-50"></i>
                                    No tienes ventas registradas en este periodo.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ventas as $v): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    <?= htmlspecialchars($v['prefijo_resolucion'] . $v['consecutivo']) ?>
                                </td>
                                <td>
                                    <div class="text-dark"><?= date('d/m/Y', strtotime($v['fecha_emision'])) ?></div>
                                    <div class="text-muted small"><?= date('H:i A', strtotime($v['fecha_emision'])) ?></div>
                                </td>
                                <td><?= htmlspecialchars($v['cliente_nombre'] ?? 'Consumidor Final') ?></td>
                                <td><?= htmlspecialchars($v['cliente_identificacion'] ?? 'N/A') ?></td>
                                <td class="text-end pe-4 fw-bold text-success">
                                    <?= $formatMoney->format($v['total_pagar']) ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-light border text-primary rounded-circle" title="Ver Detalle" onclick="verFactura(<?= $v['id_factura'] ?>)">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver factura -->
<div class="modal fade" id="modalVerFactura" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom px-4 py-3 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-file-lines me-2 text-primary"></i> Detalle de Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="iframeFactura" src="" style="width: 100%; height: 500px; border: none;"></iframe>
            </div>
            <div class="modal-footer border-top px-4 py-3">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function verFactura(id_factura) {
    document.getElementById('iframeFactura').src = '../../controllers/FacturaController.php?action=imprimir&id=' + id_factura;
    var modal = new bootstrap.Modal(document.getElementById('modalVerFactura'));
    modal.show();
}
</script>

<?php require_once '../layouts/footer.php'; ?>
