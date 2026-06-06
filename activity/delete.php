<?php
require_once '../app.php';

use Lib\Database;

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'activity/');
    exit;
}

// IDを取得して整数に変換
$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $pdo = Database::getInstance();
    $sql = 'DELETE FROM exercise_records WHERE id = :id AND user_id = :user_id';
    // プリペアドステートメント
    $stmt = $pdo->prepare($sql);
    // SQLの実行
    $stmt->execute([
        ':id' => $id,
        ':user_id' => (int) $_SESSION['user']['id'],
    ]);
}

header('Location: ' . BASE_URL . 'activity/');
exit;
