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

            <div id="dashboard-error" class="hidden rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                データの取得に失敗しました。
            </div>

            <header class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-semibold text-sky-600">Dashboard</p>
                    <h1 id="dashboard-title" class="skeleton mt-2 text-3xl font-bold text-slate-900">　</h1>
                    <p class="mt-3 text-sm leading-7 text-slate-500">
                        健康、運動、睡眠、食事の記録をまとめて確認できます。
                    </p>
                </div>
                <a href="health/add.php" class="inline-flex items-center justify-center rounded-lg kenko-gradient px-6 py-3 text-sm font-bold text-white shadow-md shadow-sky-200 transition hover:opacity-90">
                    健康記録を追加
                </a>
            </header>

            <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-sky-100 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">最新体重</p>
                    <p id="stat-weight" class="skeleton mt-4 text-3xl font-bold text-slate-900">　</p>
                    <p id="stat-weight-date" class="skeleton mt-2 text-xs text-slate-400">　</p>
                </div>

                <div class="rounded-xl border border-sky-100 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">最新心拍数</p>
                    <p id="stat-heart" class="skeleton mt-4 text-3xl font-bold text-slate-900">　</p>
                    <p id="stat-bp" class="skeleton mt-2 text-xs text-slate-400">　</p>
                </div>

                <div class="rounded-xl border border-sky-100 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">運動合計</p>
                    <p id="stat-exercise" class="skeleton mt-4 text-3xl font-bold text-slate-900">　</p>
                    <p id="stat-exercise-sub" class="skeleton mt-2 text-xs text-slate-400">　</p>
                </div>

                <div class="rounded-xl border border-sky-100 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">最新睡眠</p>
                    <p id="stat-sleep" class="skeleton mt-4 text-3xl font-bold text-slate-900">　</p>
                    <p id="stat-sleep-sub" class="skeleton mt-2 text-xs text-slate-400">　</p>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-[1fr_0.9fr]">
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900">最近の運動</h2>
                        <span class="text-xs text-slate-400">最新5件</span>
                    </div>
                    <div id="recent-exercises" class="space-y-3">
                        <div class="skeleton h-14 rounded-lg bg-slate-50"></div>
                        <div class="skeleton h-14 rounded-lg bg-slate-50"></div>
                        <div class="skeleton h-14 rounded-lg bg-slate-50"></div>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900">最近の食事</h2>
                        <span id="meal-total-calories" class="skeleton text-xs text-slate-400">　</span>
                    </div>
                    <div id="recent-meals" class="space-y-3">
                        <div class="skeleton h-14 rounded-lg bg-slate-50"></div>
                        <div class="skeleton h-14 rounded-lg bg-slate-50"></div>
                        <div class="skeleton h-14 rounded-lg bg-slate-50"></div>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <?php include '../components/footer.php'; ?>
    <script src="js/dashboard.js" defer></script>
</body>

</html>
