<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}
// Verificar si hay sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../views/auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FactuWeb PRO - Panel de Administración</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6.5.0 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../public/css/style.css">
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="../../img/favicon.svg">
</head>
<body>
    <div class="app-container d-flex">
        
        <!-- Sidebar Admin -->
        <?php include __DIR__ . '/sidebaradmin.php'; ?>

        <!-- Main Wrapper -->
        <div class="app-main flex-grow-1 d-flex flex-column overflow-hidden">
            <!-- Topbar -->
            <header class="app-topbar d-flex justify-content-between align-items-center px-3 bg-white border-bottom shadow-sm z-1" style="height: 80px; min-height: 80px;">
                <button class="btn btn-light d-md-none" id="toggleSidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                
                <!-- Buscador -->
                <div class="d-none d-md-flex align-items-center ms-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" class="form-control bg-light border-0 shadow-none" placeholder="Buscar facturas o clientes..." style="width: 250px;">
                    </div>
                </div>

                <!-- Perfil y Notificaciones -->
                <div class="ms-auto d-flex align-items-center gap-3">
                    <a href="#" class="text-secondary fs-5 position-relative me-2">
                        <i class="fa-solid fa-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                            <span class="visually-hidden">Nuevas alertas</span>
                        </span>
                    </a>
                    
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none text-dark dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 35px; height: 35px; font-weight: bold;">
                                <?php echo strtoupper(substr($_SESSION['usuario']['usuario'] ?? 'A', 0, 1)); ?>
                            </div>
                            <span class="d-none d-md-inline fw-semibold"><?php echo $_SESSION['usuario']['usuario'] ?? 'Administrador'; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li><a class="dropdown-item py-2" href="#"><i class="fa-solid fa-user me-2 text-secondary"></i> Mi Perfil</a></li>
                            <li><a class="dropdown-item py-2" href="#"><i class="fa-solid fa-gear me-2 text-secondary"></i> Configuración</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger fw-bold" href="../../controllers/auth/AuthController.php?accion=logout"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Main Content Area (Scrollable) -->
            <main class="app-content p-4 bg-light-section flex-grow-1 overflow-auto">
