<?php
require_once '../app.php';

use Lib\Database;

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'sleep/add.php');
    exit;
}

$posts = normalizeSleepPosts($_POST);
$_SESSION['sleep_form'] = $posts;

$message = validateSleepPosts($posts);
if ($message !== '') {
    $_SESSION['sleep_message'] = $message;
    header('Location: ' . BASE_URL . 'sleep/add.php');
    exit;
}

insertSleep((int) $_SESSION['user']['id'], $posts);
unset($_SESSION['sleep_form']);

header('Location: ' . BASE_URL . 'sleep/');
exit;

function normalizeSleepPosts(array $posts): array
{
    return [
        'sleep_date'    => trim($posts['sleep_date'] ?? ''),
        'bedtime'       => trim($posts['bedtime'] ?? ''),
        'wake_time'     => trim($posts['wake_time'] ?? ''),
        'sleep_quality' => trim($posts['sleep_quality'] ?? ''),
        'memo'          => trim($posts['memo'] ?? ''),
    ];
}

function validateSleepPosts(array $posts): string
{
    if ($posts['sleep_date'] === '') {
        return '睡眠日を入力してください。';
    }
    if ($posts['bedtime'] === '') {
        return '就寝時刻を入力してください。';
    }
    if ($posts['wake_time'] === '') {
        return '起床時刻を入力してください。';
    }

    $bedtime  = strtotime($posts['bedtime']);
    $wakeTime = strtotime($posts['wake_time']);
    if ($wakeTime <= $bedtime) {
        return '起床時刻は就寝時刻より後に設定してください。';
    }

    if ($posts['sleep_quality'] !== '' && !in_array((int) $posts['sleep_quality'], range(1, 5), true)) {
        return '睡眠の質は1〜5の範囲で選択してください。';
    }

    return '';
}

function calcDurationMinutes(string $bedtime, string $wakeTime): int
{
    return (int) round((strtotime($wakeTime) - strtotime($bedtime)) / 60);
}

function insertSleep(int $userId, array $posts): void
{
    $pdo = Database::getInstance();
    $sql = 'INSERT INTO sleep_records (
                user_id,
                sleep_date,
                bedtime,
                wake_time,
                sleep_duration_minutes,
                sleep_quality,
                memo
            ) VALUES (
                :user_id,
                :sleep_date,
                :bedtime,
                :wake_time,
                :sleep_duration_minutes,
                :sleep_quality,
                :memo
            )';
    // プリペアドステートメント
    $stmt = $pdo->prepare($sql);
    // datetime-local の値（Y-m-dTH:i）を MySQL DATETIME 形式に変換
    $bedtime  = str_replace('T', ' ', $posts['bedtime']) . ':00';
    $wakeTime = str_replace('T', ' ', $posts['wake_time']) . ':00';

    // SQLの実行
    $stmt->execute([
        ':user_id'               => $userId,
        ':sleep_date'            => $posts['sleep_date'],
        ':bedtime'               => $bedtime,
        ':wake_time'             => $wakeTime,
        ':sleep_duration_minutes' => calcDurationMinutes($posts['bedtime'], $posts['wake_time']),
        ':sleep_quality'         => $posts['sleep_quality'] === '' ? null : (int) $posts['sleep_quality'],
        ':memo'                  => $posts['memo'] === '' ? null : $posts['memo'],
    ]);
}
