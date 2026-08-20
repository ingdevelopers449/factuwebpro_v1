<?php
require_once __DIR__ . '/config/database.php';
global $conn;

echo "--- FACTURAS ---\n";
$res = $conn->query("DESCRIBE facturas");
if ($res) {
    while($row = $res->fetch_assoc()) echo $row['Field'] . "\n";
}

echo "--- DETALLE FACTURA ---\n";
$res = $conn->query("DESCRIBE detalle_factura");
if ($res) {
    while($row = $res->fetch_assoc()) echo $row['Field'] . "\n";
}
