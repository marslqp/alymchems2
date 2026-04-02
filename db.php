<?php
// db.php — единое подключение к базе данных
// Railway автоматически задаёт переменные окружения
$host = getenv('MYSQLHOST')     ?: getenv('DB_HOST') ?: 'localhost';
$port = getenv('MYSQLPORT')     ?: getenv('DB_PORT') ?: '3306';
$user = getenv('MYSQLUSER')     ?: getenv('DB_USER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: getenv('DB_PASS') ?: '';
$db   = getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'alymchems_db';

$conn = new mysqli($host, $user, $pass, $db, (int)$port);
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'DB connection failed: ' . $conn->connect_error]));
}
$conn->set_charset('utf8mb4');
