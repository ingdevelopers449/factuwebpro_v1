<?php
require_once __DIR__ . '/config/database.php';
global $conn;

$res = $conn->query("DESCRIBE productos");
if ($res) {
    echo "SCHEMA productos:\n";
    while ($row = $res->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "Table 'productos' not found.\n";
}
