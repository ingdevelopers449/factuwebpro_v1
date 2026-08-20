<?php
require_once __DIR__ . '/config/database.php';
global $conn;

$sql_borrador = "CREATE TABLE IF NOT EXISTS ventas_borrador (
    id_borrador INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    id_cliente INT NULL,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE SET NULL
)";

$sql_detalle = "CREATE TABLE IF NOT EXISTS detalle_borrador (
    id_detalle_borrador INT AUTO_INCREMENT PRIMARY KEY,
    id_borrador INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    FOREIGN KEY (id_borrador) REFERENCES ventas_borrador(id_borrador) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE RESTRICT
)";

if ($conn->query($sql_borrador) === TRUE) {
    echo "Tabla ventas_borrador lista.\n";
} else {
    echo "Error creando ventas_borrador: " . $conn->error . "\n";
}

if ($conn->query($sql_detalle) === TRUE) {
    echo "Tabla detalle_borrador lista.\n";
} else {
    echo "Error creando detalle_borrador: " . $conn->error . "\n";
}
