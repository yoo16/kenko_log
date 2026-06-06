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
$posts = normalizeActivityPosts($_POST);
$_SESSION['activity_form'] = $posts;

$message = validateActivityPosts($posts);
if ($id < 1) {
    $message = '更新対象の記録が見つかりません。';
}

if ($message !== '') {
    $_SESSION['activity_message'] = $message;
    header('Location: ' . BASE_URL . "activity/edit.php?id={$id}");
    exit;
}

updateActivity($id, (int) $_SESSION['user']['id'], $posts);
unset($_SESSION['activity_form']);

header('Location: ' . BASE_URL . 'activity/');
exit;

function normalizeActivityPosts(array $posts): array
{
    return [
        'exercise_date' => trim($posts['exercise_date'] ?? ''),
        'exercise_type' => trim($posts['exercise_type'] ?? ''),
        'duration_minutes' => trim($posts['duration_minutes'] ?? ''),
        'calories_burned' => trim($posts['calories_burned'] ?? ''),
        'distance_km' => trim($posts['distance_km'] ?? ''),
        'memo' => trim($posts['memo'] ?? ''),
    ];
}

function validateActivityPosts(array $posts): string
{
    if ($posts['exercise_date'] === '') {
        return '運動日を入力してください。';
    }
    if ($posts['exercise_type'] === '') {
        return '運動の種類を入力してください。';
    }
    if ($posts['duration_minutes'] === '' || (int) $posts['duration_minutes'] < 1) {
        return '運動時間は1分以上で入力してください。';
    }

    return '';
}

function updateActivity(int $id, int $userId, array $posts): void
{
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare(
        'UPDATE exercise_records
         SET
            exercise_date = :exercise_date,
            exercise_type = :exercise_type,
            duration_minutes = :duration_minutes,
            calories_burned = :calories_burned,
            distance_km = :distance_km,
            memo = :memo
         WHERE id = :id AND user_id = :user_id'
    );

    $stmt->execute([
        ':exercise_date' => $posts['exercise_date'],
        ':exercise_type' => $posts['exercise_type'],
        ':duration_minutes' => (int) $posts['duration_minutes'],
        ':calories_burned' => $posts['calories_burned'] === '' ? null : (int) $posts['calories_burned'],
        ':distance_km' => $posts['distance_km'] === '' ? null : $posts['distance_km'],
        ':memo' => $posts['memo'] === '' ? null : $posts['memo'],
        ':id' => $id,
        ':user_id' => $userId,
    ]);
}
