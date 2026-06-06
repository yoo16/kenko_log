<?php
require_once '../app.php';

use Lib\Database;

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

$records = getActivityRecords((int) $_SESSION['user']['id']);

function getActivityRecords(int $userId, int $limit = 30): array
{
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare(
        'SELECT * FROM exercise_records
            WHERE user_id = :user_id
            ORDER BY exercise_date DESC, id DESC
            LIMIT :limit'
    );
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <p class="text-sm font-semibold text-sky-600">Activity Records</p>
                    <h1 class="mt-2 text-3xl font-bold text-slate-900">アクティビティ記録</h1>
                    <p class="mt-3 text-sm leading-7 text-slate-500">
                        運動の種類、時間、消費カロリー、距離を記録できます。
                    </p>
                </div>

                <div class="flex gap-3">
                    <a href="activity/chart.php" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:border-sky-200 hover:text-sky-700">
                        グラフ
                    </a>
                    <a href="activity/add.php" class="inline-flex items-center justify-center rounded-lg kenko-gradient px-5 py-3 text-sm font-bold text-white shadow-md shadow-sky-200 transition hover:opacity-90">
                        新規記録
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
                                <th class="px-5 py-4 font-semibold">種類</th>
                                <th class="px-5 py-4 font-semibold">時間</th>
                                <th class="px-5 py-4 font-semibold">消費カロリー</th>
                                <th class="px-5 py-4 font-semibold">距離</th>
                                <th class="px-5 py-4 font-semibold">メモ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($records as $row): ?>
                                <tr class="text-slate-700 transition hover:bg-sky-50/60">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="activity/edit.php?id=<?= $row['id'] ?>" class="inline-flex rounded-md border border-sky-200 px-3 py-1.5 text-xs font-semibold text-sky-700 transition hover:bg-sky-50">Edit</a>
                                            <form action="activity/duplicate.php" method="post">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <button type="submit" class="inline-flex rounded-md border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-500 transition hover:border-sky-200 hover:text-sky-700 hover:bg-sky-50">複製</button>
                                            </form>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 font-medium" nowrap="nowrap"><?= htmlspecialchars($row['exercise_date']) ?></td>
                                    <td class="px-5 py-4"><?= htmlspecialchars($row['exercise_type']) ?></td>
                                    <td class="px-5 py-4"><?= (int) $row['duration_minutes'] ?>分</td>
                                    <td class="px-5 py-4"><?= $row['calories_burned'] !== null ? (int) $row['calories_burned'] . ' kcal' : '-' ?></td>
                                    <td class="px-5 py-4"><?= $row['distance_km'] !== null ? htmlspecialchars($row['distance_km']) . ' km' : '-' ?></td>
                                    <td class="px-5 py-4 text-slate-500"><?= htmlspecialchars($row['memo'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!$records): ?>
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">アクティビティ記録はまだありません。</td>
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
