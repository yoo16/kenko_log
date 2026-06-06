<?php
require_once '../app.php';

use Lib\Database;

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'sleep/');
    exit;
}

$id    = (int) ($_POST['id'] ?? 0);
$posts = normalizeSleepPosts($_POST);
$_SESSION['sleep_form'] = $posts;

$message = validateSleepPosts($posts);
if ($id < 1) {
    $message = '更新対象の記録が見つかりません。';
}

if ($message !== '') {
    $_SESSION['sleep_message'] = $message;
    header('Location: ' . BASE_URL . "sleep/edit.php?id={$id}");
    exit;
}

updateSleep($id, (int) $_SESSION['user']['id'], $posts);
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

function updateSleep(int $id, int $userId, array $posts): void
{
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare(
        'UPDATE sleep_records
         SET
            sleep_date             = :sleep_date,
            bedtime                = :bedtime,
            wake_time              = :wake_time,
            sleep_duration_minutes = :sleep_duration_minutes,
            sleep_quality          = :sleep_quality,
            memo                   = :memo
         WHERE id = :id AND user_id = :user_id'
    );

    $bedtime  = str_replace('T', ' ', $posts['bedtime']) . ':00';
    $wakeTime = str_replace('T', ' ', $posts['wake_time']) . ':00';

    $stmt->execute([
        ':sleep_date'            => $posts['sleep_date'],
        ':bedtime'               => $bedtime,
        ':wake_time'             => $wakeTime,
        ':sleep_duration_minutes' => calcDurationMinutes($posts['bedtime'], $posts['wake_time']),
        ':sleep_quality'         => $posts['sleep_quality'] === '' ? null : (int) $posts['sleep_quality'],
        ':memo'                  => $posts['memo'] === '' ? null : $posts['memo'],
        ':id'                    => $id,
        ':user_id'               => $userId,
    ]);
}
