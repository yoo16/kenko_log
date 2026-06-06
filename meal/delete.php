<?php
require_once '../app.php';

use Lib\Database;

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'meal/');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $pdo = Database::getInstance();
    $sql = 'DELETE FROM meal_records WHERE id = :id AND user_id = :user_id';
    // プリペアドステートメントを作成
    $stmt = $pdo->prepare($sql);
    // バインドするデータを準備
    $data = [
        ':id' => $id,
        ':user_id' => (int) $_SESSION['user']['id'],
    ];
    // SQLを実行
    $stmt->execute($data);
}

header('Location: ' . BASE_URL . 'meal/');
exit;
