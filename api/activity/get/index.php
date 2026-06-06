<?php
require_once '../../../app.php';

use Lib\Database;

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'ユーザーが認証されていません。']);
    exit;
}

$userId = (int) $_SESSION['user']['id'];
$pdo    = Database::getInstance();

// 日別に集計（最新30日分、カロリーが記録されている日のみ）
$stmt = $pdo->prepare(
    'SELECT
        exercise_date,
        SUM(calories_burned)  AS total_calories,
        SUM(duration_minutes) AS total_duration,
        COUNT(*)              AS record_count
     FROM exercise_records
     WHERE user_id = :user_id
       AND calories_burned IS NOT NULL
     GROUP BY exercise_date
     ORDER BY exercise_date ASC
     LIMIT 30'
);
$stmt->execute([':user_id' => $userId]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
