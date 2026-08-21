# FactuWeb PRO — Contexto para Agente IA

## Stack & Arquitectura
- **Patrón:** MVC puro en PHP nativo (sin framework)
- **DB:** MySQL · driver `mysqli` · sentencias preparadas (`prepare()`) **obligatorias**
- **Servidor dev:** `php -S localhost:8080` desde raíz del proyecto
- **Zona horaria global:** `America/Bogota` — establecida en `config/database.php`
- **Frontend:** Bootstrap 5.3.8 (Local) + FontAwesome 6.5 + SweetAlert2 + Chart.js
- **PDF/impresión:** `window.print()` nativo (sin librería externa)
- **Autenticación:** Sesiones PHP (`$_SESSION['usuario']`)

---

## Diseño Visual (OBLIGATORIO)
| Token | Valor |
|---|---|
| Fondo base | Navy `#12102f` |
| Acento principal | Naranja `#ea580c` |
| Acento secundario | Ámbar `#f59e0b` |
| Efecto UI | Glassmorphism (tarjetas con `backdrop-filter`) |
| Fuente headings | Variable CSS `--font-heading` |

---

## Roles y Control de Acceso (RBAC)

| `id_rol` | Nombre | Acceso |
|---|---|---|
| `1` | Administrador | Todo el sistema |
| `2` | Empleado/Vendedor | Solo módulos permitidos |

**Módulos permitidos para rol 2** (lista blanca en `views/layouts/header.php`):
`facturas.php`, `imprimir_factura.php`, `clientes.php`, `mis_ventas.php`

**Guardián de roles:** `views/layouts/header.php` intercepta TODA petición. Si `id_rol=2` accede a un módulo no permitido → redirige a `views/seller/mis_ventas.php` + alerta "Acceso Denegado".

**Sidebar dinámico:** `header.php` carga `sidebarseller.php` (rol 2) o `sidebaradmin.php` (rol 1).

**Login → redirección:**
- Rol 1 → `views/admin/dashboard.php`
- Rol 2 → `views/seller/mis_ventas.php`

---

## Estructura de Archivos

```
factuwebpro/
├── config/
│   └── database.php          # Conexión mysqli → $conn global + timezone
├── controllers/
│   ├── auth/
│   │   └── AuthController.php        # login, logout, bloqueo, inactividad
│   ├── CategoriaController.php
│   ├── ClientesController.php
│   ├── DeletedUsuarioController.php
│   ├── EditUsuarioController.php
│   ├── EmpresaController.php         # HU-008: datos legales empresa
│   ├── FacturaController.php         # HU-004/005: POS, procesar, borrador, DIAN
│   ├── ParametrizacionController.php # HU-014: resolución DIAN, impuestos
│   ├── ProductosController.php       # HU-003: CRUD productos + imagen
│   ├── RegisterUsuarioAdmin.php
│   └── RentabilidadController.php    # HU-006: utilidad, márgenes
├── models/
│   ├── Categoria.php
│   ├── Clientes.php
│   ├── Empresa.php
│   ├── Factura.php          # crearFactura() → transacción completa + DIAN sim
│   ├── Productos.php
│   ├── Rentabilidad.php
│   ├── ResolucionDian.php
│   ├── Rol.php
│   ├── Usuario.php          # login, bloqueo, reseteo intentos, token recuperación
│   └── VentaBorrador.php    # guardarBorrador(), limpiarBorrador()
├── views/
│   ├── auth/
│   │   ├── login.php
│   │   ├── recovery.php     # solicitar enlace por email
│   │   └── reset_password.php
│   ├── admin/               # Módulos exclusivos admin (+ compartidos con seller)
│   │   ├── dashboard.php
│   │   ├── Facturas.php     # POS — compartido con rol 2
│   │   ├── clientes.php     # CRUD clientes — compartido con rol 2
│   │   ├── imprimir_factura.php  # Vista PDF — compartido con rol 2
│   │   ├── productos.php    # Solo rol 1
│   │   ├── categorias.php   # Solo rol 1
│   │   ├── empresa.php      # Solo rol 1
│   │   ├── gempleados.php   # Solo rol 1
│   │   ├── parametrizacion.php  # Solo rol 1
│   │   └── rentabilidad.php     # Solo rol 1
│   ├── seller/
│   │   └── mis_ventas.php   # Historial propio + comisiones (WHERE id_usuario=?)
│   └── layouts/
│       ├── header.php       # Sesión + RBAC + sidebar dinámico + topbar
│       ├── footer.php       # JS global + modal Centro de Ayuda (RF-11)
│       ├── sidebaradmin.php
│       └── sidebarseller.php
├── public/
│   ├── css/style.css        # Sistema de diseño completo
│   ├── js/facturas.js       # Lógica POS + autoguardado borrador (AJAX)
│   ├── index.php            # Landing page pública
│   └── uploads/             # Logos e imágenes de productos
└── sql/
    └── DB-FACTUWEBPRO.sql   # Schema completo
```

---

## Schema de Base de Datos (Tablas Clave)

```sql
-- Configuración
empresa(id_empresa, nit UNIQUE, razon_social, direccion, telefono, email, logo_url)
resolucion_dian(id_resolucion, id_empresa, numero_resolucion UNIQUE, fecha_vigencia,
                prefijo, rango_inicial, rango_final, contador_actual, estado ENUM('activa','inactiva'))

-- Seguridad
roles(id_rol, nombre_rol)
usuarios(id_usuario, id_empresa, nombre, email UNIQUE, password_hash,
         id_rol, estado ENUM('activo','bloqueado','inactivo'),
         intentos_fallidos, ultimo_acceso, token_recuperacion,
         porcentaje_comision DECIMAL(5,2))

-- Catálogo
categorias(id_categoria, nombre_categoria UNIQUE, descripcion, estado ENUM('activa','inactiva'))
clientes(id_cliente, identificacion UNIQUE, nombre_razon_social, email, direccion, telefono)
productos(id_producto, id_categoria FK, codigo_barras UNIQUE, nombre_producto,
          precio_compra, precio_venta, stock_actual, estado_producto ENUM('activo','inactivo'),
          tarifa_iva DEFAULT 19.00, imagen_url)

-- Facturación
facturas(id_factura, id_empresa FK, id_resolucion FK, prefijo_resolucion, consecutivo,
         fecha_emision, id_cliente FK NULL, id_usuario FK,
         subtotal, total_iva, total_pagar,
         porcentaje_comision_aplicado,   -- ← congelado al momento de la venta (HU-004.16)
         cufe UNIQUE, codigo_qr, 
         estado_dian ENUM('pendiente','aceptada','rechazada'),
         motivo_rechazo,                -- ← solo si rechazada (HU-004.14)
         fecha_validacion_dian)
detalle_factura(id_detalle, id_factura FK CASCADE, id_producto FK,
                cantidad, precio_unitario_venta, precio_unitario_costo, subtotal_linea)

-- Borrador (Persistencia POS — HU-004.10/11)
ventas_borrador(id_borrador, id_usuario UNIQUE FK, id_cliente FK NULL, fecha_actualizacion)
detalle_borrador(id_detalle_borrador, id_borrador FK CASCADE, id_producto FK, cantidad)
```

**Seed de Datos Inicial (DB-FACTUWEBPRO.sql):**
- **Usuario Admin:** `admin@factuweb.com` / `admin123`
- **Roles y Empresa:** Ya preconfigurados.

---

## Flujos de Negocio Críticos

### Autenticación (HU-001/002)
1. `login.php` → `AuthController.php?accion=login` (POST)
2. Verifica `estado='bloqueado'` → informa tiempo restante
3. 3 intentos fallidos → `estado='bloqueado'` por 15 min
4. Éxito → `session_regenerate_id(true)` → `$_SESSION['usuario']` → redirect por rol
5. Inactividad 10 min → destruye sesión → redirect login (detectado en `header.php`)
6. Recuperación: email → `token_recuperacion` en DB → link expira 30 min

### POS — Facturación (HU-004/005)
1. `Facturas.php` carga via AJAX → `FacturaController?action=init_pos`
2. Agrega productos al carrito JS (`facturas.js`) → recalcula IVA en tiempo real
3. Autoguardado AJAX cada cambio → `FacturaController?action=guardar_borrador`
4. "Finalizar venta" → `FacturaController?action=procesar` (POST JSON)
5. En `Factura::crearFactura()`:
   - Consulta resolución DIAN activa
   - Obtiene `porcentaje_comision` del usuario → lo congela en `porcentaje_comision_aplicado`
   - Valida NIT del cliente: si `strlen < 5` → `estado_dian='rechazada'`
   - Genera CUFE (`sha384`) y QR simulados
   - Transacción: INSERT factura + INSERT detalles + UPDATE stock + UPDATE contador_resolucion
6. Limpia borrador → descarga automática PDF (`imprimir_factura.php`)

### DIAN Simulada (HU-004.8/13/14) — ACADÉMICO
- **No** hay integración real con DIAN
- CUFE = `sha384(id_empresa + prefijo + consecutivo + total + timestamp)`
- QR = URL ficticia del catálogo VPFE con el CUFE
- Rechazo simulado: NIT < 5 chars o con caracteres especiales → `estado_dian='rechazada'`

### Historial Vendedor (HU-010)
- `mis_ventas.php` → `Factura::obtenerVentasPorUsuario(id_usuario, fecha_inicio, fecha_fin)`
- Filtro `WHERE f.id_usuario = ?` → el vendedor **nunca** ve facturas de otros
- Comisión estimada = `SUM(total_pagar) * (porcentaje_comision/100)`

---

## Reglas de Desarrollo (NO NEGOCIABLES)

1. **SQL:** Siempre usar `$conn->prepare()` + `bind_param()`. **Prohibido** concatenar variables en queries.
2. **Roles:** Toda vista protegida debe pasar por `header.php`. Módulos exclusivos de admin llevan validación explícita de rol.
3. **Diseño:** Paleta Navy + Naranja + Glassmorphism. No usar Tailwind.
4. **Imágenes productos:** Se suben a `public/uploads/`. Solo rutas relativas en DB, no archivos binarios.
5. **Fechas:** Siempre trabajar en zona `America/Bogota` (ya configurada en `database.php`).
6. **Alertas UI:** Usar SweetAlert2 para confirmaciones destructivas y éxito/error de operaciones.
7. **Alerta pérdida de margen (HU-006.3):** En `productos.php`, si `precio_venta <= precio_compra`, mostrar SweetAlert antes de guardar.
8. **Footer:** **No modificar** el footer actual. Solo el modal de ayuda (RF-11) puede ajustarse.

---

## Historias de Usuario — Resumen de Cumplimiento

| HU | Módulo principal | Estado |
|---|---|---|
| HU-001 | `AuthController`, `login.php` | ✅ Completo |
| HU-002 | `header.php` (RBAC), `sidebarseller.php` | ✅ Completo |
| HU-003 | `productos.php`, `ProductosController` | ✅ Completo |
| HU-004 | `Facturas.php`, `FacturaController`, `Factura.php` | ✅ Completo |
| HU-005 | `facturas.js` (cálculo tiempo real) | ✅ Completo |
| HU-006 | `rentabilidad.php`, `RentabilidadController` | ✅ Completo |
| HU-007 | `recovery.php`, `reset_password.php`, `AuthController` | ✅ Completo |
| HU-008 | `empresa.php`, `EmpresaController` | ✅ Completo |
| HU-009 | `gempleados.php`, `EditUsuarioController`, `RegisterUsuarioAdmin` | ✅ Completo |
| HU-010 | `mis_ventas.php` + filtro `id_usuario` | ✅ Completo |
| HU-011 | Modal "Centro de Ayuda" en `footer.php` (botón siempre visible) | ✅ Completo |
| HU-012 | `imprimir_factura.php` (Web Speech API) | ✅ Completo |
| HU-013 | `clientes.php`, `ClientesController` | ✅ Completo |
| HU-014 | `parametrizacion.php`, `ParametrizacionController` | ✅ Completo |
| HU-015 | `categorias.php`, `CategoriaController` | ✅ Completo |

---

## Convenciones de Código

```php
// Controlador típico — invoke por query string
// URL: controllers/FooController.php?action=bar
$action = $_GET['action'] ?? 'default';
$ctrl = new FooController();
if (method_exists($ctrl, $action)) { $ctrl->$action(); }

// Modelo típico — acceso DB
public function obtenerPorId(int $id): ?array {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM tabla WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ?: null;
}

// Sesión de usuario disponible en toda vista (después de header.php)
$_SESSION['usuario'] = [
    'id_usuario' => int,
    'id_empresa' => int,
    'nombre'     => string,
    'email'      => string,
    'id_rol'     => string  // '1' o '2'
];

// Alertas flash (SweetAlert2 en footer.php las consume automáticamente)
$_SESSION['alert'] = ['icon' => 'success|error|warning', 'title' => '...', 'text' => '...'];
```
