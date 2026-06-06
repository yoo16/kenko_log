<?php
require_once '../app.php';

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

$record = [
    'exercise_date' => date('Y-m-d'),
    'exercise_type' => '',
    'duration_minutes' => 30,
    'calories_burned' => '',
    'distance_km' => '',
    'memo' => '',
];

$message = '';

if (isset($_SESSION['activity_form'])) {
    $record = array_merge($record, $_SESSION['activity_form']);
    unset($_SESSION['activity_form']);
}

if (isset($_SESSION['activity_message'])) {
    $message = $_SESSION['activity_message'];
    unset($_SESSION['activity_message']);
}
?>

<!DOCTYPE html>
<html lang="ja">

<?php include '../components/head.php'; ?>

<body class="bg-slate-50 text-slate-800">
    <?php include '../components/nav.php'; ?>

    <main class="px-6 py-10 md:px-10">
        <section class="mx-auto max-w-3xl rounded-2xl border border-sky-100 bg-white p-8 shadow-xl shadow-sky-100/70 md:p-10">
            <div class="mb-8">
                <p class="text-sm font-semibold text-sky-600">New Activity</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">アクティビティ記録を追加</h1>
                <p class="mt-3 text-sm leading-7 text-slate-500">運動時間や消費カロリーを記録して、活動量を振り返れるようにします。</p>
            </div>

            <?php if ($message): ?>
                <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>activity/insert.php" method="post" class="space-y-6">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">運動日</label>
                    <input type="date" name="exercise_date" required value="<?= htmlspecialchars($record['exercise_date']) ?>"
                        class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">運動の種類</label>
                        <input type="text" name="exercise_type" required value="<?= htmlspecialchars($record['exercise_type']) ?>" placeholder="例: ウォーキング"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">運動時間（分）</label>
                        <input type="number" name="duration_minutes" min="1" required value="<?= htmlspecialchars($record['duration_minutes']) ?>"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">消費カロリー（kcal）</label>
                        <input type="number" name="calories_burned" min="0" value="<?= htmlspecialchars($record['calories_burned']) ?>"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">距離（km）</label>
                        <input type="number" name="distance_km" min="0" step="0.01" value="<?= htmlspecialchars($record['distance_km']) ?>"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">メモ</label>
                    <textarea name="memo" rows="4"
                        class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"><?= htmlspecialchars($record['memo']) ?></textarea>
                </div>

                <div class="flex flex-col gap-3 pt-3 sm:flex-row">
                    <button type="submit" class="w-full rounded-lg kenko-gradient px-6 py-3 text-sm font-bold text-white shadow-md shadow-sky-200 transition hover:opacity-90">
                        登録
                    </button>
                    <a href="<?= BASE_URL ?>activity/" class="w-full rounded-lg border border-sky-200 bg-white px-6 py-3 text-center text-sm font-bold text-sky-700 transition hover:bg-sky-50">キャンセル</a>
                </div>
            </form>
        </section>
    </main>

    <?php include '../components/footer.php'; ?>
</body>

</html>
