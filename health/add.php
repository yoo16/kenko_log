<?php
// 共通処理を読み込む
require_once '../app.php';

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

// 初期値
$record = [
    'weight' => 50,
    'heart_rate' => 80,
    'systolic' => 120,
    'diastolic' => 80,
    'recorded_at' => date('Y-m-d'),
];

// 初期メッセージ
$message = '';

// セッションから値を取得
if (isset($_SESSION['form'])) {
    $record = $_SESSION['form'];
    unset($_SESSION['form']);
}

// エラーメッセージの取得
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="ja">

<?php include '../components/head.php' ?>

<body class="bg-slate-50 text-slate-800">
    <?php include '../components/nav.php' ?>

    <main class="px-6 py-10 md:px-10">
        <section class="mx-auto max-w-3xl rounded-2xl border border-sky-100 bg-white p-8 shadow-xl shadow-sky-100/70 md:p-10">
            <div class="mb-8">
                <p class="text-sm font-semibold text-sky-600">New Record</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">健康記録を追加</h1>
                <p class="mt-3 text-sm leading-7 text-slate-500">今日の体調を記録して、日々の変化を振り返れるようにします。</p>
            </div>

            <?php if ($message): ?>
                <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>health/insert.php" method="post" class="space-y-6">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">記録日</label>
                    <input type="date" name="recorded_at" required value="<?= htmlspecialchars($record['recorded_at']) ?>"
                        class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">体重（kg）</label>
                        <input type="number" name="weight" step="0.1" required value="<?= htmlspecialchars($record['weight']) ?>"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">心拍数（bpm）</label>
                        <input type="number" name="heart_rate" required value="<?= htmlspecialchars($record['heart_rate']) ?>"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">血圧（上）</label>
                        <input type="number" name="systolic" required value="<?= htmlspecialchars($record['systolic']) ?>"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">血圧（下）</label>
                        <input type="number" name="diastolic" required value="<?= htmlspecialchars($record['diastolic']) ?>"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                    </div>
                </div>

                <div class="flex flex-col gap-3 pt-3 sm:flex-row">
                    <button type="submit" class="w-full rounded-lg kenko-gradient px-6 py-3 text-sm font-bold text-white shadow-md shadow-sky-200 transition hover:opacity-90">
                        登録
                    </button>
                    <a href="<?= BASE_URL ?>health/" class="w-full rounded-lg border border-sky-200 bg-white px-6 py-3 text-center text-sm font-bold text-sky-700 transition hover:bg-sky-50">キャンセル</a>
                </div>
            </form>
        </section>
    </main>

    <?php include '../components/footer.php'; ?>
</body>

</html>
