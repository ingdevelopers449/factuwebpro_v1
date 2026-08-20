<?php
require_once __DIR__ . '/config/database.php';
global $conn;

$res = $conn->query("DESCRIBE categorias");
if ($res) {
    echo "Categorias exists.\n";
} else {
    echo "Categorias does not exist.\n";
    
    // Create table
    $sql = "CREATE TABLE categorias (
        id_categoria INT AUTO_INCREMENT PRIMARY KEY,
        nombre_categoria VARCHAR(100) NOT NULL UNIQUE,
        descripcion TEXT,
        estado ENUM('activa', 'inactiva') DEFAULT 'activa',
        fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
        fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    if ($conn->query($sql)) {
        echo "Table categorias created.\n";
    } else {
        echo "Error: " . $conn->error . "\n";
    }

    $sql_alter = "ALTER TABLE productos ADD COLUMN id_categoria INT, ADD FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE SET NULL";
    if ($conn->query($sql_alter)) {
        echo "Table productos altered.\n";
    } else {
        echo "Error altering: " . $conn->error . "\n";
    }
}
