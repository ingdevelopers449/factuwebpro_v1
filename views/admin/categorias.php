<?php 
// Vista de Categorías
$current_page = 'categorias.php';
require_once '../../controllers/CategoriaController.php';

$controller = new CategoriaController();
$categorias = $controller->index();

// --- Paginación ---
$por_pagina = 8;
$total_categorias = count($categorias);
$total_paginas = max(1, ceil($total_categorias / $por_pagina));
$pagina_actual = max(1, min((int)($_GET['pagina'] ?? 1), $total_paginas));
$offset = ($pagina_actual - 1) * $por_pagina;
$categorias_pagina = array_slice($categorias, $offset, $por_pagina);

// Contadores
$activas   = count(array_filter($categorias, fn($c) => strtolower($c['estado']) === 'activa'));
$inactivas = $total_categorias - $activas;

require_once '../layouts/header.php'; 
?>

<div class="container-fluid py-4" style="max-width:1400px;">

    <!-- ── ENCABEZADO ───────────────────────────────────────── -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h3 fw-bold mb-1" style="font-family:var(--font-heading);">
                <span style="color:#f59e0b;">Gestión</span> <span class="text-dark">de Categorías</span>
            </h2>
            <p class="text-muted small mb-0">Organiza tu inventario por agrupaciones de productos</p>
        </div>
        <button class="btn shadow-sm border-0 px-4 py-2 fw-semibold text-white rounded-pill"
                style="background:linear-gradient(135deg,#f59e0b 0%,#ea580c 100%);"
                onclick="abrirModalCategoria()">
            <i class="fa-solid fa-plus me-2"></i> Nueva Categoría
        </button>
    </div>

    <!-- ── KPI CARDS ────────────────────────────────────────── -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-4">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:50px;height:50px;background:rgba(234,88,12,.1);">
                        <i class="fa-solid fa-tags fs-5" style="color:#ea580c;"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0 fw-bold text-uppercase" style="letter-spacing:.5px;">Total</p>
                        <h4 class="fw-bold text-dark mb-0"><?= $total_categorias ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:50px;height:50px;background:rgba(16,185,129,.1);">
                        <i class="fa-solid fa-circle-check fs-5" style="color:#10b981;"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0 fw-bold text-uppercase" style="letter-spacing:.5px;">Activas</p>
                        <h4 class="fw-bold text-dark mb-0"><?= $activas ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:50px;height:50px;background:rgba(239,68,68,.1);">
                        <i class="fa-solid fa-circle-xmark fs-5" style="color:#ef4444;"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0 fw-bold text-uppercase" style="letter-spacing:.5px;">Inactivas</p>
                        <h4 class="fw-bold text-dark mb-0"><?= $inactivas ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── BUSCADOR + TABLA ─────────────────────────────────── -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">

        <!-- Buscador -->
        <div class="card-header bg-white border-0 p-4 pb-3">
            <div class="row align-items-center g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-search"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 shadow-none" id="buscadorCategorias" placeholder="Buscar por nombre o descripción...">
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-muted small">Mostrando <?= count($categorias_pagina) ?> de <?= $total_categorias ?> categorías</span>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tablaCategorias">
                <thead>
                    <tr style="background:#f8f9fa;">
                        <th class="ps-4 py-3 text-muted small fw-bold text-uppercase border-0">Categoría</th>
                        <th class="py-3 text-muted small fw-bold text-uppercase border-0">Descripción</th>
                        <th class="py-3 text-muted small fw-bold text-uppercase border-0 text-center">Productos</th>
                        <th class="py-3 text-muted small fw-bold text-uppercase border-0">Estado</th>
                        <th class="py-3 text-muted small fw-bold text-uppercase border-0">Creación</th>
                        <th class="text-center pe-4 py-3 text-muted small fw-bold text-uppercase border-0">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($categorias_pagina)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fa-solid fa-folder-open fa-2x d-block mb-2 text-muted"></i>
                                <span class="text-muted">No hay categorías registradas.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categorias_pagina as $cat): ?>
                            <tr>
                                <!-- Nombre con ícono -->
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                                             style="width:38px;height:38px;background:linear-gradient(135deg,#12102f,#ea580c);min-width:38px;">
                                            <i class="fa-solid fa-tag text-white" style="font-size:.85rem;"></i>
                                        </div>
                                        <span class="fw-semibold text-dark"><?= htmlspecialchars($cat['nombre_categoria']) ?></span>
                                    </div>
                                </td>
                                <!-- Descripción -->
                                <td class="py-3">
                                    <?php if (!empty($cat['descripcion'])): ?>
                                        <span class="d-inline-block text-truncate text-muted small" style="max-width:280px;" title="<?= htmlspecialchars($cat['descripcion']) ?>">
                                            <?= htmlspecialchars($cat['descripcion']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic small">Sin descripción</span>
                                    <?php endif; ?>
                                </td>
                                <!-- Productos asociados -->
                                <td class="py-3 text-center">
                                    <?php $tp = (int)($cat['total_productos'] ?? 0); ?>
                                    <?php if ($tp > 0): ?>
                                        <span class="badge rounded-pill px-3 py-2" style="background:rgba(234,88,12,.1);color:#ea580c;">
                                            <i class="fa-solid fa-box-open me-1" style="font-size:.7rem;"></i><?= $tp ?> producto<?= $tp !== 1 ? 's' : '' ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">Sin productos</span>
                                    <?php endif; ?>
                                </td>
                                <!-- Estado -->
                                <td class="py-3">
                                    <?php if (strtolower($cat['estado']) === 'activa'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                                            <i class="fa-solid fa-circle-check me-1"></i> Activa
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">
                                            <i class="fa-solid fa-circle-xmark me-1"></i> Inactiva
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <!-- Fecha -->
                                <td class="py-3 text-muted small"><?= date('d/m/Y h:i A', strtotime($cat['fecha_creacion'])) ?></td>
                                <!-- Acciones -->
                                <td class="text-center pe-4 py-3">
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- Editar -->
                                        <button type="button"
                                                class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center border shadow-sm"
                                                style="width:35px;height:35px;color:#3b82f6;"
                                                title="Editar"
                                                onclick='editarCategoria(<?= json_encode($cat) ?>)'>
                                            <i class="fa-solid fa-pen" style="font-size:.8rem;"></i>
                                        </button>
                                        <!-- Activar / Desactivar -->
                                        <form action="../../controllers/CategoriaController.php?action=cambiar_estado" method="POST" class="m-0 p-0 d-inline form-toggle-estado">
                                            <input type="hidden" name="id_categoria" value="<?= $cat['id_categoria'] ?>">
                                            <input type="hidden" name="estado_actual" value="<?= $cat['estado'] ?>">
                                            <?php $es_activa = strtolower($cat['estado']) === 'activa'; ?>
                                            <button type="submit"
                                                    class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center border shadow-sm"
                                                    style="width:35px;height:35px;color:<?= $es_activa ? '#ef4444' : '#10b981' ?>;"
                                                    title="<?= $es_activa ? 'Desactivar' : 'Activar' ?>"
                                                    data-tiene-productos="<?= $tp ?>"
                                                    data-nombre="<?= htmlspecialchars($cat['nombre_categoria']) ?>"
                                                    data-accion="<?= $es_activa ? 'desactivar' : 'activar' ?>">
                                                <i class="fa-solid <?= $es_activa ? 'fa-ban' : 'fa-check' ?>" style="font-size:.8rem;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- ── PAGINACIÓN ───────────────────────────────────── -->
        <?php if ($total_paginas > 1): ?>
        <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center px-4 py-3">
            <span class="text-muted small">
                Página <?= $pagina_actual ?> de <?= $total_paginas ?>
            </span>
            <nav>
                <ul class="pagination pagination-sm mb-0 gap-1">
                    <!-- Anterior -->
                    <li class="page-item <?= $pagina_actual <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link rounded-pill border-0 px-3 shadow-sm" href="?pagina=<?= $pagina_actual - 1 ?>">
                            <i class="fa-solid fa-chevron-left" style="font-size:.7rem;"></i>
                        </a>
                    </li>
                    <?php
                    // Rango de páginas a mostrar
                    $rango = 2;
                    $inicio = max(1, $pagina_actual - $rango);
                    $fin    = min($total_paginas, $pagina_actual + $rango);
                    ?>
                    <?php if ($inicio > 1): ?>
                        <li class="page-item">
                            <a class="page-link rounded-pill border-0 px-3" href="?pagina=1">1</a>
                        </li>
                        <?php if ($inicio > 2): ?>
                            <li class="page-item disabled"><span class="page-link border-0 bg-transparent">…</span></li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php for ($p = $inicio; $p <= $fin; $p++): ?>
                        <li class="page-item <?= $p === $pagina_actual ? 'active' : '' ?>">
                            <a class="page-link rounded-pill border-0 px-3 <?= $p === $pagina_actual ? 'text-white shadow-sm' : '' ?>"
                               href="?pagina=<?= $p ?>"
                               <?php if ($p === $pagina_actual): ?>style="background:linear-gradient(135deg,#ea580c,#f59e0b);"<?php endif; ?>>
                                <?= $p ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($fin < $total_paginas): ?>
                        <?php if ($fin < $total_paginas - 1): ?>
                            <li class="page-item disabled"><span class="page-link border-0 bg-transparent">…</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link rounded-pill border-0 px-3" href="?pagina=<?= $total_paginas ?>"><?= $total_paginas ?></a>
                        </li>
                    <?php endif; ?>
                    <!-- Siguiente -->
                    <li class="page-item <?= $pagina_actual >= $total_paginas ? 'disabled' : '' ?>">
                        <a class="page-link rounded-pill border-0 px-3 shadow-sm" href="?pagina=<?= $pagina_actual + 1 ?>">
                            <i class="fa-solid fa-chevron-right" style="font-size:.7rem;"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>

    </div>

</div>

<!-- ── MODAL CATEGORÍA ──────────────────────────────────────── -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom border-light p-4">
                <h5 class="modal-title fw-bold text-dark" id="modalCategoriaTitle" style="font-family:var(--font-heading);">
                    <i class="fa-solid fa-tags me-2" style="color:#f59e0b;"></i>Nueva Categoría
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controllers/CategoriaController.php?action=guardar" method="POST">
                <input type="hidden" name="id_categoria" id="form_id_categoria" value="0">
                <div class="modal-body p-4 d-flex flex-column gap-3">
                    <!-- Nombre -->
                    <div>
                        <label class="form-label text-secondary small fw-bold">Nombre de la Categoría *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-tag"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" name="nombre_categoria" id="form_nombre_categoria" required placeholder="Ej: Bebidas, Electrónicos...">
                        </div>
                    </div>
                    <!-- Descripción -->
                    <div>
                        <label class="form-label text-secondary small fw-bold">Descripción</label>
                        <textarea class="form-control" name="descripcion" id="form_descripcion" rows="3" placeholder="Descripción breve de la categoría (opcional)"></textarea>
                    </div>
                    <!-- Estado -->
                    <div id="divEstado">
                        <label class="form-label text-secondary small fw-bold">Estado</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="estado" id="estadoActiva" value="activa" checked
                                       style="border-color:#10b981;">
                                <label class="form-check-label small fw-semibold" for="estadoActiva">
                                    <i class="fa-solid fa-circle-check text-success me-1"></i> Activa
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="estado" id="estadoInactiva" value="inactiva"
                                       style="border-color:#ef4444;">
                                <label class="form-check-label small fw-semibold" for="estadoInactiva">
                                    <i class="fa-solid fa-circle-xmark text-danger me-1"></i> Inactiva
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-light p-4">
                    <button type="button" class="btn btn-light border rounded-3 px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white px-4 border-0 shadow-sm"
                            style="background:linear-gradient(135deg,#f59e0b 0%,#ea580c 100%);">
                        <i class="fa-solid fa-save me-2"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../layouts/footer.php'; ?>

<script>
    let modalCategoriaObj;

    document.addEventListener('DOMContentLoaded', () => {
        modalCategoriaObj = new bootstrap.Modal(document.getElementById('modalCategoria'));

        // ── Buscador Front-end ───
        const buscador = document.getElementById('buscadorCategorias');
        const tabla = document.getElementById('tablaCategorias');
        const filas = tabla.querySelectorAll('tbody tr');

        buscador.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            filas.forEach(fila => {
                if (fila.cells.length > 1) {
                    const nombre = fila.cells[0].textContent.toLowerCase();
                    const desc = fila.cells[1].textContent.toLowerCase();
                    fila.style.display = (nombre.includes(query) || desc.includes(query)) ? '' : 'none';
                }
            });
        });

        // ── SweetAlert para desactivar con productos asociados (HU-015.5) ───
        document.querySelectorAll('.form-toggle-estado').forEach(form => {
            form.addEventListener('submit', function(e) {
                const btn = this.querySelector('button[type="submit"]');
                const tieneProductos = parseInt(btn.dataset.tieneProductos || '0');
                const nombre = btn.dataset.nombre || '';
                const accion = btn.dataset.accion || '';

                if (accion === 'desactivar' && tieneProductos > 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: '¿Desactivar categoría?',
                        html: `La categoría <strong>"${nombre}"</strong> tiene <strong>${tieneProductos} producto(s)</strong> asociados.<br>Se ocultará del selector de facturación sin eliminar los productos.`,
                        showCancelButton: true,
                        confirmButtonText: 'Sí, desactivar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        background: '#1f2937',
                        color: '#fff'
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                }
            });
        });
    });

    // ── Modal: Nueva Categoría ───
    window.abrirModalCategoria = function() {
        document.getElementById('modalCategoriaTitle').innerHTML = '<i class="fa-solid fa-tags me-2" style="color:#f59e0b;"></i>Nueva Categoría';
        document.getElementById('form_id_categoria').value = '0';
        document.getElementById('form_nombre_categoria').value = '';
        document.getElementById('form_descripcion').value = '';
        document.getElementById('estadoActiva').checked = true;
        document.getElementById('divEstado').style.display = 'block';
        modalCategoriaObj.show();
    }

    // ── Modal: Editar Categoría ───
    window.editarCategoria = function(cat) {
        document.getElementById('modalCategoriaTitle').innerHTML = '<i class="fa-solid fa-pen-to-square me-2" style="color:#f59e0b;"></i>Editar Categoría';
        document.getElementById('form_id_categoria').value = cat.id_categoria;
        document.getElementById('form_nombre_categoria').value = cat.nombre_categoria;
        document.getElementById('form_descripcion').value = cat.descripcion;

        if (cat.estado === 'activa') {
            document.getElementById('estadoActiva').checked = true;
        } else {
            document.getElementById('estadoInactiva').checked = true;
        }
        document.getElementById('divEstado').style.display = 'block';
        modalCategoriaObj.show();
    }
</script>
