<?php
require_once '../../../app.php';

use Lib\Database;

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'ユーザーが認証されていません。']);
    exit;
}

// リクエストメソッドのチェック
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => '無効なリクエストメソッドです。']);
    exit;
}

// ユーザーIDを取得
$userId = (int) $_SESSION['user']['id'];

// データベース接続
$pdo    = Database::getInstance();
// 日別に集計（最新30日分、カロリーが記録されている日のみ）
$sql = 'SELECT
            exercise_date,
            SUM(calories_burned)  AS total_calories,
            SUM(duration_minutes) AS total_duration,
            COUNT(*)              AS record_count
        FROM exercise_records
        WHERE user_id = :user_id AND calories_burned IS NOT NULL
        GROUP BY exercise_date
        ORDER BY exercise_date ASC
        LIMIT 30';
// プリペアドステートメントを作成
$stmt = $pdo->prepare($sql);
// SQLを実行
$stmt->execute([':user_id' => $userId]);
// 結果を取得
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);   
// JSON形式で結果を返す
echo json_encode($results, JSON_UNESCAPED_UNICODE);
