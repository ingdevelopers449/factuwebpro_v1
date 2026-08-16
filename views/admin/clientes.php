<?php 
// Vista de Clientes
require_once '../../controllers/ClientesController.php';

$controller = new ClientesController();
extract($controller->index());

require_once '../layouts/header.php'; 
?>

<div class="container-fluid py-4">
    <!-- Encabezado de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1 text-uppercase" style="font-family: var(--font-heading);">
                <span style="color: #f59e0b;">Clientes</span>
            </h2>
            <p class="text-muted small mb-0">Gestión de clientes registrados</p>
        </div>
        <div>
            <button type="button" class="btn btn-orange px-4 py-2 text-white shadow-sm" onclick="abrirModalNuevo()">
                <i class="fa-solid fa-plus me-2"></i> Nuevo cliente
            </button>
        </div>
    </div>

    <!-- Buscador y Tabla (Card) -->
    <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
        
        <!-- Barra de búsqueda -->
        <div class="card-header bg-white border-bottom p-4">
            <form action="clientes.php" method="GET" class="d-flex gap-2 w-100" style="max-width: 500px;">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="q" class="form-control bg-light border-start-0 ps-0" placeholder="Buscar por nombre o identificación..." value="<?= htmlspecialchars($termino) ?>">
                </div>
                <button type="submit" class="btn btn-primary px-4">Buscar</button>
                <?php if (!empty($termino)): ?>
                    <a href="clientes.php" class="btn btn-light border" title="Limpiar búsqueda"><i class="fa-solid fa-xmark"></i></a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tabla -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3 fw-semibold border-bottom-0">Identificación</th>
                            <th class="py-3 fw-semibold border-bottom-0">Nombre/Razón social</th>
                            <th class="py-3 fw-semibold border-bottom-0">Teléfono</th>
                            <th class="py-3 fw-semibold border-bottom-0">Correo</th>
                            <th class="py-3 fw-semibold border-bottom-0 text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php if (count($clientes) > 0): ?>
                            <?php foreach ($clientes as $cliente): ?>
                                <tr>
                                    <td class="ps-4 py-3 text-dark fw-medium">
                                        <?= htmlspecialchars($cliente['identificacion']) ?>
                                    </td>
                                    <td class="py-3">
                                        <?= htmlspecialchars($cliente['nombre_razon_social']) ?>
                                    </td>
                                    <td class="py-3 text-muted">
                                        <?= htmlspecialchars($cliente['telefono'] ?: '---') ?>
                                    </td>
                                    <td class="py-3 text-muted">
                                        <?= htmlspecialchars($cliente['email'] ?: '---') ?>
                                    </td>
                                    <td class="py-3 text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <!-- Botón Ver (Opcional, puede abrir un modal solo de lectura o ir a otra página. Por ahora abre el mismo modal pero visualmente destaca como ver) -->
                                            <button class="btn btn-sm btn-light border text-secondary" title="Ver" onclick='abrirModalEditar(<?= json_encode($cliente) ?>)'>
                                                <i class="fa-regular fa-eye"></i>
                                            </button>
                                            <!-- Botón Editar -->
                                            <button class="btn btn-sm btn-light border text-primary" title="Editar" onclick='abrirModalEditar(<?= json_encode($cliente) ?>)'>
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <!-- Botón Eliminar (Implementación futura segura) -->
                                            <a href="../../controllers/ClientesController.php?action=eliminar&id_cliente=<?= $cliente['id_cliente'] ?>" class="btn btn-sm btn-light border text-danger btn-eliminar" title="Eliminar">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="mb-3"><i class="fa-solid fa-users-slash fs-1 text-light"></i></div>
                                    <?php if (!empty($termino)): ?>
                                        <p class="mb-0">No se encontraron clientes que coincidan con "<strong><?= htmlspecialchars($termino) ?></strong>"</p>
                                    <?php else: ?>
                                        <p class="mb-0">No hay clientes registrados en el sistema.</p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cliente -->
<div class="modal fade" id="clienteModal" tabindex="-1" aria-labelledby="clienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-dark text-uppercase" id="clienteModalLabel" style="font-family: var(--font-heading);"><i class="fa-solid fa-user me-2 text-primary"></i> <span id="modalTituloText">Nuevo Cliente</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="../../controllers/ClientesController.php?action=guardar" method="POST" class="needs-validation" novalidate>
                <div class="modal-body p-4">
                    
                    <input type="hidden" name="id_cliente" id="id_cliente">

                    <div class="mb-3">
                        <label for="identificacion" class="form-label fw-semibold text-secondary small">Identificación / NIT *</label>
                        <input type="text" class="form-control bg-light" id="identificacion" name="identificacion" required>
                        <div class="invalid-feedback">La identificación es obligatoria.</div>
                    </div>

                    <div class="mb-3">
                        <label for="nombre_razon_social" class="form-label fw-semibold text-secondary small">Nombre / Razón social *</label>
                        <input type="text" class="form-control bg-light" id="nombre_razon_social" name="nombre_razon_social" required>
                        <div class="invalid-feedback">El nombre es obligatorio.</div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold text-secondary small">Correo electrónico</label>
                        <input type="email" class="form-control bg-light" id="email" name="email">
                        <div class="invalid-feedback">Ingrese un correo válido.</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label fw-semibold text-secondary small">Teléfono</label>
                            <input type="text" class="form-control bg-light" id="telefono" name="telefono">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="direccion" class="form-label fw-semibold text-secondary small">Dirección</label>
                            <input type="text" class="form-control bg-light" id="direccion" name="direccion">
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-top bg-light px-4 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 text-white shadow-sm" id="btnGuardarCliente">Guardar cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Declaración de variables globales
    let clienteModal;
    let modalTitulo;
    let btnGuardar;
    
    // Inputs del formulario
    let inputId, inputIdentificacion, inputNombre, inputEmail, inputTelefono, inputDireccion;

    document.addEventListener('DOMContentLoaded', function () {
        // Inicialización (esto se ejecuta DESPUÉS de que se carga footer.php y Bootstrap JS)
        clienteModal = new bootstrap.Modal(document.getElementById('clienteModal'));
        modalTitulo = document.getElementById('modalTituloText');
        btnGuardar = document.getElementById('btnGuardarCliente');
        
        inputId = document.getElementById('id_cliente');
        inputIdentificacion = document.getElementById('identificacion');
        inputNombre = document.getElementById('nombre_razon_social');
        inputEmail = document.getElementById('email');
        inputTelefono = document.getElementById('telefono');
        inputDireccion = document.getElementById('direccion');

        // Validación de Formularios Bootstrap
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        });

        // Confirmación SweetAlert para Eliminar
        const btnEliminar = document.querySelectorAll('.btn-eliminar');
        btnEliminar.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer y borrará al cliente.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    });

    window.abrirModalNuevo = function() {
        // Limpiar campos
        inputId.value = '';
        inputIdentificacion.value = '';
        inputNombre.value = '';
        inputEmail.value = '';
        inputTelefono.value = '';
        inputDireccion.value = '';
        
        // Reset validaciones visuales
        document.querySelector('#clienteModal form').classList.remove('was-validated');

        modalTitulo.textContent = 'Nuevo Cliente';
        btnGuardar.textContent = 'Guardar cliente';
        clienteModal.show();
    }

    window.abrirModalEditar = function(cliente) {
        // Llenar campos
        inputId.value = cliente.id_cliente;
        inputIdentificacion.value = cliente.identificacion;
        inputNombre.value = cliente.nombre_razon_social;
        inputEmail.value = cliente.email;
        inputTelefono.value = cliente.telefono;
        inputDireccion.value = cliente.direccion;

        // Reset validaciones visuales
        document.querySelector('#clienteModal form').classList.remove('was-validated');

        modalTitulo.textContent = 'Editar Cliente';
        btnGuardar.textContent = 'Actualizar cliente';
        clienteModal.show();
    }
</script>

<?php require_once '../layouts/footer.php'; ?>
