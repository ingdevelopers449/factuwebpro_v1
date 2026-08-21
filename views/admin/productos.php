<?php 
// Vista de Productos
require_once '../../controllers/ProductosController.php';

$controller = new ProductosController();
extract($controller->index());

require_once '../../models/Categoria.php';
$categoriaModel = new Categoria();
$categoriasActivas = $categoriaModel->obtenerActivas();

require_once '../layouts/header.php'; 
?>

<div class="container-fluid py-4">
    <!-- Encabezado de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1 text-uppercase" style="font-family: var(--font-heading);">
                <span style="color: #f59e0b;">Productos</span>
            </h2>
            <p class="text-muted small mb-0">Administra el catálogo y las existencias de productos</p>
        </div>
        <div>
            <button type="button" class="btn btn-orange px-4 py-2 text-white shadow-sm" onclick="abrirModalNuevo()">
                <i class="fa-solid fa-plus me-2"></i> Nuevo producto
            </button>
        </div>
    </div>

    <!-- Buscador y Tabla (Card) -->
    <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
        
        <!-- Barra de búsqueda -->
        <div class="card-header bg-white border-bottom p-4">
            <form action="productos.php" method="GET" class="d-flex gap-2 w-100" style="max-width: 500px;">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="q" class="form-control bg-light border-start-0 ps-0" placeholder="Buscar por nombre o código..." value="<?= htmlspecialchars($termino) ?>">
                </div>
                <button type="submit" class="btn btn-primary px-4">Buscar</button>
                <?php if (!empty($termino)): ?>
                    <a href="productos.php" class="btn btn-light border" title="Limpiar búsqueda"><i class="fa-solid fa-xmark"></i></a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tabla -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3 fw-semibold border-bottom-0">Img</th>
                            <th class="py-3 fw-semibold border-bottom-0">Código</th>
                            <th class="py-3 fw-semibold border-bottom-0">Producto</th>
                            <th class="py-3 fw-semibold border-bottom-0">Compra</th>
                            <th class="py-3 fw-semibold border-bottom-0">Venta</th>
                            <th class="py-3 fw-semibold border-bottom-0 text-center">Stock</th>
                            <th class="py-3 fw-semibold border-bottom-0 text-center">IVA</th>
                            <th class="py-3 fw-semibold border-bottom-0 text-center">Estado</th>
                            <th class="py-3 fw-semibold border-bottom-0 text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php if (count($productos) > 0): ?>
                            <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td class="ps-4 py-2">
                                        <?php if (!empty($producto['imagen_url'])): ?>
                                            <img src="../../<?= htmlspecialchars($producto['imagen_url']) ?>" alt="Img" class="rounded object-fit-cover shadow-sm" width="40" height="40">
                                        <?php else: ?>
                                            <div class="bg-light text-secondary rounded d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                                <i class="fa-solid fa-box"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 text-muted">
                                        <?= htmlspecialchars($producto['codigo_barras'] ?: '---') ?>
                                    </td>
                                    <td class="py-3 text-dark fw-medium">
                                        <?= htmlspecialchars($producto['nombre_producto']) ?>
                                    </td>
                                    <td class="py-3 text-muted">
                                        $<?= number_format($producto['precio_compra'], 2) ?>
                                    </td>
                                    <td class="py-3 text-muted fw-semibold">
                                        $<?= number_format($producto['precio_venta'], 2) ?>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="badge <?= $producto['stock_actual'] > 5 ? 'bg-success-subtle text-success' : ($producto['stock_actual'] > 0 ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') ?> rounded-pill px-3 py-2">
                                            <?= htmlspecialchars($producto['stock_actual']) ?>
                                        </span>
                                    </td>
                                    <td class="py-3 text-center text-muted">
                                        <?= floatval($producto['tarifa_iva']) ?>%
                                    </td>
                                    <td class="py-3 text-center">
                                        <?php if(strtolower($producto['estado_producto']) === 'activo'): ?>
                                            <span class="badge bg-success rounded-pill px-3"><i class="fa-solid fa-circle-check me-1"></i> Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-pill px-3"><i class="fa-solid fa-ban me-1"></i> Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <!-- Botón Ver -->
                                            <button class="btn btn-sm btn-light border text-secondary" title="Ver" onclick='abrirModalEditar(<?= json_encode($producto) ?>)'>
                                                <i class="fa-regular fa-eye"></i>
                                            </button>
                                            <!-- Botón Editar -->
                                            <button class="btn btn-sm btn-light border text-primary" title="Editar" onclick='abrirModalEditar(<?= json_encode($producto) ?>)'>
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            
                                            <!-- Botón Alternar Estado -->
                                            <?php if(strtolower($producto['estado_producto']) === 'activo'): ?>
                                                <button class="btn btn-sm btn-light border text-danger btn-estado" title="Desactivar" data-id="<?= $producto['id_producto'] ?>" data-estado="activo">
                                                    <i class="fa-solid fa-power-off"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-light border text-success btn-estado" title="Activar" data-id="<?= $producto['id_producto'] ?>" data-estado="inactivo">
                                                    <i class="fa-solid fa-bolt"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="mb-3"><i class="fa-solid fa-box-open fs-1 text-light"></i></div>
                                    <?php if (!empty($termino)): ?>
                                        <p class="mb-0">No se encontraron productos que coincidan con "<strong><?= htmlspecialchars($termino) ?></strong>"</p>
                                    <?php else: ?>
                                        <p class="mb-0">No hay productos registrados en el catálogo.</p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <?php if ($total_pages > 1): ?>
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                    <nav aria-label="Navegación de páginas">
                        <ul class="pagination pagination-sm mb-0">
                            <!-- Botón Anterior -->
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="productos.php?page=<?= $page - 1 ?><?= !empty($termino) ? '&q='.urlencode($termino) : '' ?>" tabindex="-1">Anterior</a>
                            </li>
                            
                            <!-- Números de página -->
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="productos.php?page=<?= $i ?><?= !empty($termino) ? '&q='.urlencode($termino) : '' ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Botón Siguiente -->
                            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="productos.php?page=<?= $page + 1 ?><?= !empty($termino) ? '&q='.urlencode($termino) : '' ?>">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Producto -->
<div class="modal fade" id="productoModal" tabindex="-1" aria-labelledby="productoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-dark text-uppercase" id="productoModalLabel" style="font-family: var(--font-heading);"><i class="fa-solid fa-box me-2 text-primary"></i> <span id="modalTituloText">Nuevo Producto</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="../../controllers/ProductosController.php?action=guardar" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <div class="modal-body p-4">
                    
                    <input type="hidden" name="id_producto" id="id_producto">

                    <div class="row g-3">
                        <div class="col-md-4 mb-3">
                            <label for="codigo_barras" class="form-label fw-semibold text-secondary small">Código de barras</label>
                            <input type="text" class="form-control bg-light" id="codigo_barras" name="codigo_barras">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nombre_producto" class="form-label fw-semibold text-secondary small">Nombre del producto *</label>
                            <input type="text" class="form-control bg-light" id="nombre_producto" name="nombre_producto" required>
                            <div class="invalid-feedback">El nombre es obligatorio.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="id_categoria" class="form-label fw-semibold text-secondary small">Categoría</label>
                            <select class="form-select bg-light" id="id_categoria" name="id_categoria">
                                <option value="">-- Sin categoría --</option>
                                <?php foreach($categoriasActivas as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre_categoria']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="precio_compra" class="form-label fw-semibold text-secondary small">Precio de compra *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">$</span>
                                <input type="number" step="0.01" class="form-control bg-light" id="precio_compra" name="precio_compra" required>
                                <div class="invalid-feedback">Requerido.</div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="precio_venta" class="form-label fw-semibold text-secondary small">Precio de venta *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">$</span>
                                <input type="number" step="0.01" class="form-control bg-light" id="precio_venta" name="precio_venta" required>
                                <div class="invalid-feedback">Requerido.</div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="stock_actual" class="form-label fw-semibold text-secondary small">Stock actual *</label>
                            <input type="number" class="form-control bg-light" id="stock_actual" name="stock_actual" value="0" required>
                            <div class="invalid-feedback">Requerido.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tarifa_iva" class="form-label fw-semibold text-secondary small">Tarifa IVA</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control bg-light" id="tarifa_iva" name="tarifa_iva" value="19.00">
                                <span class="input-group-text bg-light text-muted">%</span>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-secondary small">Estado</label>
                            <div class="d-flex gap-4 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="estado_producto" id="estadoActivo" value="activo" checked>
                                    <label class="form-check-label" for="estadoActivo">Activo</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="estado_producto" id="estadoInactivo" value="inactivo">
                                    <label class="form-check-label" for="estadoInactivo">Inactivo</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="imagen_url" class="form-label fw-semibold text-secondary small">Imagen del Producto (Opcional)</label>
                            
                            <div class="d-flex align-items-center gap-3 mt-1">
                                <div id="previewImagenContainer" class="d-none">
                                    <img src="" id="previewImagen" class="rounded object-fit-cover shadow-sm border" width="60" height="60" alt="Vista previa">
                                </div>
                                <div class="flex-grow-1">
                                    <input class="form-control bg-light" type="file" id="imagen_url" name="imagen_url" accept="image/png, image/jpeg, image/webp">
                                    <div class="form-text small">Formatos permitidos: JPG, PNG, WEBP. Si subes una nueva, reemplazará a la actual.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-top bg-light px-4 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 text-white shadow-sm" id="btnGuardarProducto">Guardar producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Declaración de variables globales
    let productoModal;
    let modalTitulo;
    let btnGuardar;
    
    // Inputs del formulario
    let inputId, inputCodigo, inputNombre, inputCategoria, inputCompra, inputVenta, inputStock, inputIva, radioActivo, radioInactivo, inputImagen;

    document.addEventListener('DOMContentLoaded', function () {
        // Inicialización
        productoModal = new bootstrap.Modal(document.getElementById('productoModal'));
        modalTitulo = document.getElementById('modalTituloText');
        btnGuardar = document.getElementById('btnGuardarProducto');
        
        inputId = document.getElementById('id_producto');
        inputCodigo = document.getElementById('codigo_barras');
        inputNombre = document.getElementById('nombre_producto');
        inputCategoria = document.getElementById('id_categoria');
        inputCompra = document.getElementById('precio_compra');
        inputVenta = document.getElementById('precio_venta');
        inputStock = document.getElementById('stock_actual');
        inputIva = document.getElementById('tarifa_iva');
        radioActivo = document.getElementById('estadoActivo');
        radioInactivo = document.getElementById('estadoInactivo');
        inputImagen = document.getElementById('imagen_url');

        // Validación de Formularios Bootstrap
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                } else {
                    // HU-006.3 Alerta de pérdida
                    const compra = parseFloat(inputCompra.value) || 0;
                    const venta = parseFloat(inputVenta.value) || 0;
                    
                    if (venta <= compra) {
                        event.preventDefault(); // Pausar envío
                        Swal.fire({
                            title: '¿Margen de pérdida?',
                            text: 'El precio de venta es menor o igual al precio de compra. ¿Estás seguro de registrar este producto así?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ea580c',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Sí, guardar de todos modos',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Remover listener temporalmente y enviar
                                form.submit();
                            }
                        });
                        return;
                    }
                }
                form.classList.add('was-validated')
            }, false)
        });

        // Confirmación SweetAlert para Desactivar / Activar
        const btnEstadoList = document.querySelectorAll('.btn-estado');
        btnEstadoList.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const estado = this.getAttribute('data-estado'); // estado actual
                
                const esDesactivar = (estado === 'activo');
                const title = esDesactivar ? '¿Desactivar producto?' : '¿Activar producto?';
                const text = esDesactivar 
                    ? "El producto dejará de estar disponible para nuevas facturas."
                    : "El producto volverá a estar disponible en el inventario.";
                const confirmColor = esDesactivar ? '#ef4444' : '#10b981';
                const confirmText = esDesactivar ? 'Sí, desactivar' : 'Sí, activar';

                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: confirmColor,
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `../../controllers/ProductosController.php?action=alternar_estado&id_producto=${id}&estado=${estado}`;
                    }
                });
            });
        });
    });

    window.abrirModalNuevo = function() {
        // Limpiar campos
        inputId.value = '';
        inputCodigo.value = '';
        inputNombre.value = '';
        inputCategoria.value = '';
        inputCompra.value = '';
        inputVenta.value = '';
        inputStock.value = '0';
        inputIva.value = '19.00';
        radioActivo.checked = true;
        inputImagen.value = '';
        
        document.getElementById('previewImagenContainer').classList.add('d-none');
        document.getElementById('previewImagen').src = '';
        
        // Reset validaciones visuales
        document.querySelector('#productoModal form').classList.remove('was-validated');

        modalTitulo.textContent = 'Nuevo Producto';
        btnGuardar.textContent = 'Guardar producto';
        productoModal.show();
    }

    window.abrirModalEditar = function(producto) {
        // Llenar campos
        inputId.value = producto.id_producto;
        inputCodigo.value = producto.codigo_barras || '';
        inputNombre.value = producto.nombre_producto;
        inputCategoria.value = producto.id_categoria || '';
        inputCompra.value = producto.precio_compra;
        inputVenta.value = producto.precio_venta;
        inputStock.value = producto.stock_actual;
        inputIva.value = producto.tarifa_iva;
        inputImagen.value = ''; // No se puede pre-cargar archivos por seguridad en navegadores
        
        if (producto.imagen_url) {
            document.getElementById('previewImagen').src = '../../' + producto.imagen_url;
            document.getElementById('previewImagenContainer').classList.remove('d-none');
        } else {
            document.getElementById('previewImagenContainer').classList.add('d-none');
            document.getElementById('previewImagen').src = '';
        }
        
        if(producto.estado_producto && producto.estado_producto.toLowerCase() === 'inactivo') {
            radioInactivo.checked = true;
        } else {
            radioActivo.checked = true;
        }

        // Reset validaciones visuales
        document.querySelector('#productoModal form').classList.remove('was-validated');

        modalTitulo.textContent = 'Editar Producto';
        btnGuardar.textContent = 'Actualizar producto';
        productoModal.show();
    }
</script>

<?php require_once '../layouts/footer.php'; ?>
