<?php
require_once '../../../app.php';
require_once '../../../services/GeminiService.php';

use Lib\Database;

header('Content-Type: application/json; charset=utf-8');

// ユーザー認証
$user_id = $_SESSION['user']['id'] ?? null;
if ($user_id === null) {
    echo json_encode(['status' => 'error', 'message' => 'ユーザーが認証されていません。']);
    exit;
}

// 1. データ取得（最新30件）
$pdo = Database::getInstance();
$sql = "SELECT * FROM health_records 
            WHERE user_id = :user_id
            ORDER BY recorded_at 
            ASC LIMIT 30";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. AI診断: データをGeminiServiceに渡して診断結果を取得
$service = new GeminiService();
$result = $service->chatHealth($data);

// テスト用のダミーデータ
// $result['status'] = 'ok';
// $result['advice'] = "AI診断結果の例: 体重は適正範囲内ですが、血圧がやや高めです。食事に注意し、定期的な運動をおすすめします。";

// 3. 結果をDBに保存
if (isset($result['advice'])) {
    // 成功の場合は診断結果をDBに保存
    try {
        $insertSql = "INSERT INTO ai_diagnosis_logs (user_id, diagnosis_type, result) 
                        VALUES (:user_id, :diagnosis_type, :result)";
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->execute([
            ':user_id' => $user_id,
            ':diagnosis_type' => 'health',
            ':result' => $result['advice']
        ]);
    } catch (PDOException $error) {
        $result['status'] = 'error';
        $result['advice'] = "";
        $result['message'] = $error->getMessage();
    }
}

// 4. 結果をJSON形式で返す
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
