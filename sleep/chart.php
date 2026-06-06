<?php
require_once '../app.php';

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
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
                    <p class="text-sm font-semibold text-sky-600">Sleep Charts</p>
                    <h1 class="mt-2 text-3xl font-bold text-slate-900">睡眠グラフ</h1>
                    <p class="mt-3 text-sm leading-7 text-slate-500">
                        睡眠時間の推移をグラフで確認できます。
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
                    <button onclick="downloadChart()" class="inline-flex items-center justify-center rounded-lg kenko-gradient px-5 py-3 text-sm font-bold text-white shadow-md shadow-sky-200 transition hover:opacity-90">
                        グラフダウンロード
                    </button>
                </div>
            </header>

            <?php include '../components/message.php'; ?>

            <section class="grid gap-6">
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm md:p-6">
                    <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">睡眠時間</h2>
                            <p class="mt-1 text-xs text-slate-400">Sleep duration trend</p>
                        </div>
                        <span class="text-xs font-semibold text-sky-700">時間 (h)</span>
                    </div>
                    <div class="h-72 md:h-96">
                        <canvas id="sleepChart"></canvas>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php include '../components/footer.php'; ?>
    <script src="js/sleep_chart.js" defer></script>
</body>

</html>