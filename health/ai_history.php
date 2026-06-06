<?php
require_once '../app.php';

use Lib\Database;

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

$pdo  = Database::getInstance();
$stmt = $pdo->prepare(
    'SELECT id, diagnosis_type, result, created_at
     FROM ai_diagnosis_logs
     WHERE user_id = :user_id
     ORDER BY created_at DESC
     LIMIT 50'
);
$stmt->execute([':user_id' => (int) $_SESSION['user']['id']]);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <p class="text-sm font-semibold text-sky-600">AI Diagnosis History</p>
                    <h1 class="mt-2 text-3xl font-bold text-slate-900">AI診断履歴</h1>
                    <p class="mt-3 text-sm leading-7 text-slate-500">
                        過去の AI 診断結果を確認できます。最新50件を表示。
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="health/add.php" class="inline-flex items-center justify-center rounded-lg kenko-gradient px-5 py-3 text-sm font-bold text-white shadow-md shadow-sky-200 transition hover:opacity-90">
                        新規記録
                    </a>
                    <a href="health/" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:border-sky-200 hover:text-sky-700">
                        記録
                    </a>
                    <a href="health/chart.php" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:border-sky-200 hover:text-sky-700">
                        グラフ
                    </a>
                    <a href="health/ai_history.php" class="inline-flex items-center justify-center rounded-lg border border-sky-300 bg-sky-50 px-5 py-3 text-sm font-bold text-sky-700 transition hover:bg-sky-100">
                        AI履歴
                    </a>
                    <a href="api/health/ai/csv/" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:border-sky-200 hover:text-sky-700">
                        CSVダウンロード
                    </a>
                </div>
            </header>

            <?php if ($logs): ?>
                <div class="space-y-4">
                    <?php foreach ($logs as $log): ?>
                        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="inline-flex items-center rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">
                                    <?= htmlspecialchars($log['diagnosis_type']) ?>
                                </span>
                                <span class="text-xs text-slate-400"><?= htmlspecialchars($log['created_at']) ?></span>
                            </div>
                            <div class="prose prose-sm max-w-none text-sm leading-7 text-slate-700">
                                <?= nl2br(htmlspecialchars($log['result'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="rounded-xl border border-slate-200 bg-white px-6 py-16 text-center shadow-sm">
                    <p class="text-sm text-slate-500">AI診断の履歴はまだありません。</p>
                    <a href="health/" class="mt-4 inline-flex items-center justify-center rounded-lg kenko-gradient px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-sky-200 transition hover:opacity-90">
                        健康記録ページへ
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../components/footer.php'; ?>
</body>

</html>
