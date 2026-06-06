<?php
require_once '../app.php';

use Lib\Database;

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

$records = getMealRecords((int) $_SESSION['user']['id']);

function getMealRecords(int $userId, int $limit = 30): array
{
    $pdo = Database::getInstance();
    $sql = 'SELECT * FROM meal_records 
                WHERE user_id = :user_id 
                ORDER BY meal_date DESC, id DESC 
                LIMIT :limit';
    $stmt = $pdo->prepare($sql);
    // SQLを実行
    $stmt->execute([
        ':user_id' => $userId,
        ':limit' => $limit
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function mealTypeLabel(string $mealType): string
{
    $labels = [
        'breakfast' => '朝食',
        'lunch' => '昼食',
        'dinner' => '夕食',
        'snack' => '間食',
    ];

    return $labels[$mealType] ?? $mealType;
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
                    <p class="text-sm font-semibold text-sky-600">Meal Records</p>
                    <h1 class="mt-2 text-3xl font-bold text-slate-900">食事記録</h1>
                    <p class="mt-3 text-sm leading-7 text-slate-500">
                        食事の種類、メニュー、カロリー、栄養素を記録できます。
                    </p>
                </div>

                <a href="meal/add.php" class="inline-flex items-center justify-center rounded-lg kenko-gradient px-5 py-3 text-sm font-bold text-white shadow-md shadow-sky-200 transition hover:opacity-90">
                    新規記録
                </a>
            </header>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto border-collapse text-sm">
                        <thead class="bg-sky-50 text-left text-xs font-semibold uppercase tracking-wide text-sky-700">
                            <tr>
                                <th class="px-5 py-4 font-semibold"></th>
                                <th class="px-5 py-4 font-semibold">日付</th>
                                <th class="px-5 py-4 font-semibold">種類</th>
                                <th class="px-5 py-4 font-semibold">メニュー</th>
                                <th class="px-5 py-4 font-semibold">カロリー</th>
                                <th class="px-5 py-4 font-semibold">P/F/C</th>
                                <th class="px-5 py-4 font-semibold">メモ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($records as $row): ?>
                                <tr class="text-slate-700 transition hover:bg-sky-50/60">
                                    <td class="px-5 py-4">
                                        <a href="meal/edit.php?id=<?= $row['id'] ?>" class="inline-flex rounded-md border border-sky-200 px-3 py-1.5 text-xs font-semibold text-sky-700 transition hover:bg-sky-50">Edit</a>
                                    </td>
                                    <td class="px-5 py-4 font-medium" nowrap="nowrap"><?= htmlspecialchars($row['meal_date']) ?></td>
                                    <td class="px-5 py-4"><?= htmlspecialchars(mealTypeLabel($row['meal_type'])) ?></td>
                                    <td class="px-5 py-4"><?= htmlspecialchars($row['food_name']) ?></td>
                                    <td class="px-5 py-4"><?= $row['calories'] !== null ? (int) $row['calories'] . ' kcal' : '-' ?></td>
                                    <td class="px-5 py-4 text-slate-500">
                                        <?= $row['protein_g'] !== null ? htmlspecialchars($row['protein_g']) . 'g' : '-' ?> /
                                        <?= $row['fat_g'] !== null ? htmlspecialchars($row['fat_g']) . 'g' : '-' ?> /
                                        <?= $row['carbohydrate_g'] !== null ? htmlspecialchars($row['carbohydrate_g']) . 'g' : '-' ?>
                                    </td>
                                    <td class="px-5 py-4 text-slate-500"><?= htmlspecialchars($row['memo'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!$records): ?>
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">食事記録はまだありません。</td>
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
