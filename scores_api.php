<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'db.php';

$action = $_GET['action'] ?? '';

// ─── LEADERBOARD (общий) ──────────────────────────────────────────────────
if ($action === 'leaderboard') {
    $result = $conn->query("SELECT fullname, grade, total_score FROM users ORDER BY total_score DESC LIMIT 20");
    $rows = [];
    while ($row = $result->fetch_assoc()) $rows[] = $row;
    echo json_encode($rows); exit;
}

// ─── LEADERBOARD ПО КЛАССУ ────────────────────────────────────────────────
if ($action === 'leaderboard_class' && isset($_GET['grade'])) {
    $grade = $_GET['grade'];
    $stmt = $conn->prepare("SELECT fullname, grade, total_score FROM users WHERE grade=? ORDER BY total_score DESC LIMIT 20");
    $stmt->bind_param('s', $grade);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($row = $res->fetch_assoc()) $rows[] = $row;
    $stmt->close();
    echo json_encode($rows); exit;
}

// ─── USER DATA ────────────────────────────────────────────────────────────
if ($action === 'user_data' && isset($_GET['user'])) {
    $name = $_GET['user'];
    $stmt = $conn->prepare("SELECT id, total_score, grade FROM users WHERE fullname=?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$user) { echo json_encode(['error' => 'not_found']); exit; }

    $uid   = $user['id'];
    $score = $user['total_score'];
    $grade = $user['grade'];

    // Выполненные активности
    $stmt2 = $conn->prepare("SELECT activity_key, points FROM scores WHERE user_id=?");
    $stmt2->bind_param('i', $uid);
    $stmt2->execute();
    $res = $stmt2->get_result();
    $keys = [];
    while ($r = $res->fetch_assoc()) $keys[] = ['key' => $r['activity_key'], 'points' => $r['points']];
    $stmt2->close();

    // Общий рейтинг
    $rankQ = $conn->prepare("SELECT COUNT(*) as c FROM users WHERE total_score > ?");
    $rankQ->bind_param('i', $score);
    $rankQ->execute();
    $rank = $rankQ->get_result()->fetch_assoc()['c'] + 1;
    $rankQ->close();

    // Рейтинг по классу
    $rankCQ = $conn->prepare("SELECT COUNT(*) as c FROM users WHERE total_score > ? AND grade=?");
    $rankCQ->bind_param('is', $score, $grade);
    $rankCQ->execute();
    $rankClass = $rankCQ->get_result()->fetch_assoc()['c'] + 1;
    $rankCQ->close();

    echo json_encode([
        'total_score' => $score,
        'completed'   => $keys,
        'rank'        => $rank,
        'rank_class'  => $rankClass,
        'grade'       => $grade
    ]);
    exit;
}

// ─── SAVE SCORE ───────────────────────────────────────────────────────────
if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data   = json_decode(file_get_contents('php://input'), true);
    $name   = $data['user']         ?? '';
    $actKey = $data['activity_key'] ?? '';
    $points = intval($data['points'] ?? 0);

    if (!$name || !$actKey || $points <= 0) { echo json_encode(['error' => 'invalid']); exit; }

    $stmt = $conn->prepare("SELECT id FROM users WHERE fullname=?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $userRow = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$userRow) { echo json_encode(['error' => 'user_not_found']); exit; }

    $uid = $userRow['id'];
    $ins = $conn->prepare("INSERT IGNORE INTO scores (user_id, activity_key, points) VALUES (?,?,?)");
    $ins->bind_param('isi', $uid, $actKey, $points);
    $ins->execute();

    if ($conn->affected_rows > 0) {
        $upd = $conn->prepare("UPDATE users SET total_score = total_score + ? WHERE id = ?");
        $upd->bind_param('ii', $points, $uid);
        $upd->execute();
        echo json_encode(['success' => true, 'points_added' => $points]);
    } else {
        echo json_encode(['success' => false, 'message' => 'already_done']);
    }
    $ins->close();
    exit;
}

echo json_encode(['error' => 'unknown_action']);
$conn->close();
