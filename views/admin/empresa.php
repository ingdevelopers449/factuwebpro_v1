<?php 
// Vista de Empresa
require_once '../../controllers/EmpresaController.php';

$controller = new EmpresaController();
extract($controller->index());

require_once '../layouts/header.php'; 
?>

<div class="container-fluid py-2">
    <div class="row g-4 justify-content-center">
        <div class="col-12 col-xl-10">
            <!-- Main Card -->
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                <div class="mb-4">
                    <h2 class="h3 fw-bold text-dark mb-1" style="font-family: var(--font-heading);">
                        <span style="color: #f59e0b;">Empresa</span>
                    </h2>
                    <p class="text-muted small mb-0">Configuración de datos legales y de identidad del negocio.</p>
                </div>

                <?php if (!$empresaRegistrada): ?>
                    <!-- Estado: Empresa NO registrada (Mostrar Formulario Vacío) -->
                    <div class="alert alert-warning border-0 rounded-3 d-flex align-items-center mb-4" style="background-color: #fffbeb; color: #b45309;">
                        <i class="fa-solid fa-circle-info me-3 fs-4"></i>
                        <div>
                            <strong>Aviso importante:</strong> Aún no has configurado los datos de tu empresa. Por favor, completa el siguiente formulario para poder emitir documentos.
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Estado: Empresa SI registrada (Modo Edición) -->
                    <div class="alert alert-success border-0 rounded-3 d-flex align-items-center mb-4" style="background-color: #ecfdf5; color: #047857;">
                        <i class="fa-solid fa-circle-check me-3 fs-4"></i>
                        <div>
                            <strong>Empresa Configurada:</strong> Los datos actuales de tu empresa se muestran a continuación.
                        </div>
                    </div>
                <?php endif; ?>

                <form action="../../controllers/EmpresaController.php?action=guardar" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    
                    <h5 class="fw-bold mb-4 mt-2 border-bottom pb-2" style="font-family: var(--font-heading); color: #334155;">Información de la empresa</h5>
                    
                    <div class="row g-4">
                        <!-- Logo Upload -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small">Logo de la Empresa</label>
                            <div class="d-flex align-items-center gap-4 p-3 rounded-3 border" style="background-color: #f8fafc; border-style: dashed !important; border-color: #cbd5e1 !important;">
                                <div class="bg-white rounded-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; border: 1px solid #e2e8f0;">
                                    <?php if (!empty($empresaDatos['logo'])): ?>
                                        <img src="../../public/uploads/<?= htmlspecialchars($empresaDatos['logo'] ?? '') ?>" alt="Logo" class="img-fluid rounded-3" style="max-height: 90px;">
                                    <?php else: ?>
                                        <i class="fa-solid fa-image text-muted fs-1"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <input class="form-control form-control-sm mb-2" type="file" name="logo" id="logo" accept="image/png, image/jpeg">
                                    <span class="text-muted" style="font-size: 0.75rem;">Recomendado: Imagen PNG transparente, tamaño máximo 2MB.</span>
                                </div>
                            </div>
                        </div>

                        <!-- NIT -->
                        <div class="col-md-4">
                            <label for="nit" class="form-label fw-semibold text-secondary small">NIT *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-id-card"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="nit" name="nit" value="<?= htmlspecialchars($empresaDatos['nit'] ?? '') ?>" placeholder="Ej: 900.123.456-7" required>
                                <div class="invalid-feedback">El NIT es obligatorio.</div>
                            </div>
                        </div>

                        <!-- Razón Social -->
                        <div class="col-md-8">
                            <label for="razon_social" class="form-label fw-semibold text-secondary small">Razón social *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-building"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="razon_social" name="razon_social" value="<?= htmlspecialchars($empresaDatos['razon_social'] ?? '') ?>" placeholder="Nombre legal de la empresa" required>
                                <div class="invalid-feedback">La Razón social es obligatoria.</div>
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="col-12">
                            <label for="direccion" class="form-label fw-semibold text-secondary small">Dirección física</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-map-location-dot"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="direccion" name="direccion" value="<?= htmlspecialchars($empresaDatos['direccion'] ?? '') ?>" placeholder="Ej: Calle 123 # 45-67, Ciudad">
                            </div>
                        </div>

                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <label for="telefono" class="form-label fw-semibold text-secondary small">Teléfono</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-phone"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="telefono" name="telefono" value="<?= htmlspecialchars($empresaDatos['telefono'] ?? '') ?>" placeholder="Ej: 300 123 4567">
                            </div>
                        </div>

                        <!-- Correo Electrónico -->
                        <div class="col-md-6">
                            <label for="correo" class="form-label fw-semibold text-secondary small">Correo electrónico oficial</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" class="form-control border-start-0 ps-0" id="correo" name="correo" value="<?= htmlspecialchars($empresaDatos['correo'] ?? '') ?>" placeholder="contacto@miempresa.com">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-5 border-top pt-4">
                        <button type="submit" class="btn btn-orange px-5 py-2 text-white">
                            <i class="fa-solid fa-floppy-disk me-2"></i> 
                            <?= $empresaRegistrada ? 'Actualizar Empresa' : 'Guardar Empresa' ?>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script>
    // Script simple para validación de Bootstrap en el frontend
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

<?php require_once '../layouts/footer.php'; ?>
