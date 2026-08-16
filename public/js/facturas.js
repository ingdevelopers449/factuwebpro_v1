// 1. Estado de Datos
let catalogoProductos = [];
let listaClientes = [];
let carrito = [];

// 2. Referencias DOM
let loaderPOS, interfazPOS, listaProductosUI, buscadorProductos, cuerpoCarrito;
let txtSubtotal, txtIva, txtTotal, btnProcesar, selectCliente;

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

// Inicializar eventos al cargar el DOM
document.addEventListener('DOMContentLoaded', () => {
    
    loaderPOS = document.getElementById('loaderPOS');
    interfazPOS = document.getElementById('interfazPOS');
    listaProductosUI = document.getElementById('listaProductos');
    buscadorProductos = document.getElementById('buscadorProductos');
    cuerpoCarrito = document.getElementById('cuerpoCarrito');
    txtSubtotal = document.getElementById('txtSubtotal');
    txtIva = document.getElementById('txtIva');
    txtTotal = document.getElementById('txtTotal');
    btnProcesar = document.getElementById('btnProcesar');
    selectCliente = document.getElementById('selectCliente');

    // Eventos
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

    // Arrancar la inicialización AJAX
    inicializarPOS();
});
