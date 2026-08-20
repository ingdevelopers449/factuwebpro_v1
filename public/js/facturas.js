// 1. Estado de Datos
let catalogoProductos = [];
let listaClientes = [];
let carrito = [];
let clienteSeleccionado = null;
let productoEnFoco = null;

// Formateador de moneda
const formatter = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 });

// 2. Referencias DOM
let loaderPOS, interfazPOS, cuerpoCarrito;
let txtSubtotal, txtIva, txtTotal, btnProcesar, btnLeerFactura;
let buscadorCliente, resultadosCliente, btnBuscarCliente;
let clienteNombre, clienteIdentificacion, clienteTelefono, btnRemoverCliente;
let buscadorProductosPOS, resultadosProducto;
let lblStockDisponible, lblProductoSeleccionado;
let modalNuevoClienteObj, formNuevoCliente;

// 3. Obtener Datos por AJAX (Separación Frontend-Backend)
async function inicializarPOS() {
    try {
        const response = await fetch('../../controllers/FacturaController.php?action=init_pos');
        const data = await response.json();
        
        if (data.success) {
            catalogoProductos = data.productos;
            listaClientes = data.clientes;
            
            actualizarCarrito();

            // Mostrar interfaz
            loaderPOS.classList.add('d-none');
            interfazPOS.classList.remove('d-none');
            interfazPOS.classList.add('d-flex');
        } else {
            Swal.fire('Error', 'No se pudo cargar la información del POS.', 'error');
        }
    } catch (error) {
        console.error(error);
        Swal.fire('Error de conexión', 'No se pudo comunicar con el servidor.', 'error');
    }
}

// 4. Lógica de Cliente
function configurarBuscadorCliente() {
    buscadorCliente.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        resultadosCliente.innerHTML = '';
        
        if (query.length < 2) {
            resultadosCliente.classList.add('d-none');
            return;
        }

        const filtrados = listaClientes.filter(c => 
            c.nombre_razon_social.toLowerCase().includes(query) || 
            c.identificacion.includes(query)
        ).slice(0, 5); // Max 5 resultados

        if (filtrados.length > 0) {
            filtrados.forEach(c => {
                const a = document.createElement('a');
                a.href = '#';
                a.className = 'list-group-item list-group-item-action py-2';
                a.innerHTML = `<div class="fw-bold">${c.nombre_razon_social}</div><small class="text-muted">CC/NIT: ${c.identificacion}</small>`;
                a.onclick = (e) => {
                    e.preventDefault();
                    seleccionarCliente(c);
                };
                resultadosCliente.appendChild(a);
            });
            resultadosCliente.classList.remove('d-none');
        } else {
            resultadosCliente.innerHTML = `<div class="list-group-item text-muted py-2 small">No se encontraron resultados.</div>`;
            resultadosCliente.classList.remove('d-none');
        }
    });

    // Ocultar resultados al hacer click fuera
    document.addEventListener('click', (e) => {
        if (!buscadorCliente.contains(e.target) && !resultadosCliente.contains(e.target)) {
            resultadosCliente.classList.add('d-none');
        }
    });
}

function seleccionarCliente(cliente) {
    clienteSeleccionado = cliente;
    clienteNombre.textContent = cliente.nombre_razon_social;
    clienteIdentificacion.textContent = `CC/NIT: ${cliente.identificacion}`;
    clienteTelefono.textContent = `Tel: ${cliente.telefono || 'N/A'}`;
    
    buscadorCliente.value = '';
    resultadosCliente.classList.add('d-none');
    btnRemoverCliente.style.display = 'inline-block';
}

function quitarCliente() {
    clienteSeleccionado = null;
    clienteNombre.textContent = 'Consumidor Final';
    clienteIdentificacion.textContent = 'CC/NIT: ---';
    clienteTelefono.textContent = 'Tel: ---';
    btnRemoverCliente.style.display = 'none';
}

window.abrirModalNuevoCliente = function() {
    if(!modalNuevoClienteObj) {
        modalNuevoClienteObj = new bootstrap.Modal(document.getElementById('modalNuevoCliente'));
    }
    formNuevoCliente.reset();
    modalNuevoClienteObj.show();
}

function guardarClienteRapido(e) {
    e.preventDefault();
    const btnSubmit = document.getElementById('btnGuardarClienteRápido');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';

    const formData = new FormData();
    formData.append('identificacion', document.getElementById('nc_identificacion').value);
    formData.append('nombre_razon_social', document.getElementById('nc_nombre').value);
    formData.append('telefono', document.getElementById('nc_telefono').value);

    fetch('../../controllers/FacturaController.php?action=crear_cliente_ajax', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            listaClientes.push(data.cliente);
            seleccionarCliente(data.cliente);
            modalNuevoClienteObj.hide();
            Swal.fire({
                toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
                icon: 'success', title: 'Cliente registrado y seleccionado.'
            });
        } else {
            Swal.fire('Error', data.error, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Fallo de conexión al guardar cliente.', 'error');
    })
    .finally(() => {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = 'Guardar y Seleccionar';
    });
}

// 5. Lógica de Productos
function configurarBuscadorProducto() {
    buscadorProductosPOS.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        resultadosProducto.innerHTML = '';
        
        if (query.length < 2) {
            resultadosProducto.classList.add('d-none');
            lblStockDisponible.textContent = '--';
            lblProductoSeleccionado.textContent = 'Ningún producto seleccionado';
            return;
        }

        const filtrados = catalogoProductos.filter(p => 
            p.nombre_producto.toLowerCase().includes(query) || 
            (p.codigo_barras && p.codigo_barras.toLowerCase().includes(query))
        ).slice(0, 10);

        if (filtrados.length > 0) {
            filtrados.forEach(p => {
                const a = document.createElement('a');
                a.href = '#';
                a.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2';
                a.innerHTML = `
                    <div><div class="fw-bold">${p.nombre_producto}</div><small class="text-muted">${p.codigo_barras || 'Sin código'}</small></div>
                    <div class="text-end fw-bold text-primary">${formatter.format(p.precio_venta)}</div>
                `;
                
                // Mostrar stock al hacer hover (simulando "focus")
                a.addEventListener('mouseenter', () => {
                    productoEnFoco = p;
                    lblStockDisponible.textContent = p.stock_actual;
                    lblStockDisponible.className = p.stock_actual > 5 ? 'text-success fw-bold' : 'text-danger fw-bold';
                    lblProductoSeleccionado.textContent = p.nombre_producto;
                });

                a.onclick = (e) => {
                    e.preventDefault();
                    agregarAlCarrito(p);
                    buscadorProductosPOS.value = '';
                    resultadosProducto.classList.add('d-none');
                    buscadorProductosPOS.focus();
                };
                resultadosProducto.appendChild(a);
            });
            resultadosProducto.classList.remove('d-none');
        } else {
            resultadosProducto.innerHTML = `<div class="list-group-item text-muted py-2 small">No se encontraron productos.</div>`;
            resultadosProducto.classList.remove('d-none');
        }
    });

    document.addEventListener('click', (e) => {
        if (!buscadorProductosPOS.contains(e.target) && !resultadosProducto.contains(e.target)) {
            resultadosProducto.classList.add('d-none');
        }
    });
}

// 6. Manejo del Carrito
window.agregarAlCarrito = function(productoDB) {
    const index = carrito.findIndex(item => item.id_producto === productoDB.id_producto);
    
    if (index > -1) {
        if(carrito[index].cantidad + 1 > productoDB.stock_actual) {
            Swal.fire({toast: true, position: 'top-end', icon: 'warning', title: 'Stock insuficiente', showConfirmButton: false, timer: 2000});
            return;
        }
        carrito[index].cantidad++;
    } else {
        if(1 > productoDB.stock_actual) {
            Swal.fire({toast: true, position: 'top-end', icon: 'error', title: 'Producto agotado', showConfirmButton: false, timer: 2000});
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
        Swal.fire({toast: true, position: 'top-end', icon: 'warning', title: 'Límite de stock', showConfirmButton: false, timer: 2000});
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

    if (carrito.length === 0) {
        cuerpoCarrito.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted">El carrito está vacío</td></tr>`;
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
                <td class="py-3">
                    <div class="fw-semibold text-dark text-truncate" style="max-width: 180px;" title="${item.nombre}">${item.nombre}</div>
                </td>
                <td class="text-center py-3">
                    <div class="input-group input-group-sm rounded-3 shadow-none border mx-auto" style="width: 90px; background: #f8fafc;">
                        <button class="btn btn-light border-0 px-2 text-primary hover-bg-light" onclick="modificarCantidad(${index}, -1)"><i class="fa-solid fa-minus"></i></button>
                        <input type="text" class="form-control text-center border-0 bg-transparent px-0 fw-bold" value="${item.cantidad}" readonly>
                        <button class="btn btn-light border-0 px-2 text-primary hover-bg-light" onclick="modificarCantidad(${index}, 1)"><i class="fa-solid fa-plus"></i></button>
                    </div>
                </td>
                <td class="text-end py-3 text-muted">
                    ${formatter.format(item.precio)}
                </td>
                <td class="text-center py-3 text-muted small">
                    ${item.iva}%
                </td>
                <td class="text-end py-3 fw-bold text-dark">
                    ${formatter.format(totalLinea)}
                </td>
                <td class="text-center py-3">
                    <button class="btn btn-sm btn-outline-danger border-0" onclick="removerDelCarrito(${index})" title="Quitar"><i class="fa-regular fa-trash-can"></i></button>
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

// 7. Lectura por Voz (RF-12)
function leerFacturaVoz() {
    if (carrito.length === 0) {
        Swal.fire({toast: true, position: 'top-end', icon: 'info', title: 'Agrega productos primero', showConfirmButton: false, timer: 2000});
        return;
    }

    if (!('speechSynthesis' in window)) {
        Swal.fire('Error', 'Tu navegador no soporta la lectura por voz.', 'error');
        return;
    }

    const totalStr = formatter.format(btnProcesar.dataset.total);
    let textoALeer = `Factura por un total de ${totalStr}. Los productos son: `;
    
    carrito.forEach(item => {
        textoALeer += `${item.cantidad} unidades de ${item.nombre}, `;
    });

    const utterance = new SpeechSynthesisUtterance(textoALeer);
    utterance.lang = 'es-CO'; // Español Colombia
    utterance.rate = 1.0;
    
    window.speechSynthesis.speak(utterance);
}

// Inicializar eventos al cargar el DOM
document.addEventListener('DOMContentLoaded', () => {
    
    loaderPOS = document.getElementById('loaderPOS');
    interfazPOS = document.getElementById('interfazPOS');
    cuerpoCarrito = document.getElementById('cuerpoCarrito');
    
    txtSubtotal = document.getElementById('txtSubtotal');
    txtIva = document.getElementById('txtIva');
    txtTotal = document.getElementById('txtTotal');
    
    btnProcesar = document.getElementById('btnProcesar');
    btnLeerFactura = document.getElementById('btnLeerFactura');
    
    buscadorCliente = document.getElementById('buscadorCliente');
    resultadosCliente = document.getElementById('resultadosCliente');
    clienteNombre = document.getElementById('clienteNombre');
    clienteIdentificacion = document.getElementById('clienteIdentificacion');
    clienteTelefono = document.getElementById('clienteTelefono');
    btnRemoverCliente = document.getElementById('btnRemoverCliente');
    
    buscadorProductosPOS = document.getElementById('buscadorProductosPOS');
    resultadosProducto = document.getElementById('resultadosProducto');
    lblStockDisponible = document.getElementById('lblStockDisponible');
    lblProductoSeleccionado = document.getElementById('lblProductoSeleccionado');
    
    formNuevoCliente = document.getElementById('formNuevoCliente');

    // Inicializar Configuración
    configurarBuscadorCliente();
    configurarBuscadorProducto();

    btnRemoverCliente.addEventListener('click', quitarCliente);
    formNuevoCliente.addEventListener('submit', guardarClienteRapido);
    btnLeerFactura.addEventListener('click', leerFacturaVoz);

    // Procesar Factura
    btnProcesar.addEventListener('click', () => {
        if (carrito.length === 0) {
            Swal.fire('Carrito Vacío', 'Agrega al menos un producto para facturar.', 'warning');
            return;
        }

        const dataPayload = {
            id_cliente: clienteSeleccionado ? clienteSeleccionado.id_cliente : null,
            subtotal: btnProcesar.dataset.subtotal,
            total_iva: btnProcesar.dataset.iva,
            total_pagar: btnProcesar.dataset.total,
            detalles: carrito
        };

        btnProcesar.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Procesando...';
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
                btnProcesar.innerHTML = 'CONFIRMAR VENTA';
                btnProcesar.disabled = false;
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire('Error Crítico', 'Falló la comunicación con el servidor.', 'error');
            btnProcesar.innerHTML = 'CONFIRMAR VENTA';
            btnProcesar.disabled = false;
        });
    });

    // Arrancar la inicialización AJAX
    inicializarPOS();
});
