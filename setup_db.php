<?php
// setup_db.php — запусти один раз чтобы создать таблицы
// После запуска удали этот файл с сервера!
require_once 'db.php';

$queries = [
"CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL UNIQUE,
    grade varchar(10) NOT NULL,
    password VARCHAR(255) NOT NULL,
    total_score INT DEFAULT 0,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

"CREATE TABLE IF NOT EXISTS scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_key VARCHAR(100) NOT NULL,
    points INT NOT NULL,
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_activity (user_id, activity_key),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

"CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
];

foreach ($queries as $sql) {
    if ($conn->query($sql)) echo "✅ OK: " . substr($sql, 0, 60) . "...<br>";
    else echo "❌ ERROR: " . $conn->error . "<br>";
}
echo "<br><b>Done! Now delete setup_db.php from your server.</b>";
$conn->close();
