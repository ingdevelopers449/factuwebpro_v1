# Prompt para migrar el Landing Page a Laravel

Copia el siguiente texto y pégalo en el chat de la IA que te ayudará con el proyecto de Laravel.

***

**Actúa como un experto en Laravel y desarrollo Frontend (Blade + Bootstrap).**

Actualmente estoy migrando un proyecto a Laravel y necesito crear la vista `welcome.blade.php`. Ya tengo el diseño exacto que quiero usar, construido con Bootstrap 5.3 y CSS personalizado.

**Tus tareas son:**
1. Crear la vista `resources/views/welcome.blade.php` utilizando la estructura HTML que te proporcionaré.
2. **NO incluyas botones de Login o Registro** en la estructura, ya que Laravel (Breeze/Jetstream) ya los incluye por defecto en su propia cabecera o yo los manejaré con sus rutas nativas. He omitido los botones originales en el código HTML de abajo.
3. Crear un archivo CSS en `public/css/style.css` (o incluirlo donde sea apropiado en el layout) con los estilos personalizados que te daré.
4. Asegurarte de que el HTML incluya la CDN de Bootstrap 5.3 para que el diseño funcione de inmediato sin configuraciones adicionales de Vite.

Aquí tienes el código HTML para `welcome.blade.php`:

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FactuWeb PRO - Software de Facturación y POS</title>
    
    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6.5.0 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <!-- Top Banner -->
    <div class="top-banner">
        🚀 YA DISPONIBLE: FactuWeb PRO V1.0
    </div>

    <!-- 1. Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top shadow-lg z-3">
        <div class="container">
            <a class="navbar-brand" href="#">
                <span class="text-white fw-bold fs-4">FactuWeb PRO</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fa-solid fa-bars text-white fs-2"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link active" href="#inicio">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#caracteristicas">Características</a></li>
                    <li class="nav-item"><a class="nav-link" href="#funcionamiento">Cómo Funciona</a></li>
                    <li class="nav-item"><a class="nav-link" href="#beneficios">Beneficios</a></li>
                </ul>
                <div class="d-flex align-items-center mt-3 mt-lg-0 gap-3">
                    <!-- Los botones de login y auth de Laravel pueden ir aquí -->
                    @if (Route::has('login'))
                        <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-white px-4">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-white px-4">Ingresar</a>
                            @endauth
                        </div>
                    @endif
                    <div class="social-icons d-none d-lg-block">
                        <a href="#"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- 2. Hero principal -->
    <div class="hero-wrapper" id="inicio">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0 pe-lg-5">
                    <h1 class="hero-title">
                        SOFTWARE DE FACTURACIÓN ELECTRÓNICA Y POS
                    </h1>
                    <p class="hero-text">
                        Somos el sistema más rápido, confiable y con el mejor soporte para pequeños negocios. Centraliza ventas, inventario y facturación DIAN en un solo lugar.
                    </p>
                    <div class="hero-cta-wrapper mt-4">
                        <div class="mt-3 text-white-50 small d-flex align-items-center gap-3">
                            <span><i class="fa-solid fa-circle-check text-success me-1"></i> Configuración rápida</span>
                            <span><i class="fa-solid fa-circle-check text-success me-1"></i> Soporte VIP</span>
                        </div>
                    </div>
                    
                    <div class="trust-badge shadow">
                        4.9 <i class="fa-solid fa-star"></i> en <strong>&nbsp;Trustpilot</strong>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <!-- Dashboard Mockup con efecto 3D Isométrico -->
                    <div class="hero-mockup-wrapper">
                        <div class="d-flex justify-content-between align-items-center border-bottom border-secondary pb-3 mb-4">
                            <h5 class="m-0 fw-bold text-white">
                                <i class="fa-solid fa-border-all me-2 text-primary"></i>Panel de Control
                            </h5>
                            <div class="d-flex gap-2">
                                <span class="badge bg-success rounded-pill px-3 py-2">En Línea</span>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-label">Ventas de Hoy</div>
                                    <h4 class="stat-value">$1.250.000</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-label">Facturas DIAN</div>
                                    <h4 class="stat-value text-success">24 Emitidas</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card mb-0">
                                    <div class="stat-label">Clientes</div>
                                    <h4 class="stat-value">18 Nuevos</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card mb-0 border-warning">
                                    <div class="stat-label text-warning">Alertas Stock</div>
                                    <h4 class="stat-value text-warning">3 Productos</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Sección de características -->
    <section id="caracteristicas" class="section-padding bg-light-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">TODO LO NECESARIO PARA TU<br>NEGOCIO</h2>
                <p class="section-subtitle">Compatible con tiendas, minimarkets, ferreterías, servicios y más.</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon text-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h5 class="feature-title">Facturación Electrónica</h5>
                        <p class="feature-text">Genera facturas electrónicas de venta cumpliendo con todos los requisitos de la DIAN al instante y sin demoras.</p>
                        <a href="#" class="btn btn-blue btn-sm px-4">Ver Detalles &rarr;</a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon text-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <h5 class="feature-title">Control de Inventario</h5>
                        <p class="feature-text">Gestiona tu catálogo de productos, valida el stock en tiempo real durante la venta y recibe alertas de agotamiento.</p>
                        <a href="#" class="btn btn-blue btn-sm px-4">Ver Detalles &rarr;</a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon text-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h5 class="feature-title">Gestión de Clientes</h5>
                        <p class="feature-text">Automatiza la recepción y guarda el historial de compras de tus clientes para ofrecer un mejor servicio.</p>
                        <a href="#" class="btn btn-blue btn-sm px-4">Ver Detalles &rarr;</a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon text-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h5 class="feature-title">Reportes y Finanzas</h5>
                        <p class="feature-text">Visualiza las utilidades, comisiones y el rendimiento general de tu empresa con gráficos interactivos.</p>
                        <a href="#" class="btn btn-blue btn-sm px-4">Ver Detalles &rarr;</a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon text-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <h5 class="feature-title">Seguridad y Roles</h5>
                        <p class="feature-text">Crea usuarios para tus cajeros y vendedores, limitando sus permisos solo a lo estrictamente necesario.</p>
                        <a href="#" class="btn btn-blue btn-sm px-4">Ver Detalles &rarr;</a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon text-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h5 class="feature-title">Rapidez en POS</h5>
                        <p class="feature-text">Interfaz de punto de venta súper rápida. Diseñada para que no haya filas en tu mostrador.</p>
                        <a href="#" class="btn btn-blue btn-sm px-4">Ver Detalles &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Botón Flotante WhatsApp -->
    <a href="#" class="whatsapp-float" target="_blank">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <!-- Footer -->
    <footer>
        <div class="container text-center text-md-start">
            <div class="row align-items-center">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="text-white mb-3">FactuWeb PRO</h5>
                    <p class="small mb-0 opacity-75">El sistema de facturación y POS líder para pequeños negocios en crecimiento.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0 text-center">
                    <a href="#" class="text-white text-decoration-none mx-2 hover-opacity-100">Políticas de Privacidad</a>
                    <a href="#" class="text-white text-decoration-none mx-2 hover-opacity-100">Términos de Servicio</a>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="social-icons mb-2">
                        <a href="#" class="text-white text-decoration-none me-3 fs-5"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#" class="text-white text-decoration-none me-3 fs-5"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="text-white text-decoration-none fs-5"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                    <span class="opacity-50 small">&copy; 2026 FactuWeb PRO. Todos los derechos reservados.</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

Y aquí tienes el código CSS que debes poner en el archivo `public/css/style.css`:

```css
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700&display=swap');

:root {
    --font-primary: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    --font-heading: 'Outfit', system-ui, -apple-system, sans-serif;
    --fw-regular: 400; --fw-medium: 500; --fw-semibold: 600; --fw-bold: 700;
}

body {
    font-family: var(--font-primary);
    color: #1e293b;
    background-color: #f8fafc;
    overflow-x: hidden;
}

h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
    font-family: var(--font-heading);
    font-weight: var(--fw-bold);
    color: #0f172a;
}

/* Banner Superior */
.top-banner {
    background-color: #3b82f6;
    color: #ffffff;
    text-align: center;
    padding: 0.5rem;
    font-size: 0.85rem;
    font-weight: 600;
}

/* Navbar */
.navbar-custom {
    background: rgba(18, 16, 47, 0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    padding: 1.5rem 0;
}
.navbar-custom .nav-link {
    color: #e2e8f0;
    font-weight: 600;
    font-size: 0.95rem;
    margin: 0 0.5rem;
    transition: color 0.3s;
}
.navbar-custom .nav-link:hover, .navbar-custom .nav-link.active {
    color: #ffffff;
}
.navbar-custom .social-icons a {
    color: #ffffff;
    font-size: 1.25rem;
    margin-left: 1rem;
    transition: opacity 0.3s;
}
.navbar-custom .social-icons a:hover { opacity: 0.7; }

/* Botones */
.btn {
    font-family: var(--font-heading);
    font-weight: var(--fw-semibold);
    transition: transform 0.2s, box-shadow 0.2s;
}
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
}
.btn-white { background-color: #ffffff; color: #12102f; border: none; padding: 0.75rem 1.5rem; border-radius: 50px; }
.btn-white:hover { background-color: #f8fafc; color: #12102f; }
.btn-blue { background-color: #4f46e5; color: #ffffff; border: none; padding: 0.75rem 1.5rem; border-radius: 50px; }
.btn-blue:hover { background-color: #4338ca; color: #ffffff; }

/* Hero Section */
.hero-wrapper {
    background: linear-gradient(135deg, #12102f 0%, #1e1b4b 100%);
    clip-path: polygon(0 0, 100% 0, 100% 90%, 0 100%);
    padding: 4rem 0 12rem 0;
    position: relative;
}
.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
    margin-bottom: 1.5rem;
    text-transform: uppercase;
}
.hero-text {
    font-size: 1.1rem;
    color: #cbd5e1;
    margin-bottom: 2.5rem;
    font-weight: 400;
    line-height: 1.6;
}

.trust-badge {
    display: inline-flex;
    align-items: center;
    background: #ffffff;
    color: #0f172a;
    padding: 0.5rem 1.25rem;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.9rem;
    margin-top: 3rem;
}
.trust-badge i { color: #00b67a; margin: 0 0.5rem; }

/* Dashboard Mockup 3D Effect */
.hero-mockup-wrapper {
    position: relative;
    border-radius: 1rem;
    background: #1e293b;
    border: 1px solid #334155;
    padding: 1.5rem;
    transform: perspective(1000px) rotateY(-12deg) rotateX(5deg) scale(0.95);
    box-shadow: 25px 25px 50px rgba(0, 0, 0, 0.5), -1px -1px 0 rgba(255,255,255,0.1);
    transition: transform 0.5s ease;
}
.hero-mockup-wrapper:hover {
    transform: perspective(1000px) rotateY(-5deg) rotateX(2deg) scale(1);
}
.stat-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 0.75rem;
    padding: 1.25rem;
    margin-bottom: 1rem;
}
.stat-label {
    font-size: 0.8rem;
    color: #94a3b8;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0.25rem 0 0 0;
}

/* Secciones Generales */
.section-padding { padding: 5rem 0; }
.bg-light-section { background-color: #f8fafc; }
.section-header { text-align: center; margin-bottom: 4rem; }
.section-title { font-weight: 900; font-size: 2.5rem; color: #12102f; text-transform: uppercase; line-height: 1.2; }
.section-subtitle { color: #64748b; font-size: 1.1rem; margin-top: 1rem; }

/* Feature Cards */
.feature-card {
    background: #ffffff;
    border-radius: 1.5rem;
    padding: 3rem 2rem;
    height: 100%;
    text-align: center;
    border: none;
    box-shadow: 0 20px 40px rgba(79, 70, 229, 0.06);
    transition: transform 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.feature-card:hover { transform: translateY(-10px); }
.feature-icon { width: 80px; height: 80px; margin-bottom: 1.5rem; }
.feature-title { font-weight: 800; font-size: 1.15rem; margin-bottom: 1rem; color: #12102f; text-transform: uppercase; }
.feature-text { color: #475569; font-size: 0.95rem; line-height: 1.6; margin-bottom: 2rem; flex-grow: 1; }

/* Botón flotante WhatsApp */
.whatsapp-float {
    position: fixed; width: 60px; height: 60px; bottom: 40px; right: 40px;
    background-color: #25d366; color: #FFF; border-radius: 50px;
    text-align: center; font-size: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    z-index: 1000; display: flex; align-items: center; justify-content: center;
    transition: transform 0.3s;
}
.whatsapp-float:hover { color: #FFF; transform: scale(1.1); }

/* Footer */
footer { background-color: #12102f; color: #94a3b8; padding: 4rem 0 2rem 0; }
```
