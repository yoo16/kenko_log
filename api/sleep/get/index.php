<?php
require_once '../../../app.php';

use Lib\Database;

// ユーザー認証のチェック
if (empty($_SESSION['user'])) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

$pdo = Database::getInstance();
$sql = "SELECT sleep_date, sleep_duration_minutes, sleep_quality
        FROM sleep_records
        WHERE user_id = :user_id
        ORDER BY sleep_date ASC
        LIMIT 30";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => (int) $_SESSION['user']['id']]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($data);
