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
            <?php
                $usuario_nombre = $_SESSION['usuario']['nombre'] ?? 'Usuario';
                $usuario_inicial = strtoupper(substr($usuario_nombre, 0, 1));
                $id_rol_actual = $_SESSION['usuario']['id_rol'] ?? 0;
                $nombre_rol  = ($id_rol_actual == '1') ? 'Administrador' : 'Empleado';
                $color_rol   = ($id_rol_actual == '1') ? '#ea580c' : '#10b981';

                // Mapa de breadcrumbs por página
                $breadcrumb_map = [
                    'dashboard.php'         => [['icono'=>'fa-chart-pie',              'label'=>'Dashboard']],
                    'facturas.php'          => [['icono'=>'fa-file-invoice-dollar',    'label'=>'Ventas'], ['icono'=>'fa-cash-register', 'label'=>'Facturación POS']],
                    'clientes.php'          => [['icono'=>'fa-file-invoice-dollar',    'label'=>'Ventas'], ['icono'=>'fa-users',         'label'=>'Clientes']],
                    'imprimir_factura.php'  => [['icono'=>'fa-file-invoice-dollar',    'label'=>'Ventas'], ['icono'=>'fa-print',         'label'=>'Imprimir Factura']],
                    'categorias.php'        => [['icono'=>'fa-box-open',               'label'=>'Catálogo'], ['icono'=>'fa-tags',        'label'=>'Categorías']],
                    'productos.php'         => [['icono'=>'fa-box-open',               'label'=>'Catálogo'], ['icono'=>'fa-box-open',    'label'=>'Productos']],
                    'gempleados.php'        => [['icono'=>'fa-user-shield',            'label'=>'Administración'], ['icono'=>'fa-user-shield','label'=>'Empleados']],
                    'empresa.php'           => [['icono'=>'fa-building',               'label'=>'Administración'], ['icono'=>'fa-building',   'label'=>'Empresa']],
                    'parametrizacion.php'   => [['icono'=>'fa-building-columns',       'label'=>'Administración'], ['icono'=>'fa-building-columns','label'=>'Parametrización DIAN']],
                    'rentabilidad.php'      => [['icono'=>'fa-chart-line',             'label'=>'Reportes'], ['icono'=>'fa-chart-line',  'label'=>'Ventas y Rentabilidad']],
                    'mis_ventas.php'        => [['icono'=>'fa-list-check',             'label'=>'Mis Ventas']],
                ];
                $current_page_key = basename($_SERVER['SCRIPT_NAME']);
                $breadcrumbs = $breadcrumb_map[$current_page_key] ?? [['icono'=>'fa-house', 'label'=>'Inicio']];
            ?>
            <header class="app-topbar d-flex justify-content-between align-items-center px-4 bg-white border-bottom shadow-sm z-1" style="height: 80px; min-height: 80px;">
                <!-- Botón toggle sidebar móvil -->
                <button class="btn btn-light d-md-none me-2" id="toggleSidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <!-- Breadcrumb dinámico -->
                <nav aria-label="breadcrumb" class="d-none d-md-flex align-items-center">
                    <ol class="breadcrumb mb-0 align-items-center">
                        <!-- Inicio siempre primero -->
                        <li class="breadcrumb-item">
                            <a href="<?php echo ($id_rol_actual == '2') ? '../../views/seller/mis_ventas.php' : '../../views/admin/dashboard.php'; ?>"
                               class="text-decoration-none d-flex align-items-center gap-1"
                               style="color:#12102f;">
                                <i class="fa-solid fa-house" style="font-size:.8rem;"></i>
                                <span style="font-size:.83rem;">FactuWeb PRO</span>
                            </a>
                        </li>
                        <?php foreach ($breadcrumbs as $i => $crumb): ?>
                            <?php $is_last = ($i === count($breadcrumbs) - 1); ?>
                            <li class="breadcrumb-item <?php echo $is_last ? 'active' : ''; ?>"
                                <?php if ($is_last) echo 'aria-current="page"'; ?>>
                                <?php if ($is_last): ?>
                                    <span class="d-flex align-items-center gap-1 fw-semibold" style="color:#ea580c; font-size:.85rem;">
                                        <i class="fa-solid <?php echo htmlspecialchars($crumb['icono']); ?>" style="font-size:.78rem;"></i>
                                        <?php echo htmlspecialchars($crumb['label']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted d-flex align-items-center gap-1" style="font-size:.83rem;">
                                        <i class="fa-solid <?php echo htmlspecialchars($crumb['icono']); ?>" style="font-size:.75rem;"></i>
                                        <?php echo htmlspecialchars($crumb['label']); ?>
                                    </span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </nav>

                <!-- Perfil de usuario (estático) -->
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm text-white fw-bold flex-shrink-0"
                         style="width:40px;height:40px;background:linear-gradient(135deg,#12102f,#ea580c);font-size:1rem;">
                        <?php echo $usuario_inicial; ?>
                    </div>
                    <div class="d-none d-md-block lh-sm">
                        <div class="fw-semibold text-dark" style="font-size:.9rem;"><?php echo htmlspecialchars($usuario_nombre); ?></div>
                        <span class="badge rounded-pill px-2" style="background-color:<?php echo $color_rol; ?>; font-size:.68rem;"><?php echo $nombre_rol; ?></span>
                    </div>
                </div>
            </header>

            <!-- Main Content Area (Scrollable) -->
            <main class="app-content p-4 bg-light-section flex-grow-1 overflow-auto">
