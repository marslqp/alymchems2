<?php
// Railway может использовать разные названия переменных
// Пробуем все варианты
$host = getenv('MYSQL_HOST')     ?: getenv('MYSQLHOST')     ?: getenv('DB_HOST')     ?: '';
$port = getenv('MYSQL_PORT')     ?: getenv('MYSQLPORT')     ?: getenv('DB_PORT')     ?: '3306';
$user = getenv('MYSQL_USER')     ?: getenv('MYSQLUSER')     ?: getenv('DB_USER')     ?: '';
$pass = getenv('MYSQL_PASSWORD') ?: getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD') ?: '';
$db   = getenv('MYSQL_DATABASE') ?: getenv('MYSQLDATABASE') ?: getenv('DB_NAME')     ?: '';

// Отладка — покажет что реально получает PHP (удали после настройки!)
if (!$host) {
    $all = [];
    foreach ($_ENV as $k => $v) {
        if (stripos($k, 'mysql') !== false || stripos($k, 'db') !== false) {
            $all[] = $k . '=' . substr($v, 0, 5) . '...';
        }
    }
    die(json_encode(['error' => 'DB env vars missing', 'found' => $all, 'hint' => 'Check Railway Variables tab']));
}

$conn = new mysqli($host, $user, $pass, $db, (int)$port);
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'DB connection failed: ' . $conn->connect_error]));
}
$conn->set_charset('utf8mb4');
?>
