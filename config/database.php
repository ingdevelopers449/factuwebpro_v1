<?php
$host = "127.0.0.1";
$user = "root";
$password = "";
$bd = "factuweb_pro";
$port = 3306;

try {
    $conn = new mysqli($host, $user, $password, $bd, $port);
} catch (mysqli_sql_exception $e) {
    die("Error de Conexion: " . $e->getMessage());
}
?>