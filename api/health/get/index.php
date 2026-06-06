<?php
require_once '../../../app.php';

use Lib\Database;

if (empty($_SESSION['user'])) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

// データベース接続
$pdo = Database::getInstance();
// health_records から最新30件取得
$sql = "SELECT * FROM health_records WHERE user_id = :user_id ORDER BY recorded_at ASC LIMIT 30";
// プリペアドステートメントを作成
$stmt = $pdo->prepare($sql);
// SQLを実行
$stmt->execute([':user_id' => (int) $_SESSION['user']['id']]);
// 結果を取得
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// レスポンスヘッダーを設定
header('Content-Type: application/json');
// JSON形式で出力
echo json_encode($data);
