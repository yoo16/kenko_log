<?php
require_once '../app.php';

use Lib\Database;

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

$user = $_SESSION['user'];
$dashboard = getDashboardData((int) $user['id']);

function getDashboardData(int $userId): array
{
    $pdo = Database::getInstance();

    return [
        'latest_health' => fetchOne(
            $pdo,
            'SELECT * FROM health_records WHERE user_id = :user_id ORDER BY recorded_at DESC LIMIT 1',
            [':user_id' => $userId]
        ),
        'latest_sleep' => fetchOne(
            $pdo,
            'SELECT * FROM sleep_records WHERE user_id = :user_id ORDER BY sleep_date DESC LIMIT 1',
            [':user_id' => $userId]
        ),
        'exercise_summary' => fetchOne(
            $pdo,
            'SELECT
                COUNT(*) AS record_count,
                COALESCE(SUM(duration_minutes), 0) AS total_minutes,
                COALESCE(SUM(calories_burned), 0) AS total_calories
             FROM exercise_records
             WHERE user_id = :user_id',
            [':user_id' => $userId]
        ),
        'meal_summary' => fetchOne(
            $pdo,
            'SELECT
                COUNT(*) AS record_count,
                COALESCE(SUM(calories), 0) AS total_calories
             FROM meal_records
             WHERE user_id = :user_id',
            [':user_id' => $userId]
        ),
        'recent_exercises' => fetchAll(
            $pdo,
            'SELECT * FROM exercise_records WHERE user_id = :user_id ORDER BY exercise_date DESC, id DESC LIMIT 5',
            [':user_id' => $userId]
        ),
        'recent_meals' => fetchAll(
            $pdo,
            'SELECT * FROM meal_records WHERE user_id = :user_id ORDER BY meal_date DESC, id DESC LIMIT 5',
            [':user_id' => $userId]
        ),
    ];
}

function fetchOne(PDO $pdo, string $sql, array $params = []): ?array
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

function fetchAll(PDO $pdo, string $sql, array $params = []): array
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function formatMinutes(?int $minutes): string
{
    if (!$minutes) {
        return '0h 00m';
    }

    $hours = intdiv($minutes, 60);
    $remainingMinutes = $minutes % 60;

    return sprintf('%dh %02dm', $hours, $remainingMinutes);
}
?>

<!DOCTYPE html>
<html lang="ja">

<?php include '../components/head.php'; ?>

<body class="bg-slate-50 text-slate-800">
    <?php include '../components/nav.php'; ?>

    <main class="px-6 py-10 md:px-10">
        <div class="mx-auto max-w-6xl space-y-8">
            <header class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-semibold text-sky-600">Dashboard</p>
                    <h1 class="mt-2 text-3xl font-bold text-slate-900">
                        <?= htmlspecialchars($user['name']) ?>さんの健康ダッシュボード
                    </h1>
                    <p class="mt-3 text-sm leading-7 text-slate-500">
                        健康、運動、睡眠、食事の記録をまとめて確認できます。
                    </p>
                </div>
                <a href="<?= BASE_URL ?>health/add.php" class="inline-flex items-center justify-center rounded-lg kenko-gradient px-6 py-3 text-sm font-bold text-white shadow-md shadow-sky-200 transition hover:opacity-90">
                    健康記録を追加
                </a>
            </header>

            <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-sky-100 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">最新体重</p>
                    <p class="mt-4 text-3xl font-bold text-slate-900">
                        <?= $dashboard['latest_health'] ? htmlspecialchars($dashboard['latest_health']['weight']) . '<span class="text-base text-slate-400"> kg</span>' : '-' ?>
                    </p>
                    <p class="mt-2 text-xs text-slate-400">
                        <?= $dashboard['latest_health']['recorded_at'] ?? '記録なし' ?>
                    </p>
                </div>

                <div class="rounded-xl border border-sky-100 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">最新心拍数</p>
                    <p class="mt-4 text-3xl font-bold text-slate-900">
                        <?= $dashboard['latest_health'] ? htmlspecialchars($dashboard['latest_health']['heart_rate']) . '<span class="text-base text-slate-400"> bpm</span>' : '-' ?>
                    </p>
                    <p class="mt-2 text-xs text-slate-400">
                        血圧 <?= $dashboard['latest_health'] ? htmlspecialchars($dashboard['latest_health']['systolic']) . '/' . htmlspecialchars($dashboard['latest_health']['diastolic']) : '-' ?>
                    </p>
                </div>

                <div class="rounded-xl border border-sky-100 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">運動合計</p>
                    <p class="mt-4 text-3xl font-bold text-slate-900">
                        <?= (int) ($dashboard['exercise_summary']['total_minutes'] ?? 0) ?><span class="text-base text-slate-400"> min</span>
                    </p>
                    <p class="mt-2 text-xs text-slate-400">
                        <?= (int) ($dashboard['exercise_summary']['record_count'] ?? 0) ?>件 / <?= (int) ($dashboard['exercise_summary']['total_calories'] ?? 0) ?> kcal
                    </p>
                </div>

                <div class="rounded-xl border border-sky-100 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">最新睡眠</p>
                    <p class="mt-4 text-3xl font-bold text-slate-900">
                        <?= $dashboard['latest_sleep'] ? formatMinutes((int) $dashboard['latest_sleep']['sleep_duration_minutes']) : '-' ?>
                    </p>
                    <p class="mt-2 text-xs text-slate-400">
                        睡眠品質 <?= $dashboard['latest_sleep']['sleep_quality'] ?? '-' ?> / 5
                    </p>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-[1fr_0.9fr]">
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900">最近の運動</h2>
                        <span class="text-xs text-slate-400">最新5件</span>
                    </div>

                    <div class="space-y-3">
                        <?php if ($dashboard['recent_exercises']): ?>
                            <?php foreach ($dashboard['recent_exercises'] as $exercise): ?>
                                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-4 py-3">
                                    <div>
                                        <a href="<?= BASE_URL ?>activity/edit.php?id=<?= $exercise['id'] ?>" class="text-sm font-semibold text-slate-800 transition hover:text-sky-700"><?= htmlspecialchars($exercise['exercise_type']) ?></a>
                                        <p class="mt-1 text-xs text-slate-400"><?= htmlspecialchars($exercise['exercise_date']) ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-sky-700"><?= (int) $exercise['duration_minutes'] ?> min</p>
                                        <p class="mt-1 text-xs text-slate-400"><?= (int) $exercise['calories_burned'] ?> kcal</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="rounded-lg bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">運動記録はまだありません。</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900">最近の食事</h2>
                        <span class="text-xs text-slate-400">
                            合計 <?= (int) ($dashboard['meal_summary']['total_calories'] ?? 0) ?> kcal
                        </span>
                    </div>

                    <div class="space-y-3">
                        <?php if ($dashboard['recent_meals']): ?>
                            <?php foreach ($dashboard['recent_meals'] as $meal): ?>
                                <div class="rounded-lg bg-slate-50 px-4 py-3">
                                    <div class="flex items-center justify-between gap-4">
                                        <p class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($meal['food_name']) ?></p>
                                        <p class="shrink-0 text-sm font-bold text-sky-700"><?= (int) $meal['calories'] ?> kcal</p>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-400">
                                        <?= htmlspecialchars($meal['meal_date']) ?> / <?= htmlspecialchars($meal['meal_type']) ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="rounded-lg bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">食事記録はまだありません。</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php include '../components/footer.php'; ?>
</body>

</html>
