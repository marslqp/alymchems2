<?php
$host = getenv('MYSQLHOST');
$port = getenv('MYSQLPORT');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');

$conn = new mysqli($host, $user, $pass, $db, (int)$port);
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'DB connection failed: ' . $conn->connect_error]));
}
$conn->set_charset('utf8mb4');
?>
