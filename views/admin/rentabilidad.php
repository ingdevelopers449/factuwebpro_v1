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
    
    <!-- Header & Smart Filters -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 glass-header">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-4">
                
                <!-- Titulo -->
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1 text-uppercase" style="font-family: var(--font-heading);">
                        <i class="fa-solid fa-chart-line me-2" style="color: #f59e0b;"></i> <span style="color: #12102f;">Dashboard Financiero</span>
                    </h2>
                    <p class="text-muted small mb-0">Rentabilidad, ventas y márgenes de utilidad (Admin)</p>
                </div>
                
                <!-- Smart Filters -->
                <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center bg-white p-2 p-md-3 rounded-4 shadow-sm border">
                    <!-- Quick Filters -->
                    <div class="btn-group shadow-sm" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm fw-bold px-3" onclick="setRangoFechas('hoy')">Hoy</button>
                        <button type="button" class="btn btn-outline-primary btn-sm fw-bold px-3" onclick="setRangoFechas('semana')">Semana</button>
                        <button type="button" class="btn btn-outline-primary btn-sm fw-bold px-3" onclick="setRangoFechas('mes')">Mes</button>
                        <button type="button" class="btn btn-outline-primary btn-sm fw-bold px-3" onclick="setRangoFechas('anio')">Año</button>
                    </div>
                    
                    <div class="vr d-none d-md-block text-secondary"></div>
                    
                    <!-- Formulario Custom Fechas -->
                    <form id="formFiltroRentabilidad" action="rentabilidad.php" method="GET" class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center m-0">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small fw-bold">Del</span>
                            <input type="date" id="inputDesde" name="desde" class="form-control form-control-sm bg-light border-0 fw-medium text-dark" value="<?= htmlspecialchars($desde) ?>" style="width: 140px;">
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small fw-bold">al</span>
                            <input type="date" id="inputHasta" name="hasta" class="form-control form-control-sm bg-light border-0 fw-medium text-dark" value="<?= htmlspecialchars($hasta) ?>" style="width: 140px;">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm shadow-sm px-3 ms-sm-2">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>
                </div>
                
                <!-- Imprimir -->
                <div>
                    <button onclick="window.print()" class="btn btn-outline-secondary px-4 shadow-sm rounded-3">
                        <i class="fa-solid fa-print me-2"></i> Exportar
                    </button>
                </div>
                
            </div>
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

        <!-- KPIs Rediseñados -->
        <div class="row g-4 mb-4">
            <!-- Ventas -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 rounded-4 shadow-sm h-100 position-relative overflow-hidden" style="background: linear-gradient(135deg, #12102f 0%, #1e1b4b 100%);">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="text-white-50 small fw-bold text-uppercase mb-0">Ingresos (Sin IVA)</p>
                            <div class="bg-white bg-opacity-10 rounded-3 p-2 text-white">
                                <i class="fa-solid fa-sack-dollar fa-lg"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0 text-white"><?= $formatMoney->formatCurrency($total_ventas, 'COP') ?></h3>
                    </div>
                    <div class="position-absolute end-0 bottom-0 opacity-25" style="transform: translate(20%, 20%); z-index: 0;">
                        <i class="fa-solid fa-sack-dollar" style="font-size: 6rem; color: #fff;"></i>
                    </div>
                </div>
            </div>

            <!-- Costos -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 rounded-4 shadow-sm h-100 bg-white border-start border-4 border-warning">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="text-muted small fw-bold text-uppercase mb-0">Costo de Mercancía</p>
                            <div class="bg-warning bg-opacity-10 rounded-3 p-2 text-warning">
                                <i class="fa-solid fa-hand-holding-dollar fa-lg"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0 text-dark"><?= $formatMoney->formatCurrency($total_costos, 'COP') ?></h3>
                    </div>
                </div>
            </div>

            <!-- Utilidad -->
            <div class="col-xl-3 col-md-6">
                <?php 
                $utilidadClass = $utilidad_neta >= 0 ? 'border-success' : 'border-danger';
                $utilidadIconClass = $utilidad_neta >= 0 ? 'text-success bg-success' : 'text-danger bg-danger';
                $utilidadTextClass = $utilidad_neta >= 0 ? 'text-success' : 'text-danger';
                ?>
                <div class="card border-0 rounded-4 shadow-sm h-100 bg-white border-start border-4 <?= $utilidadClass ?>">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="text-muted small fw-bold text-uppercase mb-0">Ganancia Neta</p>
                            <div class="<?= $utilidadIconClass ?> bg-opacity-10 rounded-3 p-2">
                                <i class="fa-solid fa-piggy-bank fa-lg"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0 <?= $utilidadTextClass ?>"><?= $formatMoney->formatCurrency($utilidad_neta, 'COP') ?></h3>
                    </div>
                </div>
            </div>

            <!-- Margen -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 rounded-4 shadow-sm h-100 position-relative overflow-hidden" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="text-white-50 small fw-bold text-uppercase mb-0">Margen Promedio</p>
                            <div class="bg-white bg-opacity-25 rounded-3 p-2 text-white">
                                <i class="fa-solid fa-percent fa-lg"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0 text-white"><?= number_format($margen, 1) ?>%</h3>
                    </div>
                    <div class="position-absolute end-0 bottom-0 opacity-25" style="transform: translate(10%, 20%); z-index: 0;">
                        <i class="fa-solid fa-chart-pie" style="font-size: 6rem; color: #fff;"></i>
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

    // Lógica para Filtros Rápidos
    function setRangoFechas(tipo) {
        const inputDesde = document.getElementById('inputDesde');
        const inputHasta = document.getElementById('inputHasta');
        const form = document.getElementById('formFiltroRentabilidad');
        
        const hoy = new Date();
        const formatDate = (date) => {
            const d = String(date.getDate()).padStart(2, '0');
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const y = date.getFullYear();
            return `${y}-${m}-${d}`;
        };

        let inicio, fin;

        if (tipo === 'hoy') {
            inicio = hoy;
            fin = hoy;
        } else if (tipo === 'semana') {
            // Lunes de esta semana
            const diaSemana = hoy.getDay();
            const diff = hoy.getDate() - diaSemana + (diaSemana === 0 ? -6 : 1); 
            inicio = new Date(hoy.setDate(diff));
            fin = new Date(); // hasta hoy
        } else if (tipo === 'mes') {
            inicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
            fin = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
        } else if (tipo === 'anio') {
            inicio = new Date(hoy.getFullYear(), 0, 1);
            fin = new Date(hoy.getFullYear(), 11, 31);
        }

        inputDesde.value = formatDate(inicio);
        inputHasta.value = formatDate(fin);
        
        // Auto-enviar formulario
        form.submit();
    }

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
