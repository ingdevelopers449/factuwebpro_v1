<?php
require_once __DIR__ . '/config/database.php';
global $conn;

$tables = ['facturas', 'detalle_factura', 'factura_detalle'];
foreach ($tables as $table) {
    $res = $conn->query("DESCRIBE $table");
    if ($res) {
        echo "SCHEMA $table:\n";
        while ($row = $res->fetch_assoc()) {
            print_r($row);
        }
    }
}
