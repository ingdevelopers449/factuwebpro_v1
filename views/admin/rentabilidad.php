<?php
$current_page = 'rentabilidad.php';
require_once '../../controllers/RentabilidadController.php';

$controller = new RentabilidadController();
extract($controller->index());

// Formateadores visuales
$formatMoney = new NumberFormatter('es_CO', NumberFormatter::CURRENCY);
$formatMoney->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

require_once '../layouts/header.php';
?>

<div class="container-fluid py-4" style="max-width: 1400px;">
    
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 glass-header p-4 rounded-4 shadow-sm">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1 text-uppercase" style="font-family: var(--font-heading);">
                <i class="fa-solid fa-chart-line me-2" style="color: #f59e0b;"></i> <span style="color: #12102f;">Ventas y Rentabilidad</span>
            </h2>
            <p class="text-muted small mb-0">Análisis financiero y márgenes de utilidad del negocio (Sólo Administrador)</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary px-4 btn-print shadow-sm">
                <i class="fa-solid fa-print me-2"></i> Exportar / Imprimir
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 filters-section">
        <div class="card-body p-4">
            <form action="rentabilidad.php" method="GET" class="row align-items-end g-3">
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold text-uppercase">Desde</label>
                    <input type="date" name="desde" class="form-control bg-light border-0" value="<?= htmlspecialchars($desde) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold text-uppercase">Hasta</label>
                    <input type="date" name="hasta" class="form-control bg-light border-0" value="<?= htmlspecialchars($hasta) ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm"><i class="fa-solid fa-filter me-2"></i> Aplicar Filtros</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($total_ventas == 0): ?>
        <div class="alert alert-warning border-0 shadow-sm rounded-4 d-flex align-items-center p-4 mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation fs-3 me-3 text-warning"></i>
            <div>
                <h5 class="alert-heading fw-bold mb-1">No hay datos suficientes</h5>
                <p class="mb-0">No se registraron ventas en el periodo del <strong><?= date('d/m/Y', strtotime($desde)) ?></strong> al <strong><?= date('d/m/Y', strtotime($hasta)) ?></strong>.</p>
            </div>
        </div>
    <?php else: ?>

        <!-- Alertas de Margen Negativo -->
        <?php if ($utilidad_neta <= 0): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-center p-4 mb-4" role="alert">
                <i class="fa-solid fa-skull-crossbones fs-3 me-3 text-danger"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">¡Alerta de Pérdidas!</h5>
                    <p class="mb-0">El margen de utilidad neta global es negativo o cero. Los costos de los productos vendidos han superado o igualado los ingresos.</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- KPIs -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card shadow-sm h-100 kpi-ventas">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="kpi-icon me-3"><i class="fa-solid fa-sack-dollar"></i></div>
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Ventas (Sin IVA)</p>
                            <h3 class="fw-bold mb-0 text-dark"><?= $formatMoney->formatCurrency($total_ventas, 'COP') ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card shadow-sm h-100 kpi-costos">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="kpi-icon me-3"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Costos Operativos</p>
                            <h3 class="fw-bold mb-0 text-dark"><?= $formatMoney->formatCurrency($total_costos, 'COP') ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card shadow-sm h-100 kpi-utilidad">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="kpi-icon me-3"><i class="fa-solid fa-piggy-bank"></i></div>
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Utilidad Neta</p>
                            <h3 class="fw-bold mb-0 <?= $utilidad_neta >= 0 ? 'text-success' : 'text-danger' ?>"><?= $formatMoney->formatCurrency($utilidad_neta, 'COP') ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card shadow-sm h-100 kpi-margen">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="kpi-icon me-3"><i class="fa-solid fa-percent"></i></div>
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Margen Promedio</p>
                            <h3 class="fw-bold mb-0 text-dark"><?= number_format($margen, 1) ?>%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            
            <!-- Rentabilidad por Categoría -->
            <div class="col-xl-5">
                <div class="card border-0 rounded-4 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom p-4">
                        <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-layer-group text-primary me-2"></i> Desempeño por Categoría</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-secondary small text-uppercase">
                                    <tr>
                                        <th class="ps-4 py-3 fw-semibold border-bottom-0">Categoría</th>
                                        <th class="py-3 fw-semibold border-bottom-0 text-end">Ventas</th>
                                        <th class="pe-4 py-3 fw-semibold border-bottom-0 text-end">Utilidad</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    <?php foreach ($rentabilidad_categorias as $cat): 
                                        $u_cat = (float)$cat['utilidad'];
                                    ?>
                                        <tr>
                                            <td class="ps-4 py-3 fw-medium text-dark"><?= htmlspecialchars($cat['nombre_categoria']) ?></td>
                                            <td class="py-3 text-end text-muted"><?= $formatMoney->formatCurrency((float)$cat['ventas'], 'COP') ?></td>
                                            <td class="pe-4 py-3 text-end fw-bold <?= $u_cat >= 0 ? 'text-success' : 'text-danger' ?>">
                                                <?= $formatMoney->formatCurrency($u_cat, 'COP') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial Global de Facturas -->
            <div class="col-xl-7">
                <div class="card border-0 rounded-4 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom p-4">
                        <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-receipt text-primary me-2"></i> Historial Global de Ventas</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-secondary small text-uppercase" style="position: sticky; top: 0; z-index: 1;">
                                    <tr>
                                        <th class="ps-4 py-3 fw-semibold border-bottom-0">Factura</th>
                                        <th class="py-3 fw-semibold border-bottom-0">Fecha / Vendedor</th>
                                        <th class="py-3 fw-semibold border-bottom-0">Cliente</th>
                                        <th class="pe-4 py-3 fw-semibold border-bottom-0 text-end">Utilidad / Total</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    <?php foreach ($historial as $factura): 
                                        $u_fac = (float)$factura['utilidad_factura'];
                                    ?>
                                        <tr style="cursor: pointer;" onclick="verDetalleFactura(<?= $factura['id_factura'] ?>, '<?= $factura['prefijo_resolucion'].$factura['consecutivo'] ?>')">
                                            <td class="ps-4 py-3">
                                                <div class="fw-bold text-primary">#<?= $factura['prefijo_resolucion'] . $factura['consecutivo'] ?></div>
                                            </td>
                                            <td class="py-3">
                                                <div class="text-dark small"><i class="fa-regular fa-calendar me-1 text-muted"></i> <?= date('d/m/Y H:i', strtotime($factura['fecha_emision'])) ?></div>
                                                <div class="text-muted small"><i class="fa-solid fa-user-tag me-1"></i> <?= htmlspecialchars($factura['vendedor']) ?></div>
                                            </td>
                                            <td class="py-3">
                                                <div class="text-dark text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($factura['cliente']) ?>">
                                                    <?= htmlspecialchars($factura['cliente']) ?>
                                                </div>
                                            </td>
                                            <td class="pe-4 py-3 text-end">
                                                <div class="fw-bold <?= $u_fac >= 0 ? 'text-success' : 'text-danger' ?>" title="Utilidad Neta">
                                                    <?= $formatMoney->formatCurrency($u_fac, 'COP') ?>
                                                </div>
                                                <div class="text-muted small" title="Total Facturado (con IVA)">
                                                    <?= $formatMoney->formatCurrency((float)$factura['total_pagar'], 'COP') ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    <?php endif; ?>
</div>

<!-- Modal Detalle Factura -->
<div class="modal fade" id="modalDetalleFactura" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom px-4 py-3 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i> Detalle de Rentabilidad | <span id="lblFacturaNumero" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-white text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3 fw-semibold border-bottom text-muted">Producto</th>
                                <th class="py-3 fw-semibold border-bottom text-center text-muted">Cant</th>
                                <th class="py-3 fw-semibold border-bottom text-end text-muted">Venta (c/u)</th>
                                <th class="py-3 fw-semibold border-bottom text-end text-muted">Costo (c/u)</th>
                                <th class="pe-4 py-3 fw-semibold border-bottom text-end text-muted">Utilidad Neta</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0" id="cuerpoDetallesFactura">
                            <!-- Inyectado por JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-3 bg-light rounded-bottom-4 justify-content-between">
                <div class="text-muted small fw-bold">* Los valores mostrados aquí no incluyen IVA, ya que el IVA no es ganancia para el negocio.</div>
                <button type="button" class="btn btn-secondary px-4 shadow-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    const formatterMoney = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 });
    let modalDetalle;

    document.addEventListener('DOMContentLoaded', () => {
        modalDetalle = new bootstrap.Modal(document.getElementById('modalDetalleFactura'));
    });

    function verDetalleFactura(id_factura, numero_factura) {
        document.getElementById('lblFacturaNumero').textContent = 'Factura #' + numero_factura;
        const tbody = document.getElementById('cuerpoDetallesFactura');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fa-solid fa-spinner fa-spin me-2"></i> Cargando detalles...</td></tr>';
        
        modalDetalle.show();

        fetch(`../../controllers/RentabilidadController.php?action=detalle_ajax&id_factura=${id_factura}`)
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                if(data.success && data.detalles.length > 0) {
                    let totalUtilidad = 0;
                    data.detalles.forEach(d => {
                        const utilidad = parseFloat(d.utilidad_linea);
                        totalUtilidad += utilidad;
                        const colorClass = utilidad >= 0 ? 'text-success' : 'text-danger';
                        
                        tbody.innerHTML += `
                            <tr>
                                <td class="ps-4 py-3 text-dark fw-medium">${d.nombre_producto}</td>
                                <td class="py-3 text-center text-muted">${d.cantidad}</td>
                                <td class="py-3 text-end text-muted">${formatterMoney.format(d.precio_unitario_venta)}</td>
                                <td class="py-3 text-end text-muted">${formatterMoney.format(d.precio_unitario_costo)}</td>
                                <td class="pe-4 py-3 text-end fw-bold ${colorClass}">${formatterMoney.format(utilidad)}</td>
                            </tr>
                        `;
                    });
                    
                    // Row for totals
                    const colorTotal = totalUtilidad >= 0 ? 'text-success' : 'text-danger';
                    tbody.innerHTML += `
                        <tr class="bg-light">
                            <td colspan="4" class="ps-4 py-3 text-end fw-bold text-dark text-uppercase">Total Utilidad Factura:</td>
                            <td class="pe-4 py-3 text-end fw-bold fs-5 ${colorTotal}">${formatterMoney.format(totalUtilidad)}</td>
                        </tr>
                    `;
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No se pudieron cargar los detalles o no hay datos.</td></tr>';
                }
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-danger"><i class="fa-solid fa-triangle-exclamation"></i> Error de conexión.</td></tr>';
            });
    }
</script>

<?php require_once '../layouts/footer.php'; ?>
