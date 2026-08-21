            </main>
            <!-- Fin Main Content Area -->
            
            <footer class="app-footer bg-white border-top p-3 text-center text-muted small">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <span class="mb-2 mb-md-0">&copy; <?php echo date('Y'); ?> FactuWeb PRO S.A.S. Todos los derechos reservados.</span>
                    <span>Versión 1.0 <i class="fa-solid fa-rocket text-primary ms-1"></i></span>
                </div>
            </footer>
        </div> <!-- Fin app-main -->
    </div> <!-- Fin app-container -->

    <!-- ============================================== -->
    <!-- MÓDULO DE AYUDA Y SOPORTE (HU-011 / RF-11)   -->
    <!-- ============================================== -->
    <button class="btn btn-help-float shadow-lg" data-bs-toggle="modal" data-bs-target="#helpCenterModal" title="Centro de Ayuda">
        <i class="fa-solid fa-headset"></i>
    </button>

    <!-- Modal Centro de Ayuda -->
    <div class="modal fade" id="helpCenterModal" tabindex="-1" aria-labelledby="helpCenterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-gradient-primary text-white rounded-top-4 border-0 p-4">
                    <h5 class="modal-title fw-bold text-white" id="helpCenterModalLabel">
                        <i class="fa-solid fa-life-ring me-2 text-warning"></i> Centro de Ayuda y Soporte
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <ul class="nav nav-pills mb-4 gap-2" id="help-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill fw-bold px-4" id="contact-tab" data-bs-toggle="pill" data-bs-target="#contact-panel" type="button" role="tab">
                                <i class="fa-solid fa-address-book me-1"></i> Contacto y Soporte
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill fw-bold px-4" id="faq-tab" data-bs-toggle="pill" data-bs-target="#faq-panel" type="button" role="tab">
                                <i class="fa-solid fa-circle-question me-1"></i> Preguntas Frecuentes
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="help-tabs-content">
                        <!-- Panel de Contacto -->
                        <div class="tab-pane fade show active" id="contact-panel" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 bg-light rounded-4 p-4 text-center shadow-sm">
                                        <div class="display-5 text-success mb-3"><i class="fa-brands fa-whatsapp"></i></div>
                                        <h5 class="fw-bold text-dark">Soporte Técnico (Fallas)</h5>
                                        <p class="text-muted small">Reporta caídas del sistema, errores críticos de facturación o problemas de conexión de inmediato.</p>
                                        <a href="https://wa.me/573000000000" target="_blank" class="btn btn-success rounded-pill mt-auto fw-bold px-4">Chat WhatsApp</a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 bg-light rounded-4 p-4 text-center shadow-sm">
                                        <div class="display-5 text-primary mb-3"><i class="fa-solid fa-envelope"></i></div>
                                        <h5 class="fw-bold text-dark">Contacto Administrativo</h5>
                                        <p class="text-muted small">Solicitudes de creación de nuevos usuarios, anulación de facturas (Nota Crédito) o modificación de datos.</p>
                                        <a href="mailto:admin@factuwebpro.com" class="btn btn-primary rounded-pill mt-auto fw-bold px-4">Enviar Email</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Panel de FAQs -->
                        <div class="tab-pane fade" id="faq-panel" role="tabpanel">
                            <div class="accordion accordion-flush bg-light rounded-4 p-3 shadow-sm" id="accordionFAQ">
                                <div class="accordion-item bg-transparent border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                            <i class="fa-solid fa-wifi text-muted me-2"></i> ¿Qué hacer si no hay conexión a internet?
                                        </button>
                                    </h2>
                                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                        <div class="accordion-body text-muted small">
                                            El módulo POS almacena temporalmente tu venta en curso de manera local (Borrador). Si el internet falla, no cierres la pestaña; en cuanto regrese la conexión, tus productos seguirán allí listos para ser facturados y sincronizados con la DIAN.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item bg-transparent border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                            <i class="fa-solid fa-file-invoice text-muted me-2"></i> ¿Cómo corrijo una factura mal elaborada?
                                        </button>
                                    </h2>
                                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                        <div class="accordion-body text-muted small">
                                            Por disposición legal de la DIAN, una factura electrónica emitida no puede ser borrada. Debes utilizar el canal de <strong>Contacto Administrativo</strong> para solicitar la emisión de una <strong>Nota Crédito</strong> que anule el comprobante erróneo.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item bg-transparent">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                            <i class="fa-solid fa-box-open text-muted me-2"></i> No encuentro un producto en el POS
                                        </button>
                                    </h2>
                                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                        <div class="accordion-body text-muted small">
                                            Verifica lo siguiente:
                                            <ul>
                                                <li>Que el producto esté marcado como <strong>Activo</strong>.</li>
                                                <li>Que el <strong>Stock Actual</strong> sea mayor a cero.</li>
                                            </ul>
                                            Si cumple ambas condiciones y no aparece, contacta a Soporte Técnico.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script para Toggle del Sidebar en móviles -->
    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }
    </script>
    <!-- SweetAlert Global (Flash Messages) -->
    <?php if (isset($_SESSION['alert'])): ?>
        <style>
            .glass-alert {
                background: rgba(17, 24, 39, 0.8) !important;
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.08) !important;
                border-radius: 24px !important;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5) !important;
            }
            .swal-blur-backdrop {
                backdrop-filter: blur(5px) !important;
                -webkit-backdrop-filter: blur(5px) !important;
                background: rgba(11, 15, 25, 0.3) !important;
            }
            .alert-title {
                font-family: 'Outfit', sans-serif !important;
                font-weight: 700 !important;
            }
            .btn-premium-primary {
                font-family: 'Outfit', sans-serif !important;
                font-weight: 600 !important;
                letter-spacing: 0.3px !important;
                padding: 12px 32px !important;
                border-radius: 12px !important;
                background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%) !important;
                color: white !important;
                border: none !important;
                box-shadow: 0 4px 14px 0 rgba(245, 158, 11, 0.4) !important;
                transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
                cursor: pointer;
            }
            .btn-premium-primary:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 6px 20px 0 rgba(234, 88, 12, 0.6) !important;
            }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '<?= htmlspecialchars($_SESSION['alert']['icon']) ?>',
                    title: '<?= htmlspecialchars($_SESSION['alert']['title']) ?>',
                    text: '<?= htmlspecialchars($_SESSION['alert']['text']) ?>',
                    background: 'transparent',
                    color: '#fff',
                    customClass: {
                        popup: 'glass-alert',
                        title: 'alert-title',
                        confirmButton: 'btn-premium-primary',
                        backdrop: 'swal-blur-backdrop'
                    },
                    buttonsStyling: false
                });
            });
        </script>
        <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>
</body>
</html>
