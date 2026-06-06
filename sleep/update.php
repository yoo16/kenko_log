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
$posts = $_POST;
// 入力値をセッションに保存（エラーがあった場合にフォームに再表示するため）
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

// 更新処理
updateSleep($id, (int) $_SESSION['user']['id'], $posts);
// 更新成功後はセッションの入力値をクリア
unset($_SESSION['sleep_form']);

header('Location: ' . BASE_URL . 'sleep/');
exit;

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
    // 就寝時刻と起床時刻の論理チェック
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
    $sql = 'UPDATE sleep_records
            SET
                sleep_date             = :sleep_date,
                bedtime                = :bedtime,
                wake_time              = :wake_time,
                sleep_duration_minutes = :sleep_duration_minutes,
                sleep_quality          = :sleep_quality,
                memo                   = :memo
            WHERE id = :id AND user_id = :user_id';
    // プリペアドステートメント
    $stmt = $pdo->prepare($sql);
    // 就寝時刻と起床時刻をSQLのDATETIME形式に変換
    $bedtime  = str_replace('T', ' ', $posts['bedtime']) . ':00';
    $wakeTime = str_replace('T', ' ', $posts['wake_time']) . ':00';
    // SQLの実行
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
