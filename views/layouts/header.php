<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}
// Verificar si hay sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../views/auth/login.php');
    exit;
}

// Control de Inactividad (10 minutos = 600 segundos)
$timeout_duration = 600;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['alert'] = [
        'icon' => 'warning',
        'title' => 'Sesión expirada',
        'text' => 'Su sesión se cerró automáticamente por seguridad tras 10 minutos de inactividad.'
    ];
    header('Location: ../../views/auth/login.php');
    exit;
}
$_SESSION['last_activity'] = time();

// --- Control de Acceso por Roles (RBAC) ---
$rol = $_SESSION['usuario']['id_rol'] ?? 0;
$script_name = basename($_SERVER['SCRIPT_NAME']);

// Módulos que el vendedor (rol 2) TIENE permitido visitar (compartidos con admin)
$modulos_permitidos_vendedor = [
    'facturas.php', 
    'imprimir_factura.php', 
    'clientes.php', 
    'mis_ventas.php'
];

if ($rol == 2) {
    if (!in_array($script_name, $modulos_permitidos_vendedor)) {
        $_SESSION['alert'] = [
            'icon' => 'error',
            'title' => 'Acceso Denegado',
            'text' => 'No tienes los permisos necesarios para ver este módulo.'
        ];
        header('Location: ../../views/seller/mis_ventas.php');
        exit;
    }
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
        
        <!-- Sidebar -->
        <?php 
        if (isset($_SESSION['usuario']['id_rol']) && $_SESSION['usuario']['id_rol'] == '2') {
            include __DIR__ . '/sidebarseller.php'; 
        } else {
            include __DIR__ . '/sidebaradmin.php'; 
        }
        ?>

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
