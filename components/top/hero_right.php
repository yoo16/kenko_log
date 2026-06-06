<div class="flex min-h-[520px] fade-up fade-up-2 md:min-h-full">
    <div class="relative flex w-full items-center p-6 md:justify-end md:p-10 lg:p-14">
        <!-- カード -->
        <div class="relative w-full max-w-sm rounded-2xl border border-white/70 bg-white/90 p-6 shadow-xl shadow-sky-900/10 backdrop-blur md:mr-6 lg:mr-10">

            <!-- カードヘッダー -->
            <div class="mb-4 flex items-center justify-between">
                <span class="text-sm font-bold text-slate-700">あなたの健康データ</span>
                <span class="text-xs text-slate-400"><?= date('Y年n月j日') ?></span>
            </div>

            <!-- 歩数 -->
            <div class="mb-4 rounded-xl kenko-gradient p-4 text-white">
                <p class="mb-1 text-xs font-medium opacity-80">今日のアクティビティ</p>
                <p class="text-4xl font-bold tracking-tight">634 Kcal</p>
                <p class="mt-1 text-xs opacity-70">目標 1,000 Kcal</p>
                <div class="mt-3 h-1.5 w-full rounded-full bg-white/30">
                    <div class="h-1.5 rounded-full bg-white" style="width: 85%"></div>
                </div>
            </div>

            <!-- Sleep / Calories -->
            <div class="mb-4 grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-xs text-slate-400">睡眠時間</p>
                    <p class="mt-1 text-lg font-bold text-slate-800">7<span class="text-sm font-medium">h</span> 15<span class="text-sm font-medium">m</span></p>
                </div>
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-xs text-slate-400">消費カロリー</p>
                    <p class="mt-1 text-lg font-bold text-slate-800">450<span class="text-sm font-medium">kcal</span></p>
                </div>
            </div>

            <!-- Meal log -->
            <div class="rounded-xl bg-slate-50 p-3">
                <div class="mb-2 flex items-center justify-between">
                    <p class="text-xs font-semibold text-slate-600">食事ログ</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-amber-100 flex items-center justify-center text-base">🥣</div>
                    <div>
                        <p class="text-xs font-semibold text-slate-700">朝食</p>
                        <p class="text-xs text-slate-400">オートミール &amp; フルーツ</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>