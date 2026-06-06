<?php
// 共通処理を読み込む
require_once '../app.php';

use Lib\Database;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

// POSTデータの取得
$posts = $_POST;
$userId = (int) $_SESSION['user']['id'];

if (hasDuplicate($userId, $posts['id'], $posts['recorded_at'])) {
    // 重複があればエラーメッセージを表示
    $_SESSION['message'] = 'この日付の記録はすでに存在します。';
    header('Location: ' . BASE_URL . "health/edit.php?id={$posts['id']}");
    exit;
} else {
    // 重複がなければデータを更新
    update($userId, $posts['id'], $posts);
    header('Location: ' . BASE_URL . 'health/');
    exit;
}

// データ更新
function update(int $userId, $id, array $data)
{
    // データベース接続
    $pdo = Database::getInstance();
    // SQLクエリ
    $sql = "UPDATE health_records 
            SET 
                weight = :weight, 
                heart_rate = :heart_rate, 
                systolic = :systolic, 
                diastolic = :diastolic, 
                recorded_at = :recorded_at 
            WHERE id = :id AND user_id = :user_id";
    // プリペアドステートメントを作成
    $stmt = $pdo->prepare($sql);
    // SQLを実行
    $stmt->execute([
        ':weight' => $data['weight'],
        ':heart_rate' => $data['heart_rate'],
        ':systolic' => $data['systolic'],
        ':diastolic' => $data['diastolic'],
        ':recorded_at' => $data['recorded_at'],
        ':id' => $id,
        ':user_id' => $userId,
    ]);
}

// 重複チェック
function hasDuplicate(int $userId, $id, $recorded_at)
{
    // データベース接続
    $pdo = Database::getInstance();
    // TODO: reported_at が重複しているか確認 ただし、現在のレコードは除外する
    $sql = "SELECT id 
        FROM health_records 
        WHERE user_id = :user_id AND recorded_at = :recorded_at AND id != :id";
    // プリペアドステートメントを作成
    $stmt = $pdo->prepare($sql);

    // TODO: SQLを実行
    $stmt->execute([
        ':user_id' => $userId,
        ':recorded_at' => $recorded_at,
        ':id' => $id,
    ]);
    // 結果を取得
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // レコードが存在するか boolean を返す
    return (bool) $row;
}
