<?php
require_once '../../../app.php';
require_once '../../../services/GeminiService.php';

use Lib\Database;

header('Content-Type: application/json; charset=utf-8');

// ユーザー認証のチェック
$user_id = $_SESSION['user']['id'] ?? null;
if ($user_id === null) {
    echo json_encode(['status' => 'error', 'message' => 'ユーザーが認証されていません。']);
    exit;
}

// リクエストメソッドのチェック
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => '無効なリクエストメソッドです。']);
    exit;
}

// 1. データ取得（最新30件）
$pdo = Database::getInstance();
$sql = "SELECT * FROM health_records 
            WHERE user_id = :user_id
            ORDER BY recorded_at 
            ASC LIMIT 30";
// プリペアドステートメントを作成
$stmt = $pdo->prepare($sql);
// SQLを実行
$stmt->execute([':user_id' => $user_id]);
// 結果を取得
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. AI診断
try {
    $service = new GeminiService();
    $advice  = $service->chatHealth($data);

    if ($advice !== null) {
        $stmt = $pdo->prepare(
            'INSERT INTO ai_diagnosis_logs (user_id, diagnosis_type, result)
             VALUES (:user_id, :diagnosis_type, :result)'
        );
        $stmt->execute([
            ':user_id'        => $user_id,
            ':diagnosis_type' => 'health',
            ':result'         => $advice,
        ]);
    }

    // 3. レスポンス整形
    $output = [
        'status' => $advice !== null ? 'ok' : 'error',
        'advice' => $advice ?? '診断の取得に失敗しました。',
    ];
} catch (\Throwable $th) {
    $output = [
        'status' => 'error',
        'message' => $th->getMessage(),
        'advice' => '診断の取得に失敗しました。',
    ];
}

// JSONで出力
echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
