<?php 
require_once '../../models/Usuario.php';
require_once '../../models/Rol.php';

$usuarioModel = new Usuario();
$rolModel = new Rol();

$usuarios = $usuarioModel->obtenerTodos();
$estados = $usuarioModel->obtenerEstados();
$roles = $rolModel->obtenerTodos();


require_once '../layouts/header.php'; 
?>

<div class="container-fluid py-2">
    <div class="row g-4">
        <!-- Main Card: User List -->
        <div class="col-12">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                    <div>
                        <h2 class="h3 fw-bold text-dark mb-1" style="font-family: var(--font-heading);">
                            <span style="color: #f59e0b;">Gestión</span> de Usuarios
                        </h2>
                        <p class="text-muted small mb-0">Administra las cuentas de usuario y sus privilegios de acceso al sistema.</p>
                    </div>
                    <button type="button" class="btn shadow-sm border-0 px-4 py-2 fw-semibold text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);" data-bs-toggle="modal" data-bs-target="#modalCrear">
                        <i class="fa-solid fa-plus me-2"></i> Agregar Usuario
                    </button>
                </div>



                <!-- Table -->
                <div class="mt-2 table-responsive">
                    <table id="usuariosTable" class="table table-hover align-middle border-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 px-4 py-3 text-muted small fw-bold text-uppercase">Id</th>
                                <th class="border-0 px-4 py-3 text-muted small fw-bold text-uppercase">Nombre Completo</th>
                                <th class="border-0 px-4 py-3 text-muted small fw-bold text-uppercase">Email</th>
                                <th class="border-0 px-4 py-3 text-muted small fw-bold text-uppercase">Rol</th>
                                <th class="border-0 px-4 py-3 text-muted small fw-bold text-uppercase">Comisión</th>
                                <th class="border-0 px-4 py-3 text-muted small fw-bold text-uppercase">Estado</th>
                                <th class="border-0 px-4 py-3 text-muted small fw-bold text-uppercase text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td class="px-4 py-3 fw-bold text-secondary">#<?= htmlspecialchars($usuario['id_usuario']) ?></td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; background: rgba(245, 158, 11, 0.1); color: #ea580c;">
                                            <?= strtoupper(substr($usuario['nombre'], 0, 1)) ?>
                                        </div>
                                        <span class="fw-semibold text-dark"><?= htmlspecialchars($usuario['nombre']) ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-muted"><?= htmlspecialchars($usuario['email']) ?></td>
                                <td class="px-4 py-3">
                                    <?php
                                    $rol_badge = 'bg-secondary';
                                    $rol_icon = 'fa-user';
                                    $rol_name = htmlspecialchars($usuario['nombre_rol'] ?? 'Indefinido');

                                    if ($usuario['id_rol'] == '1') {
                                        $rol_badge = 'bg-dark text-white';
                                        $rol_icon = 'fa-shield-halved text-warning';
                                    } elseif ($usuario['id_rol'] == '2') {
                                        $rol_badge = 'bg-primary text-white';
                                        $rol_icon = 'fa-shop';
                                    }
                                    ?>
                                    <span class="badge <?= $rol_badge ?> px-3 py-2 rounded-pill shadow-sm">
                                        <i class="fa-solid <?= $rol_icon ?> me-1"></i> <?= $rol_name ?>
                                    </span>
                                </td>
                                <!-- Comisión -->
                                <td class="px-4 py-3">
                                    <?php $comision = (float)($usuario['porcentaje_comision'] ?? 0); ?>
                                    <?php if ($comision > 0): ?>
                                        <span class="badge rounded-pill px-3 py-2" style="background:rgba(234,88,12,.12); color:#ea580c;">
                                            <i class="fa-solid fa-percent me-1" style="font-size:.7rem;"></i><?= number_format($comision,2) ?>%
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                     <?php if (strtolower($usuario['estado']) === 'activo' || $usuario['estado'] == 1 || $usuario['estado'] == '1'): ?>
                                         <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill border border-success border-opacity-25">
                                            <i class="fa-solid fa-circle-check me-1"></i> Activo
                                        </span> 
                                      <?php else: ?>
                                         <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill border border-danger border-opacity-25">
                                            <i class="fa-solid fa-circle-xmark me-1"></i> Inactivo
                                         </span>
                                      <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button"
                                            class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center border shadow-sm text-primary btn-editar"
                                            style="width: 35px; height: 35px;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditar"
                                            data-id="<?= $usuario['id_usuario'] ?>"
                                            data-name="<?= $usuario['nombre'] ?>"
                                            data-email="<?= $usuario['email'] ?>"
                                            data-rol="<?= $usuario['id_rol'] ?>"
                                            data-estado="<?= $usuario['estado'] ?>"
                                            data-comision="<?= htmlspecialchars($usuario['porcentaje_comision'] ?? '0') ?>"
                                            title="Editar">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button type="button" class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center border shadow-sm text-danger deletebtn" data-id="<?= $usuario['id_usuario'] ?>" style="width: 35px; height: 35px;" title="Eliminar">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Usuario -->
<div class="modal fade" id="modalCrear" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom border-light p-4">
                <h5 class="modal-title fw-bold text-dark mb-0" id="modalCrearLabel" style="font-family: var(--font-heading);">
                    <i class="fa-solid fa-user-plus me-2" style="color: #f59e0b;"></i>Agregar Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controllers/RegisterUsuarioAdmin.php" method="POST" class="needs-validation" novalidate>
                <div class="modal-body p-4 d-flex flex-column gap-3">
                    <div class="row g-3">
                        <div>
                            <label class="form-label text-secondary small fw-bold">Nombre Completo *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="nombre" required class="form-control border-start-0 ps-0" placeholder="Ej: Juan Pérez">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label text-secondary small fw-bold">Correo Electrónico *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" name="email" required class="form-control border-start-0 ps-0" placeholder="nombre@ejemplo.com">
                        </div>
                    </div>
                    <div>
                        <label class="form-label text-secondary small fw-bold">Contraseña *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="password" required class="form-control border-start-0 ps-0" placeholder="••••••••">
                        </div>
                    </div>
                    <div>
                        <label class="form-label text-secondary small fw-bold">Rol *</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-user-shield"></i></span>
                            <select name="id_rol" id="rolSelect" required class="form-select border-start-0 ps-0 text-muted">
                                <option value="">Seleccione un rol...</option>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?= htmlspecialchars($rol['id_rol']) ?>"><?= htmlspecialchars($rol['rol']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label text-secondary small fw-bold">Estado *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-toggle-on"></i></span>
                                <select name="estado" id="estadoSelect" required class="form-select border-start-0 ps-0 text-muted">
                                    <option value="">Seleccione un estado...</option>
                                    <?php foreach ($estados as $val => $texto): ?>
                                        <option value="<?= htmlspecialchars($val) ?>"><?= htmlspecialchars($texto) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!-- Comisión (solo aplica a rol 2) -->
                        <div>
                            <label class="form-label text-secondary small fw-bold">
                                <i class="fa-solid fa-percent me-1" style="color:#ea580c;"></i>% Comisión sobre ventas
                                <span class="text-muted fw-normal">(aplica a Empleados/Vendedores)</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="porcentaje_comision" min="0" max="100" step="0.01"
                                       class="form-control" placeholder="Ej: 5.00" value="0">
                                <span class="input-group-text bg-light text-muted">%</span>
                            </div>
                            <div class="form-text text-muted" style="font-size:.75rem;">
                                Se congela en cada factura al momento de la venta.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-light p-4">
                    <button type="button" class="btn btn-light border rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white px-4 border-0" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!---------------------------------------- Modal Editar Usuario --------------------------------------->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom border-light p-4">
                <h5 class="modal-title fw-bold text-dark mb-0" id="modalEditarLabel" style="font-family: var(--font-heading);">
                    <i class="fa-solid fa-user-pen me-2" style="color: #f59e0b;"></i>Editar Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controllers/EditUsuarioController.php" method="POST" class="needs-validation" novalidate>
                <div class="modal-body p-4 d-flex flex-column gap-3">
                    <div class="row g-3">
                        <div class="col-3">
                            <label class="form-label text-secondary small fw-bold">ID</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-hashtag"></i></span>
                                <input type="text" name="id_usuario" id="edit_id_usuario" readonly class="form-control bg-light border-start-0 ps-0 cursor-not-allowed text-muted fw-bold">
                            </div>
                        </div>
                        <div class="col-9">
                            <label class="form-label text-secondary small fw-bold">Nombre Completo *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="nombre" id="edit_usuario" required class="form-control border-start-0 ps-0">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label text-secondary small fw-bold">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" name="email" id="edit_email" readonly class="form-control bg-light border-start-0 ps-0 cursor-not-allowed text-muted">
                        </div>
                    </div>
                    <div>
                        <label class="form-label text-secondary small fw-bold">Rol *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-user-shield"></i></span>
                            <select name="id_rol" id="edit_rol" required class="form-select border-start-0 ps-0 text-muted">
                                <option value="">Seleccione un rol...</option>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?= htmlspecialchars($rol['id_rol']) ?>"><?= htmlspecialchars($rol['rol']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label text-secondary small fw-bold">Estado *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-toggle-on"></i></span>
                            <select name="estado" id="edit_estado" required class="form-select border-start-0 ps-0 text-muted">
                                <option value="">Seleccione un estado...</option>
                                <?php foreach ($estados as $val => $texto): ?>
                                    <option value="<?= htmlspecialchars($val) ?>"><?= htmlspecialchars($texto) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <!-- Comisión -->
                    <div>
                        <label class="form-label text-secondary small fw-bold">
                            <i class="fa-solid fa-percent me-1" style="color:#ea580c;"></i>% Comisión sobre ventas
                            <span class="text-muted fw-normal">(aplica a Empleados/Vendedores)</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="porcentaje_comision" id="edit_comision" min="0" max="100" step="0.01"
                                   class="form-control" placeholder="Ej: 5.00">
                            <span class="input-group-text bg-light text-muted">%</span>
                        </div>
                        <div class="form-text text-muted" style="font-size:.75rem;">
                            Se congela en cada factura al momento de la venta.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-light p-4">
                    <button type="button" class="btn btn-light border rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white px-4 border-0" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);">Actualizar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-editar').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('edit_id_usuario').value = id;
                document.getElementById('edit_rol').value = this.getAttribute('data-rol');
                document.getElementById('edit_usuario').value = this.getAttribute('data-name');
                document.getElementById('edit_email').value = this.getAttribute('data-email');
                document.getElementById('edit_comision').value = this.getAttribute('data-comision') || '0';

                let estadoVal = this.getAttribute('data-estado');
                let selectEstado = document.getElementById('edit_estado');
                if (estadoVal !== null) {
                    estadoVal = estadoVal.toString().trim().toLowerCase();
                    if (estadoVal === '1' || estadoVal === 'activo' || estadoVal === 'true' || estadoVal === 'activa') {
                        selectEstado.value = 'activo';
                    } else if (estadoVal === '0' || estadoVal === 'inactivo' || estadoVal === 'false' || estadoVal === 'inactiva') {
                        selectEstado.value = 'inactivo';
                    } else {
                        selectEstado.value = estadoVal;
                    }
                }
            });
        });
    });

    // Validación Bootstrap
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>

<!-- JQuery & DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<style>
    /* Estilos extra para embellecer la tabla de DataTables */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%) !important;
        color: white !important;
        border: 1px solid #ea580c !important;
        border-radius: 8px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px;
    }
</style>

<script>
    $(document).ready(function() {
        $('#usuariosTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            responsive: true,
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: [5] } // Solo la columna 5 contiene los botones ahora
            ]
        });

        // Trigger dynamic delete modal via SweetAlert2
        $(document).on('click', '.deletebtn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Confirmar Acción',
                html: '¿Estás seguro de que deseas eliminar este usuario?<br><strong style="color: #ef4444;">Esta acción no se puede deshacer.</strong>',
                icon: 'warning',
                iconColor: '#ef4444',
                showCancelButton: true,
                background: 'transparent',
                color: '#fff',
                confirmButtonText: 'Sí, Eliminar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'glass-alert',
                    title: 'alert-title',
                    confirmButton: 'btn btn-danger px-4 mx-2 rounded-3 border-0 shadow-sm',
                    cancelButton: 'btn btn-light px-4 mx-2 rounded-3 border-0 text-dark shadow-sm',
                    backdrop: 'swal-blur-backdrop'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '../../controllers/DeletedUsuarioController.php';
                    
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'id_usuario';
                    input.value = id;
                    
                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
</script>

<?php 
// Incluir el footer
require_once '../layouts/footer.php'; 
?>