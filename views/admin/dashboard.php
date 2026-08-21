<?php
$current_page = 'dashboard.php';
require_once '../../controllers/DashboardController.php';

$ctrl = new DashboardController();
$pagina_facturas = max(1, (int)($_GET['factura_page'] ?? 1));
$data = $ctrl->index($pagina_facturas, 8);
extract($data);

$fmt = new NumberFormatter('es_CO', NumberFormatter::CURRENCY);
$fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

require_once '../layouts/header.php';
?>

<div class="container-fluid py-4" style="max-width:1400px;">

    <!-- ── ENCABEZADO ─────────────────────────────────────────── -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 glass-header p-4 rounded-4 shadow-sm">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1 text-uppercase" style="font-family:var(--font-heading);">
                <i class="fa-solid fa-chart-pie me-2" style="color:#f59e0b;"></i>
                <span style="color:#12102f;">Dashboard General</span>
            </h2>
            <p class="text-muted small mb-0">Resumen ejecutivo del negocio — <?= date('d \d\e F \d\e Y') ?></p>
        </div>
        <a href="facturas.php" class="btn btn-super-cta text-white rounded-pill shadow px-4">
            <i class="fa-solid fa-plus me-2"></i> Nueva Factura
        </a>
    </div>

    <!-- ── KPIs ───────────────────────────────────────────────── -->
    <div class="row g-4 mb-4">

        <!-- Ventas del día -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 rounded-4 shadow-sm h-100 position-relative overflow-hidden"
                 style="background:linear-gradient(135deg,#12102f 0%,#1e1b4b 100%);">
                <div class="position-absolute" style="right:-15px;top:-15px;opacity:.08;">
                    <i class="fa-solid fa-sack-dollar" style="font-size:7rem;color:#fff;"></i>
                </div>
                <div class="card-body p-4">
                    <p class="text-white-50 small fw-bold text-uppercase mb-1" style="letter-spacing:1px;">Ventas del Día</p>
                    <h3 class="text-white fw-bold mb-0"><?= $fmt->format($ventas_hoy) ?></h3>
                    <p class="text-white-50 small mt-2 mb-0">
                        <i class="fa-solid fa-file-invoice-dollar me-1 text-warning"></i>
                        <?= $facturas_hoy ?> factura<?= $facturas_hoy != 1 ? 's' : '' ?> emitida<?= $facturas_hoy != 1 ? 's' : '' ?> hoy
                    </p>
                </div>
            </div>
        </div>

        <!-- Ventas del mes -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 rounded-4 shadow-sm h-100 position-relative overflow-hidden"
                 style="background:linear-gradient(135deg,#ea580c 0%,#f59e0b 100%);">
                <div class="position-absolute" style="right:-15px;top:-15px;opacity:.1;">
                    <i class="fa-solid fa-chart-line" style="font-size:7rem;color:#fff;"></i>
                </div>
                <div class="card-body p-4">
                    <p class="text-white-50 small fw-bold text-uppercase mb-1" style="letter-spacing:1px;">Ventas del Mes</p>
                    <h3 class="text-white fw-bold mb-0"><?= $fmt->format($ventas_mes) ?></h3>
                    <p class="text-white-50 small mt-2 mb-0">
                        <i class="fa-solid fa-calendar me-1"></i> <?= date('F Y') ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Productos con stock bajo -->
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="productos.php" class="text-decoration-none">
                <div class="card border-0 rounded-4 shadow-sm h-100 position-relative overflow-hidden <?= $productos_bajos > 0 ? 'border border-danger border-2' : '' ?>">
                    <div class="position-absolute" style="right:-15px;top:-15px;opacity:.06;">
                        <i class="fa-solid fa-box-open" style="font-size:7rem;color:#12102f;"></i>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:1px;">Stock Bajo (&lt;5 unid.)</p>
                        <h3 class="fw-bold mb-0 <?= $productos_bajos > 0 ? 'text-danger' : 'text-dark' ?>">
                            <?= $productos_bajos ?>
                            <?php if ($productos_bajos > 0): ?>
                                <i class="fa-solid fa-triangle-exclamation text-danger ms-1" style="font-size:1.1rem;"></i>
                            <?php endif; ?>
                        </h3>
                        <p class="text-muted small mt-2 mb-0">
                            <?= $productos_bajos > 0 ? 'Productos que necesitan reposición' : 'Inventario en niveles normales' ?>
                        </p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total de clientes -->
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="clientes.php" class="text-decoration-none">
                <div class="card border-0 rounded-4 shadow-sm h-100 position-relative overflow-hidden">
                    <div class="position-absolute" style="right:-15px;top:-15px;opacity:.06;">
                        <i class="fa-solid fa-users" style="font-size:7rem;color:#12102f;"></i>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:1px;">Clientes Registrados</p>
                        <h3 class="fw-bold text-dark mb-0"><?= $total_clientes ?></h3>
                        <p class="text-muted small mt-2 mb-0">
                            <i class="fa-solid fa-arrow-right me-1" style="color:#ea580c;"></i> Ver base de clientes
                        </p>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <!-- ── GRÁFICA + TOP PRODUCTOS ────────────────────────────── -->
    <div class="row g-4 mb-4">

        <!-- Gráfica de ventas 7 días -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-chart-area me-2" style="color:#ea580c;"></i>
                        Ventas últimos 7 días
                    </h6>
                </div>
                <div class="card-body p-4">
                    <canvas id="chartVentas" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Top 5 productos -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-header bg-white border-0 p-4 pb-2">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-star me-2" style="color:#f59e0b;"></i>
                        Top 5 productos más vendidos
                    </h6>
                </div>
                <div class="card-body p-4 pt-2">
                    <?php if (empty($top_productos)): ?>
                        <p class="text-muted small text-center py-4">Sin ventas registradas aún.</p>
                    <?php else: ?>
                        <?php
                            $max_vendido = max(array_column($top_productos, 'total_vendido'));
                        ?>
                        <?php foreach ($top_productos as $i => $prod): ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-semibold text-dark text-truncate" style="max-width:75%;">
                                        <span class="badge rounded-pill me-1" style="background:#12102f; font-size:.65rem;"><?= $i+1 ?></span>
                                        <?= htmlspecialchars($prod['nombre_producto']) ?>
                                    </span>
                                    <span class="small text-muted fw-bold"><?= $prod['total_vendido'] ?> uds.</span>
                                </div>
                                <div class="progress rounded-pill" style="height:6px;">
                                    <div class="progress-bar rounded-pill"
                                         style="width:<?= round($prod['total_vendido']/$max_vendido*100) ?>%; background:linear-gradient(90deg,#ea580c,#f59e0b);">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ── ÚLTIMAS FACTURAS ───────────────────────────────────── -->
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0">
                <i class="fa-solid fa-clock-rotate-left me-2" style="color:#12102f;"></i>
                Últimas Facturas Emitidas
            </h6>
            <a href="rentabilidad.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                Ver reporte completo <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <?php if (empty($ultimas_facturas)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-folder-open fa-2x mb-2 d-block text-muted"></i>
                    No hay facturas registradas aún.
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small">
                        <tr>
                            <th class="ps-4 py-3 fw-semibold">N° Factura</th>
                            <th class="py-3 fw-semibold">Cliente</th>
                            <th class="py-3 fw-semibold">Vendedor</th>
                            <th class="py-3 fw-semibold">Fecha</th>
                            <th class="py-3 fw-semibold">Estado DIAN</th>
                            <th class="text-end pe-4 py-3 fw-semibold">Total</th>
                            <th class="py-3 fw-semibold"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimas_facturas as $f): ?>
                        <tr>
                            <td class="ps-4 py-3 fw-bold" style="color:#ea580c;">
                                <?= htmlspecialchars($f['prefijo_resolucion'] . $f['consecutivo']) ?>
                            </td>
                            <td class="py-3 text-dark fw-semibold"><?= htmlspecialchars($f['cliente_nombre']) ?></td>
                            <td class="py-3 text-muted small"><?= htmlspecialchars($f['vendedor'] ?? '—') ?></td>
                            <td class="py-3 text-muted small"><?= date('d/m/Y H:i', strtotime($f['fecha_emision'])) ?></td>
                            <td class="py-3">
                                <?php if ($f['estado_dian'] === 'aceptada'): ?>
                                    <span class="badge rounded-pill px-3 py-2" style="background:rgba(16,185,129,.12);color:#059669;">
                                        <i class="fa-solid fa-circle-check me-1"></i> Aceptada
                                    </span>
                                <?php elseif ($f['estado_dian'] === 'rechazada'): ?>
                                    <span class="badge rounded-pill px-3 py-2 bg-danger bg-opacity-10 text-danger">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> Rechazada
                                    </span>
                                <?php else: ?>
                                    <span class="badge rounded-pill px-3 py-2 bg-warning bg-opacity-10 text-warning border border-warning">
                                        <i class="fa-solid fa-clock me-1"></i> Pendiente
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4 py-3 fw-bold text-dark">
                                <?= $fmt->format($f['total_pagar']) ?>
                            </td>
                            <td class="py-3">
                                <a href="imprimir_factura.php?id=<?= $f['id_factura'] ?>"
                                   class="btn btn-sm btn-outline-secondary rounded-pill" target="_blank" title="Ver factura">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php if ($total_paginas_facturas > 1): ?>
            <div class="card-footer bg-white border-0 px-4 py-3 d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    Página <?= $pagina_facturas ?> de <?= $total_paginas_facturas ?>
                </span>
                <nav aria-label="Paginación de facturas">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= $pagina_facturas <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?factura_page=<?= $pagina_facturas - 1 ?>">Anterior</a>
                        </li>
                        <li class="page-item active"><span class="page-link"><?= $pagina_facturas ?></span></li>
                        <li class="page-item <?= $pagina_facturas >= $total_paginas_facturas ? 'disabled' : '' ?>">
                            <a class="page-link" href="?factura_page=<?= $pagina_facturas + 1 ?>">Siguiente</a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('chartVentas').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($grafica_labels) ?>,
        datasets: [{
            label: 'Ventas ($)',
            data: <?= json_encode($grafica_valores) ?>,
            borderColor: '#ea580c',
            backgroundColor: 'rgba(234,88,12,0.08)',
            borderWidth: 2.5,
            pointBackgroundColor: '#ea580c',
            pointRadius: 4,
            pointHoverRadius: 6,
            tension: 0.4,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ' $' + ctx.parsed.y.toLocaleString('es-CO')
                }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#6b7280', font: { size: 11 } } },
            y: {
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: {
                    color: '#6b7280',
                    font: { size: 11 },
                    callback: v => '$' + (v/1000000 >= 1 ? (v/1000000).toFixed(1)+'M' : v/1000 >= 1 ? (v/1000).toFixed(0)+'K' : v)
                }
            }
        }
    }
});
</script>

<?php require_once '../layouts/footer.php'; ?>
