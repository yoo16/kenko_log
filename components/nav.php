<?php
require_once __DIR__ . '/../app.php';
?>
<nav class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm shadow-sm border-b border-sky-100">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">

        <!-- ロゴ -->
        <a href="" class="inline-flex items-center gap-2 group">
            <img src="images/logo.png" class="h-10 w-auto" alt="Kenko Log">
        </a>

        <!-- ナビゲーション（デスクトップ） -->
        <!-- TODO: $auth_user が存在する場合のナビゲーション項目を表示 -->
        <?php include BASE_DIR . 'components/user_nav.php'; ?>

        <!-- CTAボタン -->
        <?php include BASE_DIR . 'components/public_nav.php'; ?>

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