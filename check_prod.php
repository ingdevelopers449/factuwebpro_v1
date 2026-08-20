<?php
require_once __DIR__ . '/config/database.php';
global $conn;

$res = $conn->query("DESCRIBE productos");
if ($res) {
    while($row = $res->fetch_assoc()) {
        echo $row['Field'] . "\n";
    }
}
