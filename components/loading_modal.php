<?php 
$loading_message = $loading_message ?? '処理中です';
?>
<div id="ai-loading-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm">
    <div class="flex flex-col items-center gap-5 rounded-2xl bg-white px-14 py-10 shadow-2xl">
        <div class="h-12 w-12 animate-spin rounded-full border-4 border-sky-200 border-t-sky-600"></div>
        <div class="text-center">
            <p class="text-sm font-bold text-slate-700"><?= $loading_message ?></p>
            <p class="mt-1 text-xs text-slate-400">少々お待ちください…</p>
        </div>
    </div>
</div>