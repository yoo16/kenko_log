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

$id = (int) ($_POST['id'] ?? 0);
$userId = (int) $_SESSION['user']['id'];

if (!$id) {
    header('Location: ' . BASE_URL . 'activity/');
    exit;
}

$pdo  = Database::getInstance();
$sql = 'SELECT exercise_type, duration_minutes, calories_burned, distance_km, memo
            FROM exercise_records
            WHERE id = :id AND user_id = :user_id
            LIMIT 1';
// プリペアドステートメントの準備
$stmt = $pdo->prepare($sql);
// SQLの実行
$stmt->execute([':id' => $id, ':user_id' => $userId]);
// 結果の取得
$source = $stmt->fetch(PDO::FETCH_ASSOC);

if ($source) {
    $ins = $pdo->prepare(
        'INSERT INTO exercise_records
                (user_id, exercise_date, exercise_type, duration_minutes, calories_burned, distance_km, memo)
                VALUES
                (:user_id, :exercise_date, :exercise_type, :duration_minutes, :calories_burned, :distance_km, :memo)'
    );
    // SQLの実行
    $ins->execute([
        ':user_id'          => $userId,
        ':exercise_date'    => date('Y-m-d'),
        ':exercise_type'    => $source['exercise_type'],
        ':duration_minutes' => $source['duration_minutes'],
        ':calories_burned'  => $source['calories_burned'],
        ':distance_km'      => $source['distance_km'],
        ':memo'             => $source['memo'],
    ]);
}

header('Location: ' . BASE_URL . 'activity/');
exit;
