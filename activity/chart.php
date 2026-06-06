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
                    <p class="text-sm font-semibold text-orange-500">Activity Charts</p>
                    <h1 class="mt-2 text-3xl font-bold text-slate-900">アクティビティグラフ</h1>
                    <p class="mt-3 text-sm leading-7 text-slate-500">
                        日別の消費カロリーと運動時間の推移を確認できます。
                    </p>
                </div>

                <div class="flex gap-3">
                    <a href="activity/" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:border-orange-200 hover:text-orange-600">
                        一覧に戻る
                    </a>
                    <button onclick="downloadChart()" class="inline-flex items-center justify-center rounded-lg bg-orange-500 px-5 py-3 text-sm font-bold text-white shadow-md shadow-orange-200 transition hover:bg-orange-600">
                        グラフダウンロード
                    </button>
                </div>
            </header>

            <?php include '../components/message.php'; ?>

            <section class="grid gap-6">
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm md:p-6">
                    <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">消費カロリー</h2>
                            <p class="mt-1 text-xs text-slate-400">Daily calories burned &amp; exercise duration</p>
                        </div>
                        <div class="flex items-center gap-4 text-xs font-semibold">
                            <span class="text-orange-500">━ 消費カロリー（kcal）</span>
                            <span class="text-indigo-400">╌ 運動時間（分）</span>
                        </div>
                    </div>
                    <div class="h-96 md:h-[30rem]">
                        <canvas id="caloriesChart"></canvas>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php include '../components/footer.php'; ?>

    <script src="js/activity_chart.js" defer></script>
</body>

</html>
