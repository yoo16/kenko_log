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

// セッション(form)で入力値を保持
$_SESSION['form'] = $posts;

if (hasDuplicate($userId, $posts)) {
    // 重複があればエラーメッセージを表示
    $_SESSION['message'] = 'この日付の記録はすでに存在します。';
    header('Location: ' . BASE_URL . 'health/add.php');
    exit;
} else {
    insert($userId, $posts);
    header('Location: ' . BASE_URL . 'health/');
    exit;
}

function insert(int $userId, array $posts)
{
    // データベース接続
    $pdo = Database::getInstance();
    // SQLクエリ
    $sql = "INSERT INTO health_records (user_id, weight, heart_rate, systolic, diastolic, recorded_at) 
            VALUES (:user_id, :weight, :heart_rate, :systolic, :diastolic, :recorded_at)";
    // プリペアドステートメントを作成
    $stmt = $pdo->prepare($sql);
    // SQLを実行
    $stmt->execute([
        ':user_id' => $userId,
        ':weight' => $posts['weight'],
        ':heart_rate' => $posts['heart_rate'],
        ':systolic' => $posts['systolic'],
        ':diastolic' => $posts['diastolic'],
        ':recorded_at' => $posts['recorded_at'],
    ]);
}

function hasDuplicate(int $userId, array $posts)
{
    // データベース接続
    $pdo = Database::getInstance();
    // reported_at が重複しているか確認
    $sql = "SELECT id FROM health_records WHERE user_id = :user_id AND recorded_at = :recorded_at";
    // プリペアドステートメントを作成
    $stmt = $pdo->prepare($sql);
    // SQLを実行
    $stmt->execute([
        ':user_id' => $userId,
        ':recorded_at' => $posts['recorded_at'],
    ]);
    // 結果を取得
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // レコードが存在するか boolean を返す
    return (bool) $row;
}
