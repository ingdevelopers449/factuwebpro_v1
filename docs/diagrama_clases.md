# Diagrama de Clases — FactuWeb PRO

> Referencia de entidades (Modelos) y controladores con sus atributos y métodos.  
> Notación: `+` público, `-` privado, `#` protegido.

---

## 🗂️ MODELOS (Entidades)

---

### `Categoria`
**Archivo:** `models/Categoria.php`  
**Tabla:** `categorias`

| Tipo | Nombre |
|------|--------|
| **Atributos** | |
| `-` | `$conn : mysqli` |
| **Métodos** | |
| `+` | `__construct()` |
| `+` | `obtenerTodas() : array` |
| `+` | `obtenerActivas() : array` |
| `+` | `obtenerPorId(int $id_categoria) : ?array` |
| `+` | `existeNombre(string $nombre, ?int $id_excluir = null) : bool` |
| `+` | `insertar(string $nombre_categoria, string $descripcion, string $estado, string $fecha_creacion, string $fecha_actualizacion) : int\|false` |
| `+` | `actualizar(int $id_categoria, string $nombre_categoria, string $descripcion, string $estado, string $fecha_creacion, string $fecha_actualizacion) : bool` |
| `+` | `cambiarEstado(int $id_categoria, string $nuevo_estado, string $fecha_actualizacion) : bool` |

---

### `Cliente`
**Archivo:** `models/Clientes.php`  
**Tabla:** `clientes`

| Tipo | Nombre |
|------|--------|
| **Atributos** | |
| `-` | `$conn : mysqli` |
| **Métodos** | |
| `+` | `__construct()` |
| `+` | `obtenerTodos() : array` |
| `+` | `buscar($termino) : array` |
| `+` | `obtenerPorIdentificacion($identificacion, $exclude_id = null) : ?array` |
| `+` | `obtenerPorId($id_cliente) : ?array` |
| `+` | `insertar($identificacion, $nombre_razon_social, $email, $direccion, $telefono) : bool` |
| `+` | `actualizar($id_cliente, $identificacion, $nombre_razon_social, $email, $direccion, $telefono) : bool` |
| `+` | `eliminar($id_cliente) : bool` |

---

### `Empresa`
**Archivo:** `models/Empresa.php`  
**Tabla:** `empresa`

| Tipo | Nombre |
|------|--------|
| **Atributos** | |
| `-` | `$conn : mysqli` |
| **Métodos** | |
| `+` | `__construct()` |
| `+` | `obtenerTodos() : array` |
| `+` | `insertar($nit, $razon_social, $direccion, $telefono, $correo, $logo) : bool` |
| `+` | `actualizar($nit, $razon_social, $direccion, $telefono, $correo, $logo) : bool` |

---

### `Factura`
**Archivo:** `models/Factura.php`  
**Tabla:** `facturas` + `detalle_factura`

| Tipo | Nombre |
|------|--------|
| **Atributos** | |
| `-` | `$conn : mysqli` |
| **Métodos** | |
| `+` | `__construct()` |
| `+` | `obtenerSiguienteNumero(int $id_empresa) : array` |
| `+` | `crearFactura(int $id_empresa, ?int $id_cliente, ?int $id_usuario, float $subtotal, float $total_iva, float $total_pagar, array $detalles) : array` |
| `+` | `obtenerFacturaPorId(int $id_factura) : ?array` |
| `+` | `obtenerDetallesFactura(int $id_factura) : array` |
| `+` | `contarVentasPorUsuario(int $id_usuario, ?string $fecha_inicio, ?string $fecha_fin) : int` |
| `+` | `totalVentasPorUsuario(int $id_usuario, ?string $fecha_inicio, ?string $fecha_fin) : float` |
| `+` | `obtenerVentasPorUsuario(int $id_usuario, ?string $fecha_inicio, ?string $fecha_fin, int $pagina, int $por_pagina) : array` |

---

### `Producto`
**Archivo:** `models/Productos.php`  
**Tabla:** `productos`

| Tipo | Nombre |
|------|--------|
| **Atributos** | |
| `-` | `$conn : mysqli` |
| **Métodos** | |
| `+` | `__construct()` |
| `+` | `obtenerTodos(int $limit, int $offset) : array` |
| `+` | `contarTodos() : int` |
| `+` | `buscar(string $termino, int $limit, int $offset) : array` |
| `+` | `contarBuscar(string $termino) : int` |
| `+` | `existeCodigo(string $codigo_barras, ?int $id_excluir) : bool` |
| `+` | `insertar(?string $codigo_barras, string $nombre_producto, float $precio_compra, float $precio_venta, int $stock_actual, float $tarifa_iva, string $estado_producto, ?int $id_categoria, ?string $imagen_url) : bool` |
| `+` | `actualizar(int $id_producto, ?string $codigo_barras, string $nombre_producto, float $precio_compra, float $precio_venta, int $stock_actual, float $tarifa_iva, string $estado_producto, ?int $id_categoria, ?string $imagen_url) : bool` |
| `+` | `alternarEstado(int $id_producto, string $nuevo_estado) : bool` |

---

### `Rentabilidad`
**Archivo:** `models/Rentabilidad.php`  
**Tablas:** `facturas` + `detalle_factura` (consultas analíticas)

| Tipo | Nombre |
|------|--------|
| **Atributos** | |
| `-` | `$conn : mysqli` |
| **Métodos** | |
| `+` | `__construct()` |
| `+` | `obtenerConsolidado(string $fecha_inicio, string $fecha_fin) : array` |
| `+` | `obtenerPorCategoria(string $fecha_inicio, string $fecha_fin) : array` |
| `+` | `obtenerHistorialGlobal(string $fecha_inicio, string $fecha_fin) : array` |
| `+` | `obtenerDetalleUtilidad(int $id_factura) : array` |

---

### `ResolucionDian`
**Archivo:** `models/ResolucionDian.php`  
**Tabla:** `resolucion_dian`

| Tipo | Nombre |
|------|--------|
| **Atributos** | |
| `-` | `$conn : mysqli` |
| **Métodos** | |
| `+` | `__construct()` |
| `+` | `obtenerTodos() : array` |
| `+` | `insertar($id_empresa, $numero_resolucion, $fecha_vigencia, $prefijo, $rango_inicial, $rango_final, $contador_actual, $estado) : bool` |
| `+` | `actualizar($id_empresa, $numero_resolucion, $fecha_vigencia, $prefijo, $rango_inicial, $rango_final, $contador_actual, $estado) : bool` |

---

### `Rol`
**Archivo:** `models/Rol.php`  
**Tabla:** `roles`

| Tipo | Nombre |
|------|--------|
| **Atributos** | |
| `-` | `$conn : mysqli` |
| **Métodos** | |
| `+` | `__construct()` |
| `+` | `obtenerTodos() : array` |

---

### `Usuario`
**Archivo:** `models/Usuario.php`  
**Tabla:** `usuarios`

| Tipo | Nombre |
|------|--------|
| **Atributos** | |
| `-` | `$conn : mysqli` |
| **Métodos** | |
| `+` | `__construct()` |
| `+` | `registrar(int $id_empresa, string $nombre, string $email, string $password, int $id_rol, string $estado, float $porcentaje_comision) : bool` |
| `+` | `emailExiste(string $email) : bool` |
| `+` | `obtenerPorEmail(string $email) : ?array` |
| `+` | `registrarIntentoFallido(int $id_usuario, int $intentos_actuales) : bool` |
| `+` | `guardarTokenRecuperacion(string $email, string $token, string $expiracion) : bool` |
| `+` | `obtenerPorToken(string $token) : ?array` |
| `+` | `actualizarPassword(int $id_usuario, string $nueva_password) : bool` |
| `+` | `resetearIntentosYActualizarAcceso(int $id_usuario) : void` |
| `+` | `obtenerTodos() : array` |
| `+` | `obtenerEstados() : array` |
| `+` | `actualizar(int $id_usuario, array $datos) : bool\|string` |
| `+` | `eliminar(int $id_usuario) : bool\|string` |

---

### `VentaBorrador`
**Archivo:** `models/VentaBorrador.php`  
**Tablas:** `ventas_borrador` + `detalle_borrador`

| Tipo | Nombre |
|------|--------|
| **Atributos** | |
| `-` | `$conn : mysqli` |
| **Métodos** | |
| `+` | `__construct()` |
| `+` | `obtenerBorrador(int $id_usuario) : ?array` |
| `+` | `guardarBorrador(int $id_usuario, ?int $id_cliente, array $detalles) : bool` |
| `+` | `limpiarBorrador(int $id_usuario) : bool` |

---

## 🎮 CONTROLADORES

---

### `AuthController`
**Archivo:** `controllers/auth/AuthController.php`

| Tipo | Nombre |
|------|--------|
| `+` | `login() : void` |
| `+` | `logout() : void` |
| `+` | `forgot_password() : void` |
| `+` | `reset_password() : void` |

---

### `CategoriaController`
**Archivo:** `controllers/CategoriaController.php`

| Tipo | Nombre |
|------|--------|
| `+` | `setAlert(string $icon, string $title, string $text) : void` |
| `+` | `index() : array` |
| `+` | `guardar() : void` |
| `+` | `cambiarEstado() : void` |
| `+` | `run() : void` |

---

### `ClientesController`
**Archivo:** `controllers/ClientesController.php`

| Tipo | Nombre |
|------|--------|
| `+` | `setAlert(string $icon, string $title, string $text) : void` |
| `+` | `index() : array` |
| `+` | `guardar() : void` |
| `+` | `eliminar() : void` |
| `+` | `run() : void` |

---

### `DashboardController`
**Archivo:** `controllers/DashboardController.php`

| Tipo | Nombre |
|------|--------|
| `+` | `index() : array` |

---

### `DeletedUsuarioController`
**Archivo:** `controllers/DeletedUsuarioController.php`

| Tipo | Nombre |
|------|--------|
| **Atributos** | |
| `-` | `$usuarioModel : Usuario` |
| **Métodos** | |
| `+` | `__construct()` |
| `+` | `delete() : void` |
| `+` | `setAlert(string $icon, string $title, string $text) : void` |

---

### `EmpresaController`
**Archivo:** `controllers/EmpresaController.php`

| Tipo | Nombre |
|------|--------|
| `+` | `setAlert(string $icon, string $title, string $text) : void` |
| `+` | `index() : array` |
| `+` | `guardar() : void` |
| `+` | `run() : void` |

---

### `FacturaController`
**Archivo:** `controllers/FacturaController.php`

| Tipo | Nombre |
|------|--------|
| `+` | `setAlert(string $icon, string $title, string $text) : void` |
| `+` | `init_pos() : void` |
| `+` | `procesar() : void` |
| `+` | `crear_cliente_ajax() : void` |
| `+` | `guardar_borrador() : void` |
| `+` | `limpiar_borrador() : void` |
| `+` | `imprimir() : void` |
| `+` | `run() : void` |

---

### `ParametrizacionController`
**Archivo:** `controllers/ParametrizacionController.php`

| Tipo | Nombre |
|------|--------|
| `+` | `setAlert(string $icon, string $title, string $text) : void` |
| `+` | `index() : array` |
| `+` | `guardar() : void` |
| `+` | `run() : void` |

---

### `ProductosController`
**Archivo:** `controllers/ProductosController.php`

| Tipo | Nombre |
|------|--------|
| `+` | `setAlert(string $icon, string $title, string $text) : void` |
| `+` | `index() : array` |
| `+` | `guardar() : void` |
| `+` | `alternarEstado() : void` |
| `+` | `run() : void` |

---

### `RentabilidadController`
**Archivo:** `controllers/RentabilidadController.php`

| Tipo | Nombre |
|------|--------|
| `+` | `__construct()` |
| `+` | `index() : array` |
| `+` | `getDetalleFacturaAjax() : void` |

---

## 🔗 RELACIONES ENTRE ENTIDADES

```
Usuario ──────┬── Factura (1:N)       → id_usuario
              └── VentaBorrador (1:1) → id_usuario (UNIQUE)

Empresa ──────┬── Usuario (1:N)       → id_empresa
              ├── Factura (1:N)       → id_empresa
              └── ResolucionDian (1:N) → id_empresa

Rol ──────────── Usuario (1:N)        → id_rol

Cliente ──────┬── Factura (1:N)       → id_cliente (NULL = Consumidor Final)
              └── VentaBorrador (0:1) → id_cliente

Categoria ────── Producto (1:N)       → id_categoria

Producto ─────┬── DetalleFactura (1:N)  → id_producto
              └── DetalleBorrador (1:N) → id_producto

Factura ──────── DetalleFactura (1:N) → id_factura (CASCADE)

ResolucionDian ── Factura (1:N)       → id_resolucion

VentaBorrador ── DetalleBorrador (1:N) → id_borrador (CASCADE)
```

---

> **Generado automáticamente** desde el código fuente de FactuWeb PRO.  
> Fecha: 2026-08-21
