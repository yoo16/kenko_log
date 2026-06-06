<?php
require_once '../../../app.php';
require_once '../../../services/GeminiService.php';

header('Content-Type: application/json; charset=utf-8');

// ユーザー認証のチェック
if (empty($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'ユーザーが認証されていません。']);
    exit;
}

// リクエストメソッドのチェック
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => '無効なリクエストメソッドです。']);
    exit;
}

// リクエストボディから食品名を取得
$body = json_decode(file_get_contents('php://input'), true);
$foodName = trim($body['food_name'] ?? '');

if ($foodName === '') {
    echo json_encode(['status' => 'error', 'message' => '食品名を入力してください。']);
    exit;
}

try {
    $service = new GeminiService();
    // AIに栄養成分の推定を依頼
    $result  = $service->chatMeal($foodName);

    if ($result === null) {
        echo json_encode(['status' => 'error', 'message' => '栄養成分の取得に失敗しました。']);
        exit;
    }

    // JSONで出力
    echo json_encode(array_merge(['status' => 'ok'], $result), JSON_UNESCAPED_UNICODE);
} catch (\Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => '通信エラーが発生しました。']);
}