<?php
require_once __DIR__ . '/config/database.php';

$res = $conn->query("SELECT id_usuario, email, estado, intentos_fallidos, ultimo_acceso FROM usuarios");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
echo "PHP TIME: " . date('Y-m-d H:i:s') . "\n";
echo "MYSQL NOW(): ";
$res2 = $conn->query("SELECT NOW() as db_time");
$row2 = $res2->fetch_assoc();
echo $row2['db_time'] . "\n";

// Reset user to active so they can log in
$conn->query("UPDATE usuarios SET estado='activo', intentos_fallidos=0 WHERE email='admin@factuweb.com'");
echo "Usuario desbloqueado.\n";
