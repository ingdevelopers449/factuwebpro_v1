<aside class="app-sidebar text-white shadow-lg" id="sidebar">
    <div class="sidebar-brand d-flex align-items-center justify-content-center border-bottom border-light border-opacity-10" style="height: 80px; min-height: 80px;">
        <a href="../../views/admin/facturas.php" class="text-decoration-none">
            <img src="../../img/logo_nuevo.svg" alt="FactuWeb PRO" height="50">
        </a>
    </div>

    <?php $current_page = $current_page ?? basename($_SERVER['PHP_SELF']); ?>
    <ul class="nav flex-column sidebar-nav mt-3 overflow-auto pb-5">
        
        <li class="nav-item px-3 mt-2 mb-2 text-white-50 small fw-bold text-uppercase" style="letter-spacing: 1px;">Punto de Venta</li>
        <li class="nav-item">
            <a href="../../views/admin/facturas.php" class="nav-link <?php echo ($current_page == 'facturas.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-cash-register"></i> Facturar (POS)
            </a>
        </li>
        <li class="nav-item">
            <a href="../../views/seller/mis_ventas.php" class="nav-link <?php echo ($current_page == 'mis_ventas.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-list-check"></i> Mis Ventas (Historial)
            </a>
        </li>
        <li class="nav-item">
            <a href="../../views/admin/clientes.php" class="nav-link <?php echo ($current_page == 'clientes.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-users"></i> Clientes
            </a>
        </li>

    </ul>
    <div class="p-3 border-top border-light border-opacity-10 mt-auto">
        <div class="d-flex align-items-center gap-2 p-2 rounded-3" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold fs-6 shadow-sm" style="width: 36px; height: 36px; min-width: 36px; background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);">
                <?php echo strtoupper(substr($_SESSION['usuario']['nombre'] ?? 'E', 0, 1)); ?>
            </div>
            <div class="overflow-hidden flex-grow-1">
                <h6 class="text-white mb-0 fw-bold text-truncate" style="font-size: 0.85rem;"><?php echo $_SESSION['usuario']['nombre'] ?? 'Vendedor'; ?></h6>
                <span class="text-white-50 d-block text-truncate" style="font-size: 0.7rem;"><i class="fa-solid fa-circle text-success" style="font-size: 0.5rem; vertical-align: middle; margin-right: 3px;"></i>Rol: Empleado</span>
            </div>
            <a href="../../controllers/auth/AuthController.php?accion=logout" class="btn btn-sm border-0 p-1 rounded-circle d-flex align-items-center justify-content-center text-white-50" title="Cerrar Sesión" style="width: 28px; height: 28px; transition: color 0.2s;" onmouseover="this.classList.replace('text-white-50', 'text-danger')" onmouseout="this.classList.replace('text-danger', 'text-white-50')">
                <i class="fa-solid fa-power-off"></i>
            </a>
        </div>
    </div>
</aside>
