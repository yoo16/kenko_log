<?php
// 共通処理を読み込む
require_once '../app.php';

use Lib\Database;

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

// POSTリクエストでない場合は終了
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

// POSTリクエストからIDを取得
$id = $_POST['id'] ?? 0;

if ($id > 0) {
    // データベース接続
    $pdo = Database::getInstance();
    // SQLクエリ
    $sql = "DELETE FROM health_records WHERE id = :id AND user_id = :user_id";
    // プリペアドステートメントを作成
    $stmt = $pdo->prepare($sql);
    // SQLを実行
    $stmt->execute([
        ':id' => $id,
        ':user_id' => (int) $_SESSION['user']['id'],
    ]);
}

// 削除後は履歴ページにリダイレクト
header('Location: ' . BASE_URL . 'health/');
exit;
