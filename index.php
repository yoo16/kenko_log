<?php
require_once 'app.php';
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

                <?php include 'components/top/hero_left.php'; ?>
                <?php include 'components/top/hero_right.php'; ?>

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
                    <a href="health/add.php"
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
                    <a href="dashboard/"
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
                    <a href="health/chart.php"
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
