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

<script>
    // 1. Estado de Datos
    let catalogoProductos = [];
    let listaClientes = [];
    let carrito = [];

    // 2. Referencias DOM
    const loaderPOS = document.getElementById('loaderPOS');
    const interfazPOS = document.getElementById('interfazPOS');
    
    const listaProductosUI = document.getElementById('listaProductos');
    const buscadorProductos = document.getElementById('buscadorProductos');
    const cuerpoCarrito = document.getElementById('cuerpoCarrito');
    
    const txtSubtotal = document.getElementById('txtSubtotal');
    const txtIva = document.getElementById('txtIva');
    const txtTotal = document.getElementById('txtTotal');
    const btnProcesar = document.getElementById('btnProcesar');
    const selectCliente = document.getElementById('selectCliente');

    // 3. Obtener Datos por AJAX (Separación Frontend-Backend)
    async function inicializarPOS() {
        try {
            const response = await fetch('../../controllers/FacturaController.php?action=init_pos');
            const data = await response.json();
            
            if (data.success) {
                catalogoProductos = data.productos;
                listaClientes = data.clientes;
                
                cargarClientes(listaClientes);
                renderizarCatalogo();
                actualizarCarrito();

                // Mostrar interfaz
                loaderPOS.classList.add('d-none');
                interfazPOS.classList.remove('d-none');
            } else {
                Swal.fire('Error', 'No se pudo cargar la información del POS.', 'error');
            }
        } catch (error) {
            console.error(error);
            Swal.fire('Error de conexión', 'No se pudo comunicar con el servidor.', 'error');
        }
    }

    function cargarClientes(clientes) {
        clientes.forEach(cliente => {
            const option = document.createElement('option');
            option.value = cliente.id_cliente;
            option.textContent = `${cliente.identificacion} - ${cliente.nombre_razon_social}`;
            selectCliente.appendChild(option);
        });
    }

    // 4. Renderizar Catálogo
    function renderizarCatalogo(filtro = '') {
        listaProductosUI.innerHTML = '';
        
        const termino = filtro.toLowerCase();
        const filtrados = catalogoProductos.filter(p => 
            p.nombre_producto.toLowerCase().includes(termino) || 
            (p.codigo_barras && p.codigo_barras.toLowerCase().includes(termino))
        );

        if (filtrados.length === 0) {
            listaProductosUI.innerHTML = `<div class="p-4 text-center text-muted">No se encontraron productos.</div>`;
            return;
        }

        filtrados.forEach(p => {
            const precioFormatter = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 });
            const precioVenta = parseFloat(p.precio_venta);
            
            const div = document.createElement('a');
            div.href = '#';
            div.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 border-bottom';
            div.onclick = (e) => {
                e.preventDefault();
                agregarAlCarrito(p);
            };
            
            div.innerHTML = `
                <div>
                    <h6 class="mb-1 text-dark fw-bold">${p.nombre_producto}</h6>
                    <small class="text-muted"><i class="fa-solid fa-barcode"></i> ${p.codigo_barras || 'Sin código'} &nbsp;&bull;&nbsp; <span class="text-${p.stock_actual > 5 ? 'success' : 'warning'}"><i class="fa-solid fa-box"></i> Stock: ${p.stock_actual}</span></small>
                </div>
                <div class="text-end">
                    <span class="d-block fs-6 fw-bold text-primary">${precioFormatter.format(precioVenta)}</span>
                    <small class="text-muted">IVA ${parseFloat(p.tarifa_iva)}%</small>
                </div>
            `;
            listaProductosUI.appendChild(div);
        });
    }

    // 5. Manejo del Carrito
    window.agregarAlCarrito = function(productoDB) {
        const index = carrito.findIndex(item => item.id_producto === productoDB.id_producto);
        
        if (index > -1) {
            if(carrito[index].cantidad + 1 > productoDB.stock_actual) {
                Swal.fire('Stock Insuficiente', 'No hay suficientes existencias de este producto.', 'warning');
                return;
            }
            carrito[index].cantidad++;
        } else {
            if(1 > productoDB.stock_actual) {
                Swal.fire('Sin Stock', 'Este producto está agotado.', 'error');
                return;
            }
            carrito.push({
                id_producto: productoDB.id_producto,
                nombre: productoDB.nombre_producto,
                precio: parseFloat(productoDB.precio_venta),
                iva: parseFloat(productoDB.tarifa_iva),
                cantidad: 1,
                stock_max: productoDB.stock_actual
            });
        }
        actualizarCarrito();
    }

    window.removerDelCarrito = function(index) {
        carrito.splice(index, 1);
        actualizarCarrito();
    }

    window.modificarCantidad = function(index, delta) {
        const item = carrito[index];
        const nuevaCant = item.cantidad + delta;
        if (nuevaCant <= 0) {
            removerDelCarrito(index);
        } else if (nuevaCant > item.stock_max) {
            Swal.fire('Límite de Stock', 'Has alcanzado el máximo disponible en inventario.', 'warning');
        } else {
            item.cantidad = nuevaCant;
            actualizarCarrito();
        }
    }

    window.actualizarCarrito = function() {
        cuerpoCarrito.innerHTML = '';
        
        let subtotalGlobal = 0;
        let ivaGlobal = 0;
        let totalGlobal = 0;

        const formatter = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 });

        if (carrito.length === 0) {
            cuerpoCarrito.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-muted small">El carrito está vacío</td></tr>`;
        } else {
            carrito.forEach((item, index) => {
                const subtotalLinea = item.precio * item.cantidad;
                const ivaLinea = subtotalLinea * (item.iva / 100);
                const totalLinea = subtotalLinea + ivaLinea;

                subtotalGlobal += subtotalLinea;
                ivaGlobal += ivaLinea;
                totalGlobal += totalLinea;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="ps-3 py-3">
                        <div class="fw-semibold text-dark small text-truncate" style="max-width: 150px;" title="${item.nombre}">${item.nombre}</div>
                        <div class="text-muted" style="font-size: 0.7rem;">${formatter.format(item.precio)}</div>
                    </td>
                    <td class="text-center py-3">
                        <div class="input-group input-group-sm rounded-3 shadow-none border mx-auto" style="width: 80px;">
                            <button class="btn btn-light border-0 px-2" onclick="modificarCantidad(${index}, -1)"><i class="fa-solid fa-minus fs-7"></i></button>
                            <input type="text" class="form-control text-center border-0 bg-transparent px-0 fw-bold" value="${item.cantidad}" readonly style="font-size: 0.85rem;">
                            <button class="btn btn-light border-0 px-2" onclick="modificarCantidad(${index}, 1)"><i class="fa-solid fa-plus fs-7"></i></button>
                        </div>
                    </td>
                    <td class="text-end py-3 fw-bold text-dark small">
                        ${formatter.format(totalLinea)}
                    </td>
                    <td class="pe-3 text-center py-3">
                        <button class="btn btn-sm text-danger border-0" onclick="removerDelCarrito(${index})"><i class="fa-regular fa-trash-can"></i></button>
                    </td>
                `;
                cuerpoCarrito.appendChild(tr);
            });
        }

        txtSubtotal.textContent = formatter.format(subtotalGlobal);
        txtIva.textContent = formatter.format(ivaGlobal);
        txtTotal.textContent = formatter.format(totalGlobal);
        
        btnProcesar.dataset.subtotal = subtotalGlobal;
        btnProcesar.dataset.iva = ivaGlobal;
        btnProcesar.dataset.total = totalGlobal;
    }

    // 6. Eventos
    buscadorProductos.addEventListener('input', (e) => {
        renderizarCatalogo(e.target.value);
    });

    btnProcesar.addEventListener('click', () => {
        if (carrito.length === 0) {
            Swal.fire('Carrito Vacío', 'Agrega al menos un producto para facturar.', 'warning');
            return;
        }

        const dataPayload = {
            id_cliente: selectCliente.value,
            subtotal: btnProcesar.dataset.subtotal,
            total_iva: btnProcesar.dataset.iva,
            total_pagar: btnProcesar.dataset.total,
            detalles: carrito
        };

        btnProcesar.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> PROCESANDO...';
        btnProcesar.disabled = true;

        fetch('../../controllers/FacturaController.php?action=procesar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dataPayload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                Swal.fire('Error al Facturar', data.error || 'Error desconocido', 'error');
                btnProcesar.innerHTML = '<i class="fa-solid fa-check-circle me-2"></i> PROCESAR FACTURA';
                btnProcesar.disabled = false;
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire('Error Crítico', 'Falló la comunicación con el servidor.', 'error');
            btnProcesar.innerHTML = '<i class="fa-solid fa-check-circle me-2"></i> PROCESAR FACTURA';
            btnProcesar.disabled = false;
        });
    });

    // Arrancar la inicialización AJAX al cargar el DOM
    document.addEventListener('DOMContentLoaded', inicializarPOS);

</script>

<?php require_once '../layouts/footer.php'; ?>
