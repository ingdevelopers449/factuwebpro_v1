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
