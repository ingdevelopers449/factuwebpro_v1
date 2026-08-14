<?php 
// 1. Incluir el header (este a su vez inicia la sesión, valida permisos, incluye el <head>, 
// el sidebaradmin.php, y abre los contenedores principales y el topbar).
require_once '../layouts/header.php'; 
?>

<!-- ======================================================= -->
<!-- CONTENIDO PRINCIPAL DEL MÓDULO -->
<!-- ======================================================= -->
<div class="container-fluid">
    <!-- Título y Botón Principal -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
        <h2 class="fw-bold m-0 text-dark">Dashboard General</h2>
        <button type="button" class="btn btn-warning">
            <i class="fa-solid fa-plus me-2"></i> Nueva Factura
        </button>
    </div>

    <!-- Tarjetas Estadísticas -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="fa-solid fa-dollar-sign fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted small mb-1 fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Ventas del Día</p>
                        <h4 class="mb-0 fw-bold text-dark">$1,250,000</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="fa-solid fa-file-invoice-dollar fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted small mb-1 fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Facturas Emitidas</p>
                        <h4 class="mb-0 fw-bold text-dark">24</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="fa-solid fa-box-open fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted small mb-1 fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Productos Bajos</p>
                        <h4 class="mb-0 fw-bold text-dark">3</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="fa-solid fa-users fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted small mb-1 fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Nuevos Clientes</p>
                        <h4 class="mb-0 fw-bold text-dark">18</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ejemplo de Tabla o Contenido más amplio -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold m-0 text-dark">Últimas Ventas Realizadas</h5>
            <a href="#" class="text-decoration-none fw-semibold small">Ver todo <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th class="ps-4 py-3 fw-semibold">Factura</th>
                            <th class="py-3 fw-semibold">Cliente</th>
                            <th class="py-3 fw-semibold">Fecha</th>
                            <th class="py-3 fw-semibold">Estado</th>
                            <th class="text-end pe-4 py-3 fw-semibold">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4 py-3 fw-semibold text-primary">#FV-001</td>
                            <td class="py-3">Juan Pérez</td>
                            <td class="py-3 text-muted small">Hoy, 10:30 AM</td>
                            <td class="py-3"><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">Pagada</span></td>
                            <td class="text-end pe-4 py-3 fw-bold text-dark">$150,000</td>
                        </tr>
                        <tr>
                            <td class="ps-4 py-3 fw-semibold text-primary">#FV-002</td>
                            <td class="py-3">María Gómez</td>
                            <td class="py-3 text-muted small">Hoy, 11:15 AM</td>
                            <td class="py-3"><span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-3 py-2 text-dark">Pendiente</span></td>
                            <td class="text-end pe-4 py-3 fw-bold text-dark">$320,000</td>
                        </tr>
                        <tr>
                            <td class="ps-4 py-3 fw-semibold text-primary">#FV-003</td>
                            <td class="py-3">Cliente Mostrador</td>
                            <td class="py-3 text-muted small">Hoy, 12:45 PM</td>
                            <td class="py-3"><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">Pagada</span></td>
                            <td class="text-end pe-4 py-3 fw-bold text-dark">$45,000</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- ======================================================= -->
<!-- FIN DEL CONTENIDO PRINCIPAL -->
<!-- ======================================================= -->

<?php 
// 2. Incluir el footer (cierra los contenedores, pone el copyright y los scripts JS)
require_once '../layouts/footer.php'; 
?>
