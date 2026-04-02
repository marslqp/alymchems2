<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'POST only']); exit;
}

$input = json_decode(file_get_contents('php://input'), true);
// поддержка и JSON и обычной формы
$name  = trim($input['fullname'] ?? $_POST['fullname'] ?? '');
$grade = intval($input['grade']  ?? $_POST['grade']    ?? 0);
$raw   = $input['password']      ?? $_POST['password'] ?? '';

if (!$name || !$grade || !$raw) {
    echo json_encode(['error' => 'missing_fields']); exit;
}
if (strlen($raw) < 6) {
    echo json_encode(['error' => 'password_short']); exit;
}

// Проверяем уникальность имени
$check = $conn->prepare("SELECT id FROM users WHERE LOWER(fullname)=LOWER(?)");
$check->bind_param('s', $name);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo json_encode(['error' => 'name_taken']); exit;
}
$check->close();

$hash = password_hash($raw, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (fullname, grade, password) VALUES (?,?,?)");
$stmt->bind_param('sis', $name, $grade, $hash);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'name' => $name, 'grade' => $grade]);
} else {
    echo json_encode(['error' => 'db_error', 'msg' => $conn->error]);
}
$stmt->close();
$conn->close();
