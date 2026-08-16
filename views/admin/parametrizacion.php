<?php 
// Vista de Parametrización DIAN
require_once '../../controllers/ParametrizacionController.php';

$controller = new ParametrizacionController();
extract($controller->index());

require_once '../layouts/header.php'; 
?>

<div class="container-fluid py-2">
    <div class="row g-4 justify-content-center">
        <div class="col-12 col-xl-10">
            
            <?php if (!$empresaRegistrada): ?>
                <div class="alert alert-danger border-0 rounded-3 shadow-sm d-flex align-items-center mb-4">
                    <i class="fa-solid fa-triangle-exclamation me-3 fs-4"></i>
                    <div>
                        <strong>Atención:</strong> Primero debes configurar tu <a href="empresa.php" class="alert-link">Empresa</a> antes de parametrizar la resolución DIAN.
                    </div>
                </div>
            <?php endif; ?>

            <!-- Main Card -->
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                <div class="mb-4">
                    <h2 class="h3 fw-bold text-dark mb-1 text-uppercase" style="font-family: var(--font-heading);">
                        <span style="color: #f59e0b;">Parametrización</span> DIAN
                    </h2>
                    <p class="text-muted small mb-0">Configuración de numeración para la facturación electrónica.</p>
                </div>

                <form action="../../controllers/ParametrizacionController.php?action=guardar" method="POST" class="needs-validation" novalidate>
                    
                    <div class="row g-4 mt-1">
                        <!-- Empresa -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small">Empresa</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-building"></i></span>
                                <input type="text" class="form-control bg-light border-start-0 ps-0 text-muted" value="<?= htmlspecialchars($nombreEmpresa) ?>" readonly>
                            </div>
                        </div>

                        <!-- Número de resolución -->
                        <div class="col-md-7">
                            <label for="numero_resolucion" class="form-label fw-semibold text-secondary small">Número de resolución *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-hashtag"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="numero_resolucion" name="numero_resolucion" value="<?= htmlspecialchars($configDatos['numero_resolucion'] ?? '') ?>" required <?= !$empresaRegistrada ? 'disabled' : '' ?>>
                                <div class="invalid-feedback">El número de resolución es obligatorio.</div>
                            </div>
                        </div>

                        <!-- Fecha de vigencia -->
                        <div class="col-md-5">
                            <label for="fecha_vigencia" class="form-label fw-semibold text-secondary small">Fecha de vigencia *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-regular fa-calendar"></i></span>
                                <input type="date" class="form-control border-start-0 ps-0" id="fecha_vigencia" name="fecha_vigencia" value="<?= htmlspecialchars($configDatos['fecha_vigencia'] ?? '') ?>" required <?= !$empresaRegistrada ? 'disabled' : '' ?>>
                                <div class="invalid-feedback">Indique la fecha de vigencia.</div>
                            </div>
                        </div>

                        <!-- Prefijo -->
                        <div class="col-md-4">
                            <label for="prefijo" class="form-label fw-semibold text-secondary small">Prefijo *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-font"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0 text-uppercase" id="prefijo" name="prefijo" value="<?= htmlspecialchars($configDatos['prefijo'] ?? '') ?>" placeholder="Ej: FV" required <?= !$empresaRegistrada ? 'disabled' : '' ?>>
                                <div class="invalid-feedback">El prefijo es obligatorio.</div>
                            </div>
                        </div>

                        <!-- Rangos -->
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-secondary small">Rango autorizado</label>
                            <div class="d-flex gap-3">
                                <div class="input-group flex-grow-1">
                                    <span class="input-group-text bg-light text-muted small">Inicial *</span>
                                    <input type="number" class="form-control" id="rango_inicial" name="rango_inicial" value="<?= htmlspecialchars($configDatos['rango_inicial'] ?? '') ?>" placeholder="Ej: 1" required <?= !$empresaRegistrada ? 'disabled' : '' ?>>
                                </div>
                                <div class="input-group flex-grow-1">
                                    <span class="input-group-text bg-light text-muted small">Final *</span>
                                    <input type="number" class="form-control" id="rango_final" name="rango_final" value="<?= htmlspecialchars($configDatos['rango_final'] ?? '') ?>" placeholder="Ej: 5000" required <?= !$empresaRegistrada ? 'disabled' : '' ?>>
                                </div>
                            </div>
                        </div>

                        <!-- Consecutivo actual -->
                        <div class="col-md-6">
                            <label for="contador_actual" class="form-label fw-semibold text-secondary small">Consecutivo actual</label>
                            <div class="input-group mb-1">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-arrow-up-1-9"></i></span>
                                <input type="number" class="form-control border-start-0 ps-0" id="contador_actual" name="contador_actual" value="<?= htmlspecialchars($configDatos['contador_actual'] ?? '1') ?>" required <?= !$empresaRegistrada ? 'disabled' : '' ?>>
                            </div>
                            <span class="text-muted" style="font-size: 0.75rem;"><i class="fa-solid fa-circle-info text-primary me-1"></i> Indica el consecutivo utilizado actualmente por el sistema.</span>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">Estado</label>
                            <div class="d-flex gap-4 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="estado" id="estadoActiva" value="Activa" <?= (empty($configDatos['estado']) || strtolower($configDatos['estado']) === 'activa') ? 'checked' : '' ?> <?= !$empresaRegistrada ? 'disabled' : '' ?>>
                                    <label class="form-check-label" for="estadoActiva">Activa</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="estado" id="estadoInactiva" value="Inactiva" <?= (strtolower($configDatos['estado'] ?? '') === 'inactiva') ? 'checked' : '' ?> <?= !$empresaRegistrada ? 'disabled' : '' ?>>
                                    <label class="form-check-label" for="estadoInactiva">Inactiva</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-5 border-top pt-4">
                        <button type="submit" class="btn btn-orange px-5 py-2 text-white" <?= !$empresaRegistrada ? 'disabled' : '' ?>>
                            <i class="fa-solid fa-gear me-2"></i> 
                            <?= $configRegistrada ? 'Actualizar Configuración' : 'Guardar Configuración' ?>
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
