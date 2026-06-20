<?php
$basePath = BASE_URL;
?>

<footer class="bg-sky-700 text-white">
    <div class="max-w-6xl mx-auto px-6 py-4">

        <!-- 上段：ロゴ＋ナビ -->
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">

            <!-- ロゴ＋キャッチ -->
            <div class="space-y-3">
                <a href="<?= $basePath ?>" class="inline-flex items-center gap-2">
                    KENKO LOG
                </a>
            </div>
            <div class="text-xs">
                <!-- TODO: クレジットの年数を自動更新 -->
                <span>&copy; 2020 - 2022 <?= SITE_TITLE ?>. All rights reserved.</span>
            </div>
        </div>
    </div>
</footer>