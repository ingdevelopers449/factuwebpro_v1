CREATE DATABASE IF NOT EXISTS factuweb_pro;
USE factuweb_pro;

-- =========================================================================
-- TABLAS DE CONFIGURACIÓN
-- =========================================================================

CREATE TABLE empresa (
    id_empresa INT AUTO_INCREMENT PRIMARY KEY,
    nit VARCHAR(20) NOT NULL UNIQUE,
    razon_social VARCHAR(150) NOT NULL,
    direccion VARCHAR(255),
    telefono VARCHAR(20),
    email VARCHAR(150),
    logo_url VARCHAR(255)
);

CREATE TABLE resolucion_dian (
    id_resolucion INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    numero_resolucion VARCHAR(50) NOT NULL UNIQUE,
    fecha_vigencia DATE NOT NULL,
    prefijo VARCHAR(10) NOT NULL,
    rango_inicial INT NOT NULL,
    rango_final INT NOT NULL,
    contador_actual INT NOT NULL,
    estado ENUM('activa', 'inactiva') DEFAULT 'activa',

    FOREIGN KEY (id_empresa)
        REFERENCES empresa(id_empresa)
        ON DELETE RESTRICT
);

-- =========================================================================
-- TABLAS PRINCIPALES
-- =========================================================================

CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    id_rol INT,
    estado ENUM('activo', 'bloqueado', 'inactivo') DEFAULT 'activo',
    intentos_fallidos INT DEFAULT 0,
    ultimo_acceso DATETIME,
    token_recuperacion VARCHAR(255) NULL,
    porcentaje_comision DECIMAL(5,2) DEFAULT 0.00,

    FOREIGN KEY (id_empresa)
        REFERENCES empresa(id_empresa)
        ON DELETE RESTRICT,

    FOREIGN KEY (id_rol)
        REFERENCES roles(id_rol)
        ON DELETE RESTRICT
);

CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    identificacion VARCHAR(20) NOT NULL UNIQUE,
    nombre_razon_social VARCHAR(150) NOT NULL,
    email VARCHAR(150),
    direccion VARCHAR(255),
    telefono VARCHAR(20)
);

-- =========================================================================
-- 🆕 TABLA DE CATEGORÍAS (NUEVA)
-- =========================================================================

CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    estado ENUM('activa', 'inactiva') DEFAULT 'activa',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================================================================
-- TABLA PRODUCTOS (ACTUALIZADA CON CATEGORÍA)
-- =========================================================================

CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT,  -- 🆕 NUEVA COLUMNA
    codigo_barras VARCHAR(50) UNIQUE,
    nombre_producto VARCHAR(150) NOT NULL,
    precio_compra DECIMAL(12,2) NOT NULL,
    precio_venta DECIMAL(12,2) NOT NULL,
    stock_actual INT NOT NULL DEFAULT 0,
    estado_producto ENUM('activo', 'inactivo') DEFAULT 'activo',
    tarifa_iva DECIMAL(5,2) DEFAULT 19.00,
    imagen_url VARCHAR(255) NULL,  -- 🆕 NUEVA COLUMNA: ruta/URL de la imagen del producto (no se guarda el archivo en la BD)

    -- 🆕 NUEVA LLAVE FORÁNEA
    FOREIGN KEY (id_categoria)
        REFERENCES categorias(id_categoria)
        ON DELETE SET NULL  -- Si se elimina la categoría, el producto queda sin categoría
);

CREATE TABLE facturas (
    id_factura INT AUTO_INCREMENT PRIMARY KEY,

    id_empresa INT NOT NULL,

    id_resolucion INT NOT NULL,
    prefijo_resolucion VARCHAR(10) NOT NULL,
    consecutivo INT NOT NULL,
    fecha_emision DATETIME DEFAULT CURRENT_TIMESTAMP,

    id_cliente INT,
    id_usuario INT,

    subtotal DECIMAL(12,2) NOT NULL,
    total_iva DECIMAL(12,2) NOT NULL,
    total_pagar DECIMAL(12,2) NOT NULL,

    -- 🆕 Congela el % de comisión vigente al momento de la venta, para que
    -- un cambio posterior en usuarios.porcentaje_comision no altere el
    -- historial de comisiones ya calculado.
    porcentaje_comision_aplicado DECIMAL(5,2) NULL,

    -- 🆕 VALIDEZ LEGAL ANTE LA DIAN (SIMULADA — alcance académico del prototipo)
    -- Estos campos se llenan con datos generados internamente por el propio
    -- sistema (ej. un hash/UUID como CUFE). NO hay integración real con un
    -- proveedor tecnológico autorizado por la DIAN; eso queda fuera del
    -- alcance de este prototipo y se documenta como trabajo futuro.
    cufe VARCHAR(100) UNIQUE NULL,              -- CUFE simulado, generado internamente
    codigo_qr TEXT NULL,                        -- contenido del código QR simulado
    estado_dian ENUM('pendiente', 'aceptada', 'rechazada') DEFAULT 'pendiente',
    motivo_rechazo VARCHAR(255) NULL,           -- motivo de ejemplo si estado_dian = 'rechazada'
    fecha_validacion_dian DATETIME NULL,

    UNIQUE (prefijo_resolucion, consecutivo),

    FOREIGN KEY (id_empresa)
        REFERENCES empresa(id_empresa)
        ON DELETE RESTRICT,

    FOREIGN KEY (id_resolucion)
        REFERENCES resolucion_dian(id_resolucion)
        ON DELETE RESTRICT,

    FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente)
        ON DELETE RESTRICT,

    FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON DELETE RESTRICT
);


CREATE TABLE detalle_factura (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_factura INT,
    id_producto INT,
    cantidad INT NOT NULL,
    precio_unitario_venta DECIMAL(12,2) NOT NULL,
    precio_unitario_costo DECIMAL(12,2) NOT NULL,
    subtotal_linea DECIMAL(12,2) NOT NULL,

    FOREIGN KEY (id_factura)
        REFERENCES facturas(id_factura)
        ON DELETE CASCADE,

    FOREIGN KEY (id_producto)
        REFERENCES productos(id_producto)
        ON DELETE RESTRICT
);

-- =========================================================================
-- 🆕 PERSISTENCIA DE VENTA EN CURSO (BORRADOR)
-- =========================================================================
-- Guarda la venta que un usuario tiene sin finalizar, para que sobreviva
-- incluso si cierra sesión o el equipo se apaga. Un usuario solo puede
-- tener un borrador activo a la vez (UNIQUE en id_usuario).

CREATE TABLE ventas_borrador (
    id_borrador INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    id_cliente INT NULL,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE,

    FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente)
        ON DELETE SET NULL
);

CREATE TABLE detalle_borrador (
    id_detalle_borrador INT AUTO_INCREMENT PRIMARY KEY,
    id_borrador INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,

    FOREIGN KEY (id_borrador)
        REFERENCES ventas_borrador(id_borrador)
        ON DELETE CASCADE,

    FOREIGN KEY (id_producto)
        REFERENCES productos(id_producto)
        ON DELETE RESTRICT
);

-- =========================================================================
-- DATOS INICIALES (SEED)
-- =========================================================================

-- Roles del sistema
INSERT INTO roles (id_rol, nombre_rol) VALUES
    (1, 'Administrador'),
    (2, 'Empleado');

-- Empresa por defecto
INSERT INTO empresa (id_empresa, nit, razon_social, direccion, telefono, email) VALUES
    (1, '000000000', 'Mi Empresa S.A.S.', 'Dirección por configurar', '0000000', 'empresa@factuweb.com');

-- Usuario Administrador por defecto
-- Correo: admin@factuweb.com | Contraseña: admin123
INSERT INTO usuarios (id_empresa, nombre, email, password_hash, id_rol, estado, porcentaje_comision) VALUES
    (1, 'Administrador Principal', 'admin@factuweb.com', '$2y$10$Inhgt2tNghhbEh4O3xJKP.6Qzv4XUZhiBcvsO1M0sdiYrTDKiYD7q', 1, 'activo', 0.00);