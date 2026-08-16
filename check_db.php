<?php
require_once __DIR__ . '/config/database.php';
global $conn;

// Schema
$res = $conn->query("DESCRIBE resolucion_dian");
echo "SCHEMA:\n";
while ($row = $res->fetch_assoc()) {
    print_r($row);
}

// Data
echo "\nDATA:\n";
$res2 = $conn->query("SELECT * FROM resolucion_dian");
while ($row = $res2->fetch_assoc()) {
    print_r($row);
}
