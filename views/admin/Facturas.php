<?php 
// Vista de Facturación POS - Rediseño Wireframe
$current_page = 'facturas.php';
require_once '../layouts/header.php'; 
?>

<!-- Estilos específicos para acercarse al wireframe -->
<style>
    .pos-section-title {
        font-family: var(--font-heading);
        text-transform: uppercase;
        font-size: 0.9rem;
        letter-spacing: 1px;
        color: #6b7280;
        margin-bottom: 1rem;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 0.5rem;
    }
    .client-selected-card {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 0.5rem;
        padding: 1rem;
    }
</style>

<div class="container-fluid py-4 h-100 d-flex flex-column" style="max-width: 1400px;">
    
    <!-- Encabezado POS -->
    <div class="d-flex justify-content-between align-items-end mb-3">
        <div>
            <h2 class="h4 fw-bold text-dark mb-0 text-uppercase" style="font-family: var(--font-heading);">
                FACTURACIÓN POS <span class="text-muted fs-6 ms-2">| Nueva venta</span>
            </h2>
        </div>
        <div>
            <span class="badge bg-primary rounded-pill px-3 py-2">Venta # Nueva</span>
        </div>
    </div>

    <!-- Indicador de Carga -->
    <div id="loaderPOS" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2 text-muted fw-bold">Cargando catálogo y clientes...</p>
    </div>

    <!-- Interfaz POS (Oculta hasta cargar) -->
    <div class="d-flex flex-column flex-grow-1 d-none" id="interfazPOS">
        
        <!-- SECCIÓN SUPERIOR: CLIENTE -->
        <div class="card border-0 rounded-4 shadow-sm bg-white mb-3">
            <div class="card-body">
                <h5 class="pos-section-title">CLIENTE</h5>
                
                <div class="row align-items-center mb-3">
                    <div class="col-md-8 position-relative">
                        <div class="input-group">
                            <input type="text" id="buscadorCliente" class="form-control bg-light" placeholder="Buscar por nombre, cédula o NIT..." autocomplete="off">
                            <button class="btn btn-primary px-3" type="button" id="btnBuscarCliente"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </div>
                        <!-- Dropdown de resultados de cliente -->
                        <div id="resultadosCliente" class="list-group position-absolute w-100 shadow d-none" style="z-index: 1000; max-height: 200px; overflow-y: auto;"></div>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-outline-orange w-100" type="button" onclick="abrirModalNuevoCliente()">
                            <i class="fa-solid fa-plus me-1"></i> Nuevo
                        </button>
                    </div>
                </div>

                <div class="client-selected-card">
                    <div class="text-muted small mb-1 fw-bold">Cliente seleccionado:</div>
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div class="fs-5 fw-bold text-dark" id="clienteNombre">Consumidor Final</div>
                        <div class="text-muted" id="clienteIdentificacion">CC/NIT: ---</div>
                        <div class="text-muted" id="clienteTelefono">Tel: ---</div>
                        <button class="btn btn-sm btn-light border text-danger" id="btnRemoverCliente" title="Quitar cliente" style="display: none;"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN INFERIOR: PRODUCTOS Y RESUMEN -->
        <div class="row flex-grow-1 g-3">
            
            <!-- IZQUIERDA: PRODUCTOS -->
            <div class="col-lg-8 d-flex flex-column">
                <div class="card border-0 rounded-4 shadow-sm bg-white flex-grow-1 d-flex flex-column">
                    <div class="card-body d-flex flex-column p-0">
                        <div class="p-4 pb-0">
                            <h5 class="pos-section-title">PRODUCTOS</h5>
                            
                            <!-- Buscador de productos -->
                            <div class="position-relative mb-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-box"></i></span>
                                    <input type="text" id="buscadorProductosPOS" class="form-control bg-light border-start-0" placeholder="Código / nombre del producto..." autocomplete="off">
                                </div>
                                <!-- Dropdown de resultados de productos -->
                                <div id="resultadosProducto" class="list-group position-absolute w-100 shadow d-none" style="z-index: 1000; max-height: 250px; overflow-y: auto;"></div>
                            </div>
                        </div>

                        <!-- Tabla de Carrito -->
                        <div class="flex-grow-1 overflow-auto px-4 pb-4" style="min-height: 250px;">
                            <table class="table align-middle mb-0" id="tablaCarrito">
                                <thead class="text-secondary small text-uppercase" style="position: sticky; top: 0; background: white; z-index: 1;">
                                    <tr>
                                        <th class="border-bottom-0">Producto</th>
                                        <th class="text-center border-bottom-0" style="width: 130px;">Cant.</th>
                                        <th class="text-end border-bottom-0">Precio</th>
                                        <th class="text-center border-bottom-0">IVA</th>
                                        <th class="text-end border-bottom-0">Total</th>
                                        <th class="text-center border-bottom-0" style="width: 60px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="cuerpoCarrito" class="border-top-0">
                                    <!-- Items renderizados vía JS -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Panel de Stock Info -->
                        <div class="bg-light px-4 py-2 border-top d-flex justify-content-between align-items-center">
                            <span class="text-muted small fw-bold"><i class="fa-solid fa-boxes-stacked me-1"></i> Stock disponible: <span id="lblStockDisponible" class="text-dark">--</span></span>
                            <span class="text-muted small" id="lblProductoSeleccionado">Ningún producto seleccionado</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DERECHA: RESUMEN DE VENTA -->
            <div class="col-lg-4 d-flex flex-column">
                <div class="card border-0 rounded-4 shadow-sm bg-white flex-grow-1 d-flex flex-column">
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="pos-section-title">RESUMEN DE VENTA</h5>
                        
                        <div class="mt-4 mb-auto">
                            <div class="d-flex justify-content-between mb-3 fs-5">
                                <span class="text-muted">Subtotal</span>
                                <span class="fw-bold text-dark" id="txtSubtotal">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-4 fs-5">
                                <span class="text-muted">IVA</span>
                                <span class="fw-bold text-dark" id="txtIva">$0.00</span>
                            </div>
                            
                            <hr class="text-muted">
                            
                            <div class="d-flex justify-content-between align-items-center mb-5">
                                <span class="text-muted fw-bold fs-5">TOTAL</span>
                                <span class="fw-bold text-success" id="txtTotal" style="font-size: 2rem; line-height: 1;">$0.00</span>
                            </div>
                        </div>
                        
                        <div>
                            <button class="btn btn-outline-secondary w-100 py-3 mb-3 rounded-3 fw-bold d-flex justify-content-center align-items-center" id="btnLeerFactura">
                                <i class="fa-solid fa-volume-high me-2"></i> Leer factura
                            </button>
                            <button class="btn btn-primary w-100 py-3 rounded-3 shadow fw-bold fs-5 d-flex justify-content-center align-items-center" id="btnProcesar">
                                CONFIRMAR VENTA
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Nuevo Cliente -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-dark text-uppercase" style="font-family: var(--font-heading);"><i class="fa-solid fa-user-plus me-2 text-primary"></i> Registrar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevoCliente">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Cédula / NIT *</label>
                        <input type="text" class="form-control bg-light" id="nc_identificacion" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Nombre Completo *</label>
                        <input type="text" class="form-control bg-light" id="nc_nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Teléfono</label>
                        <input type="text" class="form-control bg-light" id="nc_telefono">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-4 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 text-white shadow-sm" id="btnGuardarClienteRápido">Guardar y Seleccionar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts unificados (Lógica adaptada al nuevo UI) -->
<script src="../../public/js/facturas.js"></script>

<?php require_once '../layouts/footer.php'; ?>
