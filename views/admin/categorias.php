<?php 
// Vista de Categorías
$current_page = 'categorias.php';
require_once '../../controllers/CategoriaController.php';

$controller = new CategoriaController();
$categorias = $controller->index();

require_once '../layouts/header.php'; 
?>

<div class="container-fluid py-4">
    
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1" style="font-family: var(--font-heading);">
                <i class="fa-solid fa-tags text-primary me-2"></i> Gestión de Categorías
            </h2>
            <p class="text-muted small mb-0">Administra las agrupaciones de tus productos</p>
        </div>
        <button class="btn btn-primary shadow-sm" onclick="abrirModalCategoria()">
            <i class="fa-solid fa-plus me-2"></i> Nueva Categoría
        </button>
    </div>

    <!-- Filtros / Buscador -->
    <div class="card border-0 shadow-sm mb-4 rounded-4 bg-white">
        <div class="card-body p-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-start-0" id="buscadorCategorias" placeholder="Buscar categoría...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Data -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tablaCategorias">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4">Categoría</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($categorias)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No hay categorías registradas en el sistema.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categorias as $cat): ?>
                            <tr>
                                <td class="ps-4 fw-semibold text-dark"><?= htmlspecialchars($cat['nombre_categoria']) ?></td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($cat['descripcion']) ?>">
                                        <?= htmlspecialchars($cat['descripcion']) ?: '<span class="text-muted fst-italic">Sin descripción</span>' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (strtolower($cat['estado']) === 'activa'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Activa</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Inactiva</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small">
                                    <?= date('d/m/Y h:i A', strtotime($cat['fecha_creacion'])) ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li>
                                                <a class="dropdown-item" href="#" onclick='editarCategoria(<?= json_encode($cat) ?>)'>
                                                    <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Editar
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="../../controllers/CategoriaController.php?action=cambiar_estado" method="POST" class="m-0 p-0 d-inline">
                                                    <input type="hidden" name="id_categoria" value="<?= $cat['id_categoria'] ?>">
                                                    <input type="hidden" name="estado_actual" value="<?= $cat['estado'] ?>">
                                                    <button type="submit" class="dropdown-item <?= strtolower($cat['estado']) === 'activa' ? 'text-danger' : 'text-success' ?>">
                                                        <i class="fa-solid <?= strtolower($cat['estado']) === 'activa' ? 'fa-ban' : 'fa-check' ?> me-2"></i>
                                                        <?= strtolower($cat['estado']) === 'activa' ? 'Desactivar' : 'Activar' ?>
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Categoría -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-dark text-uppercase" id="modalCategoriaTitle" style="font-family: var(--font-heading);">Nueva Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controllers/CategoriaController.php?action=guardar" method="POST">
                <input type="hidden" name="id_categoria" id="form_id_categoria" value="0">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Nombre de la Categoría *</label>
                        <input type="text" class="form-control bg-light" name="nombre_categoria" id="form_nombre_categoria" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Descripción</label>
                        <textarea class="form-control bg-light" name="descripcion" id="form_descripcion" rows="3"></textarea>
                    </div>

                    <div class="mb-3" id="divEstado">
                        <label class="form-label fw-semibold text-secondary small">Estado</label>
                        <select class="form-select bg-light" name="estado" id="form_estado">
                            <option value="activa">Activa</option>
                            <option value="inactiva">Inactiva</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-4 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 text-white shadow-sm"><i class="fa-solid fa-save me-2"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../layouts/footer.php'; ?>

<script>
    // Variables Modal
    let modalCategoriaObj;
    
    document.addEventListener('DOMContentLoaded', () => {
        modalCategoriaObj = new bootstrap.Modal(document.getElementById('modalCategoria'));
        
        // Buscador Front-end
        const buscador = document.getElementById('buscadorCategorias');
        const tabla = document.getElementById('tablaCategorias');
        const filas = tabla.querySelectorAll('tbody tr');

        buscador.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            filas.forEach(fila => {
                if (fila.cells.length > 1) { // Ignorar fila de "No hay categorías"
                    const nombre = fila.cells[0].textContent.toLowerCase();
                    const desc = fila.cells[1].textContent.toLowerCase();
                    if (nombre.includes(query) || desc.includes(query)) {
                        fila.style.display = '';
                    } else {
                        fila.style.display = 'none';
                    }
                }
            });
        });
    });

    window.abrirModalCategoria = function() {
        document.getElementById('modalCategoriaTitle').textContent = 'Nueva Categoría';
        document.getElementById('form_id_categoria').value = '0';
        document.getElementById('form_nombre_categoria').value = '';
        document.getElementById('form_descripcion').value = '';
        document.getElementById('form_estado').value = 'activa';
        document.getElementById('divEstado').style.display = 'none'; // Ocultar estado al crear
        modalCategoriaObj.show();
    }

    window.editarCategoria = function(cat) {
        document.getElementById('modalCategoriaTitle').textContent = 'Editar Categoría';
        document.getElementById('form_id_categoria').value = cat.id_categoria;
        document.getElementById('form_nombre_categoria').value = cat.nombre_categoria;
        document.getElementById('form_descripcion').value = cat.descripcion;
        document.getElementById('form_estado').value = cat.estado;
        document.getElementById('divEstado').style.display = 'block'; // Mostrar estado al editar
        modalCategoriaObj.show();
    }
</script>
