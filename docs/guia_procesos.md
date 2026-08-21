# FactuWeb PRO — Guía de Procesos del Sistema

> **¿Para qué sirve este documento?**  
> Aquí se explica paso a paso cómo funciona cada proceso del sistema.  
> Si el instructor dice _"el login no funciona"_ o _"la factura no guarda"_, vienes aquí, buscas el proceso, y sabrás exactamente qué archivos tocar y en qué orden revisar.

---

## 📋 Índice Rápido

| # | Proceso | Ir a |
|---|---------|------|
| 1 | Login (Iniciar Sesión) | [Ver](#1-login-iniciar-sesión) |
| 2 | Bloqueo por intentos fallidos | [Ver](#2-bloqueo-por-intentos-fallidos) |
| 3 | Recuperar contraseña | [Ver](#3-recuperar-contraseña) |
| 4 | Restablecer contraseña (con token) | [Ver](#4-restablecer-contraseña-con-token) |
| 5 | Cerrar sesión + Inactividad | [Ver](#5-cerrar-sesión--inactividad) |
| 6 | Control de roles (RBAC) | [Ver](#6-control-de-roles-rbac) |
| 7 | CRUD Categorías | [Ver](#7-crud-categorías) |
| 8 | CRUD Productos | [Ver](#8-crud-productos) |
| 9 | CRUD Clientes | [Ver](#9-crud-clientes) |
| 10 | CRUD Empleados (Usuarios) | [Ver](#10-crud-empleados-usuarios) |
| 11 | Facturación POS | [Ver](#11-facturación-pos) |
| 12 | Autoguardado de borrador | [Ver](#12-autoguardado-de-borrador) |
| 13 | Imprimir factura (PDF) | [Ver](#13-imprimir-factura-pdf) |
| 14 | Simulación DIAN | [Ver](#14-simulación-dian) |
| 15 | Mis Ventas (Vendedor) | [Ver](#15-mis-ventas-vendedor) |
| 16 | Dashboard Administrador | [Ver](#16-dashboard-administrador) |
| 17 | Rentabilidad y Reportes | [Ver](#17-rentabilidad-y-reportes) |
| 18 | Datos de Empresa | [Ver](#18-datos-de-empresa) |
| 19 | Parametrización DIAN | [Ver](#19-parametrización-dian) |
| 20 | Centro de Ayuda | [Ver](#20-centro-de-ayuda) |

---

## 1. Login (Iniciar Sesión)

### ¿Qué hace?
El usuario escribe su correo y contraseña. El sistema valida y lo manda al módulo correcto según su rol.

### Archivos involucrados
| Archivo | Qué hace |
|---------|----------|
| `views/auth/login.php` | El formulario que ve el usuario (HTML) |
| `controllers/auth/AuthController.php` → método `login()` | Recibe los datos, valida contra la DB |
| `models/Usuario.php` → método `obtenerPorEmail()` | Busca al usuario en la tabla `usuarios` |

### Flujo paso a paso
```
1. Usuario llena email + contraseña en login.php
2. El form hace POST a → AuthController.php?accion=login
3. AuthController llama a Usuario::obtenerPorEmail($email)
4. Si no existe → alerta "Usuario no encontrado" → vuelve a login
5. Si estado = 'bloqueado' → verifica si ya pasaron 15 min
6. Si la contraseña no coincide → registra intento fallido
7. Si la contraseña SÍ coincide:
   - Resetea intentos a 0
   - Regenera el session_id (seguridad)
   - Guarda datos en $_SESSION['usuario']
   - Redirige según rol:
     * Rol 1 (Admin) → views/admin/dashboard.php
     * Rol 2 (Empleado) → views/seller/mis_ventas.php
```

### ¿Algo no funciona? Revisa esto:
- **"No me deja entrar"** → Verifica que el email exista en la tabla `usuarios` y que `estado` = `'activo'`
- **"Dice usuario no encontrado"** → El email puede tener espacios, revisa `trim()` en AuthController
- **"Me manda a login otra vez"** → Revisa que `session_start()` esté al inicio de AuthController

---

## 2. Bloqueo por intentos fallidos

### ¿Qué hace?
Si el usuario falla la contraseña 3 veces seguidas, se bloquea la cuenta por 15 minutos.

### Archivos involucrados
| Archivo | Qué hace |
|---------|----------|
| `controllers/auth/AuthController.php` → `login()` | Cuenta los intentos |
| `models/Usuario.php` → `registrarIntentoFallido()` | Suma +1 al campo `intentos_fallidos` |
| `models/Usuario.php` → `resetearIntentosYActualizarAcceso()` | Pone intentos en 0 al desbloquear |

### Flujo paso a paso
```
1. Contraseña incorrecta → llama registrarIntentoFallido()
2. Si intentos_fallidos llega a 3 → estado = 'bloqueado' + guarda ultimo_acceso = NOW()
3. En el próximo login, si estado = 'bloqueado':
   - Calcula: ¿pasaron 15 minutos desde ultimo_acceso?
   - NO → muestra "Cuenta bloqueada, espere X minutos"
   - SÍ → desbloquea automáticamente (resetea intentos, estado = 'activo')
```

### ¿Algo no funciona? Revisa esto:
- **"Se bloqueó pero no se desbloquea"** → Revisa la zona horaria en `config/database.php` (`America/Bogota`)
- **"Se bloquea al primer intento"** → Verifica que `intentos_fallidos` empiece en 0 en la DB

---

## 3. Recuperar contraseña

### ¿Qué hace?
El usuario ingresa su email. El sistema genera un token temporal y muestra el enlace de recuperación (simulado, no envía correo real).

### Archivos involucrados
| Archivo | Qué hace |
|---------|----------|
| `views/auth/recovery.php` | Formulario "Ingresa tu correo" |
| `controllers/auth/AuthController.php` → `forgot_password()` | Genera token y guarda en DB |
| `models/Usuario.php` → `guardarTokenRecuperacion()` | Guarda token + fecha expiración en tabla `usuarios` |

### Flujo paso a paso
```
1. Usuario escribe su email en recovery.php
2. POST a → AuthController.php?accion=forgot_password
3. Valida que el email exista en la DB
4. Genera token aleatorio con bin2hex(random_bytes(32))
5. Guarda token + expiración (30 min) en campos token_recuperacion y token_expiracion
6. Muestra SweetAlert con el enlace (simulado, no envía email real)
7. El enlace tiene formato: reset_password.php?token=XXXXXX
```

### ¿Algo no funciona? Revisa esto:
- **"Dice correo inválido"** → Verifica que el email exista en la tabla `usuarios`
- **"No muestra el enlace"** → Revisa que `$_SESSION['alert']` se esté seteando correctamente
- **Link "¿Olvidaste tu contraseña?" no funciona** → Está FUERA del `<form>` en login.php (se movió para evitar validación HTML5)

---

## 4. Restablecer contraseña (con token)

### Archivos involucrados
| Archivo | Qué hace |
|---------|----------|
| `views/auth/reset_password.php` | Formulario "Nueva contraseña" |
| `controllers/auth/AuthController.php` → `reset_password()` | Valida token y actualiza password |
| `models/Usuario.php` → `obtenerPorToken()` | Busca usuario por token |
| `models/Usuario.php` → `actualizarPassword()` | Hashea y guarda nueva contraseña |

### Flujo paso a paso
```
1. Usuario llega con URL: reset_password.php?token=XXXXXX
2. POST con nueva contraseña → AuthController.php?accion=reset_password
3. Busca usuario con ese token en DB (obtenerPorToken)
4. Verifica que token no haya expirado (30 min)
5. Hashea nueva contraseña con password_hash()
6. Actualiza en DB + limpia el token
7. Redirige a login con SweetAlert "Contraseña actualizada"
```

---

## 5. Cerrar sesión + Inactividad

### Archivos involucrados
| Archivo | Qué hace |
|---------|----------|
| `controllers/auth/AuthController.php` → `logout()` | Destruye la sesión |
| `views/layouts/header.php` | Detecta inactividad de 10 min |
| `views/layouts/sidebaradmin.php` / `sidebarseller.php` | Botón de cerrar sesión en sidebar |

### Flujo
```
Cerrar sesión manual:
1. Clic en botón logout del sidebar
2. GET a → AuthController.php?accion=logout
3. session_unset() + session_destroy()
4. Redirige a login.php

Inactividad automática (10 min):
1. header.php guarda $_SESSION['ultima_actividad'] = time()
2. En cada página, compara con time() actual
3. Si diferencia > 600 segundos (10 min) → destruye sesión → redirige a login
```

---

## 6. Control de roles (RBAC)

### ¿Qué hace?
Controla qué módulos puede ver cada usuario según su rol.

### Archivo clave: `views/layouts/header.php`

```
Rol 1 (Administrador) → Puede ver TODO
Rol 2 (Empleado)      → Solo puede ver:
  - facturas.php (Facturación POS)
  - imprimir_factura.php
  - clientes.php
  - mis_ventas.php

Si Rol 2 intenta acceder a otro módulo:
  → Redirige a mis_ventas.php + SweetAlert "Acceso Denegado"
```

### ¿Algo no funciona? Revisa esto:
- **"El empleado ve módulos de admin"** → Revisa la lista blanca `$modulos_seller` en `header.php`
- **"El sidebar muestra todo"** → header.php carga `sidebarseller.php` (rol 2) o `sidebaradmin.php` (rol 1)

---

## 7. CRUD Categorías

### Archivos involucrados
| Archivo | Qué hace |
|---------|----------|
| `views/admin/categorias.php` | Vista con tabla + modales crear/editar |
| `controllers/CategoriaController.php` | Procesa guardar, cambiar estado |
| `models/Categoria.php` | Queries a tabla `categorias` |

### Flujos
```
CREAR:
1. Clic "Nueva Categoría" → abre modal
2. Llena nombre + descripción + estado
3. Submit POST → CategoriaController.php?action=guardar
4. Si id_categoria = 0 → INSERT (insertar)
5. Si id_categoria > 0 → UPDATE (actualizar)

EDITAR:
1. Clic ícono lápiz → abre modal con datos precargados (JS)
2. Mismo flujo que crear pero con id > 0

ACTIVAR / DESACTIVAR:
1. Clic ícono verde/rojo
2. POST → CategoriaController.php?action=cambiar_estado
3. Si tiene productos asociados → SweetAlert de advertencia
4. Cambia estado en DB entre 'activa' / 'inactiva'
```

---

## 8. CRUD Productos

### Archivos involucrados
| Archivo | Qué hace |
|---------|----------|
| `views/admin/productos.php` | Vista con tabla + modal crear/editar + paginación |
| `controllers/ProductosController.php` | Procesa CRUD |
| `models/Productos.php` | Queries a tabla `productos` |

### Flujos
```
CREAR:
1. Clic "Agregar Producto" → abre modal
2. Llena: código barras, nombre, precio compra, precio venta, stock, IVA, categoría, imagen
3. Si precio_venta <= precio_compra → SweetAlert alerta de margen (HU-006.3)
4. POST → ProductosController.php?action=guardar
5. Si hay imagen → sube a public/uploads/

EDITAR:
1. Clic lápiz → modal con datos precargados
2. Mismo flujo, pero UPDATE en vez de INSERT

ACTIVAR / DESACTIVAR:
1. Clic botón toggle
2. POST → ProductosController.php?action=alternarEstado
3. Cambia entre 'activo' / 'inactivo'
```

### ¿Algo no funciona? Revisa esto:
- **"No sube la imagen"** → Verifica que la carpeta `public/uploads/` exista y tenga permisos
- **"No muestra la categoría"** → Solo se listan categorías con estado = 'activa'

---

## 9. CRUD Clientes

### Archivos involucrados
| Archivo | Qué hace |
|---------|----------|
| `views/admin/clientes.php` | Vista con tabla + modal crear/editar |
| `controllers/ClientesController.php` | Procesa CRUD |
| `models/Clientes.php` | Queries a tabla `clientes` |

### Flujos
```
CREAR:
1. Clic "Nuevo Cliente" → modal
2. Llena: identificación, nombre/razón social, email, dirección, teléfono
3. POST → ClientesController.php?action=guardar
4. Valida que la identificación no esté duplicada

EDITAR:
1. Clic lápiz → modal precargado
2. Misma validación de duplicados (excluyendo el cliente actual)

ELIMINAR:
1. Clic ícono basura → SweetAlert confirmación
2. POST → ClientesController.php?action=eliminar
```

---

## 10. CRUD Empleados (Usuarios)

### Archivos involucrados
| Archivo | Qué hace |
|---------|----------|
| `views/admin/gempleados.php` | Vista con tabla + modales |
| `controllers/RegisterUsuarioAdmin.php` | Crea usuario nuevo |
| `controllers/EditUsuarioController.php` | Edita usuario existente |
| `controllers/DeletedUsuarioController.php` | Elimina usuario |
| `models/Usuario.php` | Queries a tabla `usuarios` |

### Flujos
```
CREAR:
1. Clic "Agregar Usuario" → modal
2. Llena: nombre, email, contraseña, rol, estado, % comisión
3. POST → RegisterUsuarioAdmin.php
4. Hashea contraseña con password_hash(PASSWORD_BCRYPT)
5. Valida que email no exista ya

EDITAR:
1. Clic lápiz → modal precargado (JS con data-attributes)
2. POST → EditUsuarioController.php
3. Actualiza nombre, rol, estado, comisión (email es readonly)

ELIMINAR:
1. Clic basura → SweetAlert confirmación
2. POST → DeletedUsuarioController.php
```

### Campo importante: `porcentaje_comision`
- Se configura en el modal de crear/editar
- Al momento de facturar, este % se "congela" en la factura (`porcentaje_comision_aplicado`)
- Si después cambias el %, las facturas antiguas NO cambian

---

## 11. Facturación POS

### ¡Este es el proceso más complejo del sistema!

### Archivos involucrados
| Archivo | Qué hace |
|---------|----------|
| `views/admin/facturas.php` | Interfaz POS (punto de venta) |
| `public/js/facturas.js` | Lógica del carrito en JavaScript (AJAX) |
| `controllers/FacturaController.php` | Procesa toda la facturación |
| `models/Factura.php` → `crearFactura()` | INSERT en DB con transacción |
| `models/VentaBorrador.php` | Autoguardado del carrito |

### Flujo paso a paso
```
1. Se carga facturas.php → AJAX a FacturaController?action=init_pos
   → Trae: productos, clientes, resolución DIAN, borrador previo

2. Usuario busca producto → lo agrega al carrito (JavaScript)
   → facturas.js calcula subtotal, IVA, total en TIEMPO REAL

3. Cada cambio en el carrito → AJAX guarda borrador automático
   → FacturaController?action=guardar_borrador

4. Usuario selecciona cliente (opcional, si no → "Consumidor Final")

5. Clic "Finalizar Venta" → POST JSON a FacturaController?action=procesar

6. En el backend (Factura::crearFactura):
   a. Consulta resolución DIAN activa
   b. Obtiene consecutivo siguiente
   c. Congela porcentaje_comision del vendedor
   d. Genera CUFE = sha384(empresa + prefijo + consecutivo + total + timestamp)
   e. Genera QR simulado
   f. Valida NIT del cliente (si < 5 chars → estado_dian = 'rechazada')
   g. TRANSACCIÓN MySQL:
      - INSERT factura
      - INSERT detalles (por cada producto)
      - UPDATE stock de cada producto (-cantidad)
      - UPDATE contador de resolución DIAN (+1)
   h. Limpia borrador

7. Responde con id_factura → abre imprimir_factura.php automáticamente
```

### ¿Algo no funciona? Revisa esto:
- **"No carga productos"** → Revisa `init_pos()` en FacturaController y que haya productos `activo`
- **"No calcula IVA"** → La lógica está en `facturas.js`, busca la función de cálculo
- **"Error al finalizar"** → Revisa la consola del navegador (F12) para ver errores AJAX
- **"No descuenta stock"** → Está dentro de la transacción en `crearFactura()`, si falla un UPDATE, se hace rollback de todo
- **"No hay resolución DIAN"** → Debes crear una en Parametrización con estado = 'activa'

---

## 12. Autoguardado de borrador

### ¿Qué hace?
Si estás en medio de una venta y cierras el navegador (o se va la luz), al volver el carrito sigue ahí.

### Archivos involucrados
| Archivo | Qué hace |
|---------|----------|
| `public/js/facturas.js` | Envía AJAX cada que cambia el carrito |
| `controllers/FacturaController.php` → `guardar_borrador()` | Recibe y guarda |
| `models/VentaBorrador.php` | INSERT/UPDATE en `ventas_borrador` + `detalle_borrador` |

### Flujo
```
1. Usuario agrega/quita producto del carrito
2. facturas.js detecta el cambio
3. AJAX POST → FacturaController?action=guardar_borrador
4. VentaBorrador::guardarBorrador() → borra detalles anteriores + inserta nuevos
5. Al recargar la página, init_pos() carga el borrador existente
6. Al finalizar venta → VentaBorrador::limpiarBorrador() borra todo
```

---

## 13. Imprimir factura (PDF)

### Archivos involucrados
| Archivo | Qué hace |
|---------|----------|
| `views/admin/imprimir_factura.php` | Vista de impresión (HTML para `window.print()`) |
| `models/Factura.php` → `obtenerFacturaPorId()` + `obtenerDetallesFactura()` | Trae datos |

### Flujo
```
1. Se abre con: imprimir_factura.php?id=123
2. Consulta factura por ID → trae cabecera + detalles + datos empresa
3. Renderiza HTML con diseño de factura (logo, datos empresa, CUFE, QR)
4. Botón "Imprimir" → ejecuta window.print() (nativo, sin librería)
5. También tiene Web Speech API (HU-012) → lee la factura en voz alta
```

---

## 14. Simulación DIAN

### ⚠️ Esto es ACADÉMICO, NO hay integración real con la DIAN

### ¿Cómo funciona?
```
CUFE (Código Único de Factura Electrónica):
→ sha384(id_empresa + prefijo + consecutivo + total + timestamp)
→ Se genera en Factura::crearFactura()

QR:
→ URL ficticia: https://catalogo-vpfe.dian.gov.co/document/searchqr?documentkey=CUFE

Estado DIAN:
→ 'aceptada' por defecto
→ 'rechazada' si el NIT del cliente tiene < 5 caracteres o caracteres especiales

Esto se controla en: models/Factura.php → crearFactura()
```

---

## 15. Mis Ventas (Vendedor)

### Archivos involucrados
| Archivo | Qué hace |
|---------|----------|
| `views/seller/mis_ventas.php` | Vista exclusiva del vendedor |
| `models/Factura.php` → `obtenerVentasPorUsuario()` | Filtra facturas WHERE id_usuario = ? |

### Flujo
```
1. Vendedor (rol 2) entra al sistema → redirigido a mis_ventas.php
2. La vista llama a Factura::obtenerVentasPorUsuario(id_usuario)
3. Muestra SOLO las facturas que hizo ESE vendedor (nunca ve las de otros)
4. Puede filtrar por rango de fechas
5. Muestra comisión estimada = SUM(total_pagar) × (porcentaje_comision / 100)
```

### ¿Algo no funciona? Revisa esto:
- **"No muestra ventas"** → Verifica que `$_SESSION['usuario']['id_usuario']` sea el correcto
- **"Muestra ventas de otros"** → Revisa el WHERE en `obtenerVentasPorUsuario()`

---

## 16. Dashboard Administrador

### Archivos involucrados
| Archivo | Qué hace |
|---------|----------|
| `views/admin/dashboard.php` | Vista del dashboard |
| `controllers/DashboardController.php` | Consultas de KPIs |

### ¿Qué muestra?
```
- Ventas del día (SUM total_pagar WHERE fecha = hoy)
- Ventas del mes
- Productos con stock bajo (< 5 unidades)
- Total clientes registrados
- Gráfica de ventas últimos 7 días (Chart.js)
- Top 5 productos más vendidos
- Tabla de últimas 8 facturas emitidas
```

---

## 17. Rentabilidad y Reportes

### Archivos involucrados
| Archivo | Qué hace |
|---------|----------|
| `views/admin/rentabilidad.php` | Vista del reporte |
| `controllers/RentabilidadController.php` | Lógica de consultas |
| `models/Rentabilidad.php` | Queries analíticas |

### ¿Qué muestra?
```
- Consolidado: total ventas, costo, utilidad bruta, margen %
- Desglose por categoría
- Historial de facturas con utilidad individual
- Detalle por factura (modal AJAX)
- Filtro por rango de fechas
```

---

## 18. Datos de Empresa

### Archivos involucrados
| Archivo | Qué hace |
|---------|----------|
| `views/admin/empresa.php` | Formulario de datos |
| `controllers/EmpresaController.php` | Guarda/actualiza |
| `models/Empresa.php` | Queries a tabla `empresa` |

### Flujo
```
1. Carga datos actuales de la empresa (si existen)
2. Admin edita: NIT, razón social, dirección, teléfono, email, logo
3. Si hay logo → sube a public/uploads/
4. Si ya existe empresa → UPDATE, si no → INSERT
```

---

## 19. Parametrización DIAN

### Archivos involucrados
| Archivo | Qué hace |
|---------|----------|
| `views/admin/parametrizacion.php` | Formulario de resolución |
| `controllers/ParametrizacionController.php` | Guarda/actualiza |
| `models/ResolucionDian.php` | Queries a tabla `resolucion_dian` |

### ¿Qué se configura?
```
- Número de resolución (ej: 18764015524)
- Fecha de vigencia
- Prefijo (ej: FV)
- Rango inicial y final (ej: 1 a 1000)
- Contador actual (se incrementa automáticamente con cada factura)
- Estado: activa / inactiva
```

### ⚠️ IMPORTANTE: Sin resolución activa, NO se pueden crear facturas

---

## 20. Centro de Ayuda

### Archivo: `views/layouts/footer.php`

```
- Es un modal (popup) accesible desde un botón flotante en todas las páginas
- Muestra información de ayuda del sistema (RF-11)
- NO se debe modificar el footer visual, solo el contenido del modal
```

---

## 🔧 Referencia rápida: ¿Dónde está cada cosa?

| Si te dicen... | Busca en... |
|----------------|-------------|
| "El login no funciona" | `controllers/auth/AuthController.php` → `login()` |
| "No se bloquea la cuenta" | `models/Usuario.php` → `registrarIntentoFallido()` |
| "La factura no guarda" | `models/Factura.php` → `crearFactura()` |
| "El IVA calcula mal" | `public/js/facturas.js` (buscar "iva" o "tarifa") |
| "El empleado ve módulos de admin" | `views/layouts/header.php` → `$modulos_seller` |
| "No carga el sidebar" | `views/layouts/header.php` (decide cuál sidebar cargar) |
| "Las fechas están mal" | `config/database.php` → `date_default_timezone_set('America/Bogota')` |
| "No sube imágenes" | `controllers/ProductosController.php` → `guardar()` + carpeta `public/uploads/` |
| "El breadcrumb no muestra el módulo" | `views/layouts/header.php` → `$breadcrumb_map` |
| "SweetAlert no aparece" | `views/layouts/footer.php` → consume `$_SESSION['alert']` |
| "El borrador no se guarda" | `controllers/FacturaController.php` → `guardar_borrador()` |
| "La comisión no se congela" | `models/Factura.php` → `crearFactura()` → buscar `porcentaje_comision` |
| "El CUFE sale mal" | `models/Factura.php` → buscar `hash('sha384', ...)` |
| "Productos bajos no se muestran" | `controllers/DashboardController.php` → query `stock_actual < 5` |

---

## 🗄️ Tablas de la Base de Datos

| Tabla | Para qué sirve |
|-------|----------------|
| `empresa` | Datos legales de la empresa (NIT, razón social, logo) |
| `resolucion_dian` | Configuración de resolución de facturación |
| `roles` | Roles: 1=Admin, 2=Empleado |
| `usuarios` | Cuentas de usuario con contraseña hasheada |
| `categorias` | Agrupaciones de productos |
| `productos` | Catálogo de productos con precios y stock |
| `clientes` | Base de datos de clientes |
| `facturas` | Cabecera de cada factura emitida |
| `detalle_factura` | Líneas/productos de cada factura |
| `ventas_borrador` | Carrito temporal (1 por usuario) |
| `detalle_borrador` | Productos del carrito temporal |

---

> **Tip:** Si algo falla y no sabes por dónde empezar, abre la **consola del navegador** (F12 → Console) para ver errores de JavaScript, y revisa el **terminal del servidor PHP** para ver errores de PHP.

> **Generado para:** FactuWeb PRO  
> **Fecha:** 2026-08-21
