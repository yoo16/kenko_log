<?php
require_once 'app.php';
// use Lib\Database;
// try {
//     Database::getInstance();
// } catch (Throwable $e) {
//     header('Location: create_database.php');
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="ja">
<?php include 'components/head.php'; ?>

<body class="bg-white text-slate-800">

    <?php include 'components/nav.php'; ?>

    <main class="w-full">

        <!-- ========== ヒーローセクション ========== -->
        <section class="relative overflow-hidden bg-cover bg-center"
            style="background-image: url('images/back_image.png');">

            <div class="absolute inset-0"></div>

            <div class="relative grid min-h-[calc(100vh-88px)] w-full items-stretch md:grid-cols-[1.0fr_1.1fr]">

                <!-- 左カラム：テキスト＆CTA -->
                <div class="flex items-center px-6 py-16">
                    <div class="max-w-xl space-y-8 fade-up fade-up-1 p-6 rounded-2xl">

                        <!-- キャッチコピー -->
                        <div class="space-y-4 bg-white/90 p-8 rounded-2xl shadow-lg shadow-sky-900/10">
                            <h2 class="text-4xl font-bold leading-loose tracking-loose text-slate-900 md:text-5xl">
                                日々の健康を、<br>
                                もっと楽しく、<br>
                                <span class="text-sky-600">かんたんに記録。</span>
                            </h2>
                            <p class="max-w-md text-base leading-8 text-black md:text-lg">
                                <?= SITE_TITLE ?> は、あなたの毎日をサポートする<br>
                                オールインワン健康管理アプリです。
                            </p>
                        </div>

                        <!-- CTAボタン -->
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <a href="register/"
                                class="inline-flex items-center justify-center gap-2 rounded-lg kenko-gradient
                                    px-8 py-3.5 text-sm font-bold text-white shadow-md shadow-sky-200
                                    transition hover:opacity-90 hover:shadow-lg hover:shadow-sky-300">
                                ユーザ登録
                            </a>
                            <a href="<?= BASE_URL ?>login/"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-sky-200
                                    bg-white px-8 py-3.5 text-sm font-bold text-sky-700 shadow-sm
                                    transition hover:border-sky-300 hover:bg-sky-50">
                                ログイン
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 右カラム：メインビジュアル -->
                <div class="flex min-h-[520px] fade-up fade-up-2 md:min-h-full">
                    <div class="relative flex w-full items-center p-6 md:justify-end md:p-10 lg:p-14">
                        <!-- カード -->
                        <div class="relative w-full max-w-sm rounded-2xl border border-white/70 bg-white/90 p-6 shadow-xl shadow-sky-900/10 backdrop-blur md:mr-6 lg:mr-10">

                            <!-- カードヘッダー -->
                            <div class="mb-4 flex items-center justify-between">
                                <span class="text-sm font-bold text-slate-700">Dashboard</span>
                                <span class="text-xs text-slate-400"><?= date('Y年n月j日') ?></span>
                            </div>

                            <!-- 歩数 -->
                            <div class="mb-4 rounded-xl kenko-gradient p-4 text-white">
                                <p class="mb-1 text-xs font-medium opacity-80">Today's Steps</p>
                                <p class="text-4xl font-bold tracking-tight">10,234</p>
                                <p class="mt-1 text-xs opacity-70">Goal 12,000</p>
                                <div class="mt-3 h-1.5 w-full rounded-full bg-white/30">
                                    <div class="h-1.5 rounded-full bg-white" style="width: 85%"></div>
                                </div>
                            </div>

                            <!-- Sleep / Calories -->
                            <div class="mb-4 grid grid-cols-2 gap-3">
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <p class="text-xs text-slate-400">Sleep</p>
                                    <p class="mt-1 text-lg font-bold text-slate-800">7<span class="text-sm font-medium">h</span> 15<span class="text-sm font-medium">m</span></p>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <p class="text-xs text-slate-400">Active Cal.</p>
                                    <p class="mt-1 text-lg font-bold text-slate-800">450<span class="text-sm font-medium">kcal</span></p>
                                </div>
                            </div>

                            <!-- Meal log -->
                            <div class="rounded-xl bg-slate-50 p-3">
                                <div class="mb-2 flex items-center justify-between">
                                    <p class="text-xs font-semibold text-slate-600">Meal log</p>
                                    <a href="<?= BASE_URL ?>health/add.php" class="text-xs text-sky-600 hover:underline">更新 ›</a>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-lg bg-amber-100 flex items-center justify-center text-base">🥣</div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-700">Breakfast</p>
                                        <p class="text-xs text-slate-400">Oatmeal &amp; Fruit</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ========== 主な機能セクション ========== -->
        <section class="bg-white px-6 py-16 md:px-10 md:py-24">
            <div class="mx-auto max-w-6xl">

                <!-- セクションタイトル -->
                <div class="mb-12 text-center fade-up fade-up-1">
                    <h3 class="text-2xl font-bold text-slate-900 md:text-3xl">主な機能</h3>
                    <p class="mt-3 text-sm text-slate-500">健康管理に必要なすべてをひとつに。</p>
                </div>

                <!-- 機能カード -->
                <div class="grid gap-6 md:grid-cols-3 fade-up fade-up-2">

                    <!-- ステップ/アクティビティ -->
                    <a href="<?= BASE_URL ?>health/add.php"
                        class="kenko-card group rounded-2xl border border-slate-200 bg-white p-7
                              hover:border-sky-200 hover:shadow-xl hover:shadow-sky-100/60">
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl
                                    bg-sky-100 text-sky-600 text-2xl transition group-hover:bg-sky-600 group-hover:text-white">
                            🏃
                        </div>
                        <h4 class="mb-3 text-base font-bold text-slate-900">ステップ / アクティビティ</h4>
                        <p class="text-sm leading-7 text-slate-500">
                            ステップやアクティビティでも、目標的に活動できます。
                        </p>
                    </a>

                    <!-- 睡眠分析 -->
                    <a href="<?= BASE_URL ?>dashboard/"
                        class="kenko-card group rounded-2xl border border-slate-200 bg-white p-7
                              hover:border-sky-200 hover:shadow-xl hover:shadow-sky-100/60">
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl
                                    bg-indigo-100 text-indigo-600 text-2xl transition group-hover:bg-indigo-600 group-hover:text-white">
                            🌙
                        </div>
                        <h4 class="mb-3 text-base font-bold text-slate-900">睡眠分析</h4>
                        <p class="text-sm leading-7 text-slate-500">
                            毎日の分析にもったいない毎日をサポートする健康する。
                        </p>
                    </a>

                    <!-- 食事を追知 -->
                    <a href="<?= BASE_URL ?>health/chart.php"
                        class="kenko-card group rounded-2xl border border-slate-200 bg-white p-7
                              hover:border-sky-200 hover:shadow-xl hover:shadow-sky-100/60">
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl
                                    bg-emerald-100 text-emerald-600 text-2xl transition group-hover:bg-emerald-600 group-hover:text-white">
                            🥗
                        </div>
                        <h4 class="mb-3 text-base font-bold text-slate-900">食事を追知</h4>
                        <p class="text-sm leading-7 text-slate-500">
                            オームール＆フルーツなど覚定されるものを掴握します。
                        </p>
                    </a>

                </div>
            </div>
        </section>

    </main>

    <?php include 'components/footer.php'; ?>
    <script src="js/app.js" defer></script>
</body>

</html>
