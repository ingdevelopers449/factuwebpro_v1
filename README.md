# FactuWeb PRO 🧾

> Sistema de Facturación Electrónica para pequeñas y medianas empresas colombianas. Proyecto académico desarrollado con PHP nativo (MVC), MySQL y Bootstrap 5.

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/Licencia-Académica-orange)

---

## 📋 Descripción

**FactuWeb PRO** es un sistema de facturación electrónica que simula el proceso de emisión de documentos fiscales ante la DIAN colombiana (alcance académico). Permite gestionar productos, clientes, empleados y generar facturas electrónicas con CUFE y código QR simulados, control de stock en tiempo real y reporte de utilidades.

---

## ✨ Características Principales

| Módulo | Funcionalidad |
|--------|--------------|
| 🔐 **Autenticación** | Login seguro, bloqueo por intentos fallidos, sesión por inactividad, recuperación por email |
| 👥 **Roles (RBAC)** | Administrador (acceso total) y Empleado (solo facturación y clientes) |
| 🛒 **POS — Facturación** | Carrito en tiempo real, cálculo de IVA, persistencia de borrador, CUFE/QR simulados |
| 📦 **Inventario** | CRUD de productos con imagen, categorías, control de stock automático |
| 🧑‍🤝‍🧑 **Clientes** | CRUD, búsqueda por NIT/Cédula, creación rápida desde el POS |
| 📊 **Rentabilidad** | Dashboard financiero con KPIs, márgenes de utilidad y gráficas por periodo |
| 👔 **Personal** | Gestión de empleados, porcentajes de comisión, roles de acceso |
| ⚙️ **Configuración** | Datos legales de empresa, logo corporativo, resolución DIAN |
| 📋 **Mis Ventas** | Historial personal del vendedor con comisiones estimadas |
| 🆘 **Centro de Ayuda** | Modal siempre visible con canales de soporte y FAQs |
| 🔊 **Lectura Asistida** | Web Speech API en la vista de factura (accesibilidad) |

---

## 🚀 Instalación y Configuración Local

### Requisitos Previos
- PHP 8.x o superior
- MySQL 8.x
- [Laragon](https://laragon.org/) / XAMPP / WAMP (o cualquier servidor con PHP + MySQL)

### Pasos

**1. Clonar el repositorio**
```bash
git clone https://github.com/ingdevelopers449/factuwebpro_v1.git
cd factuwebpro_v1
```

**2. Crear la base de datos**

Importa el schema completo en tu gestor de MySQL (phpMyAdmin, HeidiSQL, etc.):
```sql
-- Ejecuta el archivo:
sql/DB-FACTUWEBPRO.sql
```

**3. Configurar la conexión a la BD**

Edita `config/database.php` con tus credenciales:
```php
$host     = "127.0.0.1";
$user     = "root";
$password = "";          // tu contraseña
$bd       = "factuweb_pro";
$port     = 3306;
```

**4. Levantar el servidor de desarrollo**
```bash
php -S localhost:8080
```

**5. Abrir en el navegador**
```
http://localhost:8080/public/index.php   # Landing pública
http://localhost:8080/views/auth/login.php  # Acceso al sistema
```

---

## 🗂️ Estructura del Proyecto

```
factuwebpro/
├── .agents/
│   └── AGENTS.md            # Contexto para agentes IA
├── config/
│   └── database.php         # Conexión mysqli + zona horaria America/Bogota
├── controllers/
│   ├── auth/AuthController.php
│   ├── FacturaController.php
│   ├── ProductosController.php
│   ├── ClientesController.php
│   ├── EmpresaController.php
│   ├── RentabilidadController.php
│   ├── ParametrizacionController.php
│   ├── CategoriaController.php
│   ├── RegisterUsuarioAdmin.php
│   ├── EditUsuarioController.php
│   └── DeletedUsuarioController.php
├── models/
│   ├── Factura.php           # Transacción completa + DIAN simulada
│   ├── Usuario.php           # Auth, bloqueo, token recuperación
│   ├── Productos.php
│   ├── Clientes.php
│   ├── VentaBorrador.php     # Persistencia POS
│   ├── Rentabilidad.php
│   ├── Empresa.php
│   ├── ResolucionDian.php
│   ├── Categoria.php
│   └── Rol.php
├── views/
│   ├── auth/                 # login, recovery, reset_password
│   ├── admin/                # Todos los módulos (POS, productos, etc.)
│   ├── seller/               # mis_ventas.php (historial propio)
│   └── layouts/              # header, footer, sidebaradmin, sidebarseller
├── public/
│   ├── css/style.css         # Design system completo (Navy + Naranja)
│   ├── js/facturas.js        # Lógica POS + AJAX
│   ├── index.php             # Landing page
│   └── uploads/              # Logos e imágenes de productos
└── sql/
    └── DB-FACTUWEBPRO.sql    # Schema completo
```

---

## 🎨 Diseño Visual

El sistema usa un **design system propio** con las siguientes variables:

| Token | Valor | Uso |
|-------|-------|-----|
| Fondo base | `#12102f` (Navy) | Background principal |
| Acento primario | `#ea580c` (Naranja) | Botones CTA, highlights |
| Acento secundario | `#f59e0b` (Ámbar) | Íconos, alertas |
| Efecto UI | Glassmorphism | Tarjetas con `backdrop-filter` |
| Fuente headings | `--font-heading` | Títulos de módulo |

---

## 🔑 Acceso de Prueba

| Rol | Descripción | Redirección post-login |
|-----|-------------|----------------------|
| Administrador (`id_rol=1`) | Acceso total al sistema | `views/admin/dashboard.php` |
| Empleado (`id_rol=2`) | Solo facturación, clientes e historial propio | `views/seller/mis_ventas.php` |

> ⚠️ Crea los usuarios directamente en la base de datos con `password_hash` usando `PASSWORD_BCRYPT`.

---

## 🏗️ Arquitectura

El sistema sigue el patrón **MVC puro en PHP nativo**:

```
Petición HTTP
    ↓
views/*.php         ← invoca al controlador al cargar
    ↓
controllers/*.php   ← procesa lógica, valida rol, llama al modelo
    ↓
models/*.php        ← accede a MySQL con sentencias preparadas
    ↓
$_SESSION['alert']  ← flash message consumido por footer.php (SweetAlert2)
```

**Convención de invocación de controladores:**
```
URL: controllers/FacturaController.php?action=procesar
```

---

## 🛡️ Seguridad Implementada

- ✅ Contraseñas hasheadas con `password_hash()` (bcrypt)
- ✅ Todas las queries usan `prepare()` + `bind_param()` (anti SQL Injection)
- ✅ Control de sesión con `session_regenerate_id()` en cada login
- ✅ Bloqueo de cuenta tras 3 intentos fallidos (15 min)
- ✅ Cierre de sesión automático por inactividad (10 min)
- ✅ RBAC centralizado en `header.php` (lista blanca por rol)
- ✅ Tokens de recuperación de contraseña con expiración de 30 min

---

## 📄 Módulos y Historias de Usuario

| HU | Módulo | Rol |
|----|--------|-----|
| HU-001 | Login seguro | Admin |
| HU-002 | Acceso limitado por rol | Empleado |
| HU-003 | CRUD Productos + imagen | Admin |
| HU-004 | Generación de facturas electrónicas (POS) | Admin / Empleado |
| HU-005 | Cálculo automático de totales e IVA | Sistema |
| HU-006 | Dashboard de rentabilidad y utilidad | Admin |
| HU-007 | Recuperación de contraseña por email | Admin / Empleado |
| HU-008 | Configuración legal de empresa | Admin |
| HU-009 | Gestión de personal y comisiones | Admin |
| HU-010 | Historial de ventas y comisiones propias | Empleado |
| HU-011 | Centro de Ayuda y Soporte (RF-11) | Admin / Empleado |
| HU-012 | Lectura Asistida en factura (Web Speech API) | Admin / Empleado |
| HU-013 | CRUD Clientes | Admin / Empleado |
| HU-014 | Parametrización DIAN (resolución + impuestos) | Admin |
| HU-015 | CRUD Categorías de productos | Admin |

---

## ⚙️ Tecnologías Utilizadas

- **Backend:** PHP 8.x (MVC sin framework)
- **Base de datos:** MySQL 8.x — driver `mysqli`
- **Frontend:** Bootstrap 5.3, FontAwesome 6.5, Chart.js
- **Alertas:** SweetAlert2
- **PDF:** `window.print()` nativo (sin librería externa)
- **Accesibilidad:** Web Speech API (HU-012)
- **Zona horaria:** `America/Bogota`

---

## 📝 Notas Académicas

> Este proyecto es un **prototipo académico**. La integración con la DIAN es **completamente simulada**:
> - El CUFE se genera con `sha384()` internamente.
> - El código QR apunta a una URL ficticia del catálogo VPFE.
> - No existe conexión real con ningún proveedor tecnológico autorizado (PAT).

---

## 👨‍💻 Autor

Desarrollado por **ING LOZADA** — [@ingdevelopers449](https://github.com/ingdevelopers449)

---

*FactuWeb PRO v1.0 — 2026*
