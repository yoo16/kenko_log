<?php
require_once '../app.php';

use Lib\Database;

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

$records = getSleepRecords((int) $_SESSION['user']['id']);

function getSleepRecords(int $userId, int $limit = 30): array
{
    $pdo = Database::getInstance();
    $sql = "SELECT * FROM sleep_records
                WHERE user_id = :user_id
                ORDER BY sleep_date DESC, id DESC
                LIMIT :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function formatDuration(int $minutes): string
{
    $h = intdiv($minutes, 60);
    $m = $minutes % 60;
    return $h > 0 ? "{$h}時間{$m}分" : "{$m}分";
}

function qualityLabel(?int $quality): string
{
    if ($quality === null) return '-';
    // 0〜5の数値を★で表現
    $stars = str_repeat('★', $quality) . str_repeat('☆', 5 - $quality);
    return $stars;
}
?>

<!DOCTYPE html>
<html lang="ja">

<?php include '../components/head.php'; ?>

<body class="bg-slate-50 text-slate-800">
    <?php include '../components/nav.php'; ?>

    <main class="px-6 py-10 md:px-10">
        <div class="mx-auto max-w-6xl space-y-8">
            <header class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-semibold text-sky-600">Sleep Records</p>
                    <h1 class="mt-2 text-3xl font-bold text-slate-900">睡眠記録</h1>
                    <p class="mt-3 text-sm leading-7 text-slate-500">
                        就寝・起床時刻と睡眠の質を記録して、睡眠習慣を振り返れます。
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="sleep/add.php" class="inline-flex items-center justify-center rounded-lg kenko-gradient px-5 py-3 text-sm font-bold text-white shadow-md shadow-sky-200 transition hover:opacity-90">
                        新規記録
                    </a>
                    <a href="sleep/" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:border-sky-200 hover:text-sky-700">
                        記録
                    </a>
                    <a href="sleep/chart.php" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:border-sky-200 hover:text-sky-700">
                        グラフ
                    </a>
                </div>
            </header>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto border-collapse text-sm">
                        <thead class="bg-sky-50 text-left text-xs font-semibold uppercase tracking-wide text-sky-700">
                            <tr>
                                <th class="px-5 py-4 font-semibold"></th>
                                <th class="px-5 py-4 font-semibold">日付</th>
                                <th class="px-5 py-4 font-semibold">就寝</th>
                                <th class="px-5 py-4 font-semibold">起床</th>
                                <th class="px-5 py-4 font-semibold">睡眠時間</th>
                                <th class="px-5 py-4 font-semibold">睡眠の質</th>
                                <th class="px-5 py-4 font-semibold">メモ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($records as $row): ?>
                                <tr class="text-slate-700 transition hover:bg-sky-50/60">
                                    <td class="px-5 py-4">
                                        <a href="sleep/edit.php?id=<?= $row['id'] ?>" class="inline-flex rounded-md border border-sky-200 px-3 py-1.5 text-xs font-semibold text-sky-700 transition hover:bg-sky-50">Edit</a>
                                    </td>
                                    <td class="px-5 py-4 font-medium" nowrap="nowrap"><?= htmlspecialchars($row['sleep_date']) ?></td>
                                    <td class="px-5 py-4" nowrap="nowrap"><?= htmlspecialchars(substr($row['bedtime'], 0, 16)) ?></td>
                                    <td class="px-5 py-4" nowrap="nowrap"><?= htmlspecialchars(substr($row['wake_time'], 0, 16)) ?></td>
                                    <td class="px-5 py-4"><?= formatDuration((int) $row['sleep_duration_minutes']) ?></td>
                                    <td class="px-5 py-4 text-amber-400"><?= qualityLabel($row['sleep_quality'] !== null ? (int) $row['sleep_quality'] : null) ?></td>
                                    <td class="px-5 py-4 text-slate-500"><?= htmlspecialchars($row['memo'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!$records): ?>
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">睡眠記録はまだありません。</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include '../components/footer.php'; ?>
</body>

</html>