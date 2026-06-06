<?php
$auth_user = $_SESSION['user'] ?? null;
?>
<nav class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm shadow-sm border-b border-sky-100">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">

        <!-- ロゴ -->
        <a href="" class="inline-flex items-center gap-2 group">
            <img src="images/logo.png" class="h-10 w-auto" alt="Kenko Log">
        </a>

        <!-- ナビゲーション（デスクトップ） -->
        <ul class=" md:flex items-center gap-8 text-sm font-medium text-slate-600">
            <?php if ($auth_user): ?>
                <li>
                    <a href="dashboard/" class="relative py-1 transition hover:text-sky-600
                    after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0
                    after:bg-sky-500 after:transition-all hover:after:w-full">
                        ダッシュボード
                    </a>
                </li>
                <li>
                    <a href="health/" class="relative py-1 transition hover:text-sky-600
                    after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0
                    after:bg-sky-500 after:transition-all hover:after:w-full">
                        健康管理
                    </a>
                </li>
                <li>
                    <a href="activity/" class="relative py-1 transition hover:text-sky-600
                    after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0
                    after:bg-sky-500 after:transition-all hover:after:w-full">
                        アクティビティ
                    </a>
                </li>
                <li>
                    <a href="meal/" class="relative py-1 transition hover:text-sky-600
                    after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0
                    after:bg-sky-500 after:transition-all hover:after:w-full">
                        食事
                    </a>
                </li>
                <li>
                    <a href="sleep/" class="relative py-1 transition hover:text-sky-600
                    after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0
                    after:bg-sky-500 after:transition-all hover:after:w-full">
                        睡眠
                    </a>
                </li>
                <li>
                    <a href="logout/" class="relative py-1 transition hover:text-sky-600
                    after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0
                    after:bg-sky-500 after:transition-all hover:after:w-full"
                        onclick="return confirm('ログアウトしますか？');">
                        ログアウト
                    </a>
                </li>
            <?php endif ?>
        </ul>

        <!-- CTAボタン -->
        <div class="hidden md:flex items-center gap-3">
            <a href="login/"
                class="rounded-md border border-sky-300 px-5 py-2 text-sm font-semibold text-sky-700
                      transition hover:bg-sky-50">
                ログイン
            </a>
            <a href="register/"
                class="rounded-md kenko-gradient px-5 py-2 text-sm font-semibold text-white shadow-sm
                      transition hover:opacity-90">
                ユーザー登録
            </a>
            <a href="admin/"
                class="rounded-md border border-sky-300 px-5 py-2 text-sm font-semibold text-sky-700
                      transition hover:bg-sky-50">
                管理者
            </a>
        </div>

        <!-- ハンバーガー（モバイル） -->
        <button id="nav-toggle" class="md:hidden p-2 rounded-md text-slate-600 hover:bg-sky-50"
            aria-label="メニューを開く">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

</nav>