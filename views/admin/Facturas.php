<?php 
// Vista de Facturación POS - Pura (Sin Backend)
// Define la página actual para que el header resalte el menú activo
$current_page = 'facturas.php';
require_once '../layouts/header.php'; 
?>

<div class="container-fluid py-4 h-100 d-flex flex-column">
    
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1 text-uppercase" style="font-family: var(--font-heading);">
                <span class="text-primary"><i class="fa-solid fa-cash-register me-2"></i> Punto de Venta</span>
            </h2>
            <p class="text-muted small mb-0">Generación rápida de facturas POS</p>
        </div>
    </div>

    <!-- Indicador de Carga -->
    <div id="loaderPOS" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2 text-muted fw-bold">Cargando catálogo y clientes...</p>
    </div>

    <!-- Interfaz POS (Oculta hasta cargar) -->
    <div class="row flex-grow-1 g-4 d-none" id="interfazPOS">
        
        <!-- COLUMNA IZQUIERDA: Catálogo de Productos -->
        <div class="col-lg-7 d-flex flex-column">
            <div class="card border-0 rounded-4 shadow-sm bg-white flex-grow-1 d-flex flex-column overflow-hidden">
                <div class="card-header bg-white border-bottom p-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-barcode"></i></span>
                        <input type="text" id="buscadorProductos" class="form-control bg-light border-start-0 ps-0" placeholder="Buscar producto por nombre o código...">
                    </div>
                </div>
                
                <div class="card-body p-0 overflow-auto" style="height: 60vh;">
                    <div class="list-group list-group-flush" id="listaProductos">
                        <!-- Renderizado dinámico vía JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Carrito y Totales -->
        <div class="col-lg-5 d-flex flex-column">
            <div class="card border-0 rounded-4 shadow-sm bg-white flex-grow-1 d-flex flex-column overflow-hidden">
                
                <!-- Selector de Cliente -->
                <div class="card-header bg-light border-bottom p-3">
                    <label class="form-label fw-semibold small text-secondary mb-1">Cliente asignado a la factura</label>
                    <select id="selectCliente" class="form-select border-0 shadow-sm">
                        <option value="">Consumidor Final (Opcional)</option>
                        <!-- Opciones dinámicas de clientes vía JS -->
                    </select>
                </div>

                <!-- Detalle del Carrito -->
                <div class="card-body p-0 overflow-auto" style="height: 35vh;">
                    <table class="table table-hover align-middle mb-0" id="tablaCarrito">
                        <thead class="bg-white sticky-top shadow-sm text-secondary small text-uppercase" style="z-index: 1;">
                            <tr>
                                <th class="ps-3 border-0">Producto</th>
                                <th class="text-center border-0" style="width: 100px;">Cant</th>
                                <th class="text-end border-0">Total</th>
                                <th class="pe-3 text-center border-0" style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoCarrito" class="border-top-0">
                            <!-- Items del carrito renderizados vía JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Panel de Totales y Cobro -->
                <div class="card-footer bg-white border-top p-4 mt-auto">
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span class="fw-medium">Subtotal:</span>
                        <span id="txtSubtotal">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small text-muted">
                        <span class="fw-medium">Total IVA:</span>
                        <span id="txtIva">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fs-4 fw-bold text-dark">TOTAL A PAGAR</span>
                        <span class="fs-4 fw-bold text-success" id="txtTotal">$0.00</span>
                    </div>
                    
                    <button class="btn btn-primary w-100 py-3 rounded-3 shadow-sm fw-bold fs-5 d-flex justify-content-center align-items-center" id="btnProcesar">
                        <i class="fa-solid fa-check-circle me-2"></i> PROCESAR FACTURA
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="../../public/js/facturas.js"></script>

<?php require_once '../layouts/footer.php'; ?>
