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
</body>
</html>
