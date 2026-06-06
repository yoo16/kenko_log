<?php
require_once '../app.php';

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

$now  = new DateTimeImmutable();
$record = [
    'sleep_date'    => $now->format('Y-m-d'),
    'bedtime'       => $now->format('Y-m-d') . 'T23:00',
    'wake_time'     => $now->modify('+1 day')->format('Y-m-d') . 'T07:00',
    'sleep_quality' => '',
    'memo'          => '',
];

$message = '';

if (isset($_SESSION['sleep_form'])) {
    $record = array_merge($record, $_SESSION['sleep_form']);
    unset($_SESSION['sleep_form']);
}

if (isset($_SESSION['sleep_message'])) {
    $message = $_SESSION['sleep_message'];
    unset($_SESSION['sleep_message']);
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
                <p class="text-sm font-semibold text-sky-600">New Sleep</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">睡眠記録を追加</h1>
                <p class="mt-3 text-sm leading-7 text-slate-500">就寝・起床時刻を入力すると、睡眠時間を自動で計算します。</p>
            </div>

            <?php if ($message): ?>
                <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form action="sleep/insert.php" method="post" class="space-y-6">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">睡眠日</label>
                    <input type="date" name="sleep_date" required value="<?= htmlspecialchars($record['sleep_date']) ?>"
                        class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">就寝時刻</label>
                        <input type="datetime-local" name="bedtime" required value="<?= htmlspecialchars($record['bedtime']) ?>"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">起床時刻</label>
                        <input type="datetime-local" name="wake_time" required value="<?= htmlspecialchars($record['wake_time']) ?>"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">睡眠の質（任意）</label>
                    <select name="sleep_quality"
                        class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                        <option value="">未選択</option>
                        <?php foreach (range(1, 5) as $q): ?>
                            <option value="<?= $q ?>" <?= (string) $record['sleep_quality'] === (string) $q ? 'selected' : '' ?>>
                                <?= $q ?> <?= str_repeat('★', $q) . str_repeat('☆', 5 - $q) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">メモ（任意）</label>
                    <textarea name="memo" rows="4"
                        class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"><?= htmlspecialchars($record['memo']) ?></textarea>
                </div>

                <div class="flex flex-col gap-3 pt-3 sm:flex-row">
                    <button type="submit" class="w-full rounded-lg kenko-gradient px-6 py-3 text-sm font-bold text-white shadow-md shadow-sky-200 transition hover:opacity-90">
                        登録
                    </button>
                    <a href="sleep/" class="w-full rounded-lg border border-sky-200 bg-white px-6 py-3 text-center text-sm font-bold text-sky-700 transition hover:bg-sky-50">キャンセル</a>
                </div>
            </form>
        </section>
    </main>

    <?php include '../components/footer.php'; ?>
</body>

</html>
