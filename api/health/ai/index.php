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

// 2. AI診断
$service = new GeminiService();
$result = $service->chatHealth($data);

// 3. 結果をJSON形式で返す
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);