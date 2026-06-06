<?php
require_once '../app.php';

use Lib\Database;

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

// 一覧データを取得
$records = get((int) $_SESSION['user']['id']);

function get(int $userId, int $limit = 30)
{
    // データベース接続
    $pdo = Database::getInstance();
    // SQLクエリ
    $sql = "SELECT * FROM health_records 
                WHERE user_id = :user_id 
                ORDER BY recorded_at DESC 
                LIMIT :limit";
    // プリペアドステートメントを作成
    $stmt = $pdo->prepare($sql);
    // SQLを実行
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    // 結果を取得
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $records;
}
?>

<!DOCTYPE html>
<html lang="ja">

<?php include '../components/head.php' ?>

<body class="bg-slate-50 text-slate-800">
    <?php include '../components/nav.php' ?>

    <main class="px-6 py-10 md:px-10">
        <div class="mx-auto max-w-6xl space-y-8">
            <header class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-semibold text-sky-600">Health Records</p>
                    <h1 class="mt-2 text-3xl font-bold text-slate-900">健康管理</h1>
                    <p class="mt-3 text-sm leading-7 text-slate-500">
                        体重、心拍数、血圧の変化を日付ごとに確認できます。
                    </p>
                </div>

                <!-- メニュー -->
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
                    <a href="api/health/csv/" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:border-sky-200 hover:text-sky-700">
                        CSVダウンロード
                    </a>
                    <a id="ai-chat-btn" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:border-sky-200 hover:text-sky-700">
                        AI分析
                    </a>
                    <a href="health/ai_history.php" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:border-sky-200 hover:text-sky-700">
                        AI履歴
                    </a>
                </div>
            </header>
    
            <!-- メッセージ表示 -->
            <?php include '../components/message.php'; ?>
            <!-- AI結果表示 -->
            <div id="ai-result" class="hidden rounded-xl border border-sky-100 bg-white p-6 text-sm leading-7 text-slate-700 shadow-sm shadow-sky-100/70">
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto border-collapse text-sm">
                        <thead class="bg-sky-50 text-left text-xs font-semibold uppercase tracking-wide text-sky-700">
                            <tr>
                                <th class="px-5 py-4 font-semibold"></th>
                                <th class="px-5 py-4 font-semibold">日付</th>
                                <th class="px-5 py-4 font-semibold">体重(kg)</th>
                                <th class="px-5 py-4 font-semibold">心拍数(bpm)</th>
                                <th class="px-5 py-4 font-semibold">血圧（上）</th>
                                <th class="px-5 py-4 font-semibold">血圧（下）</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($records as $row): ?>
                                <tr class="text-slate-700 transition hover:bg-sky-50/60">
                                    <td class="px-5 py-4">
                                        <a href="health/edit.php?id=<?= $row['id'] ?>" class="inline-flex rounded-md border border-sky-200 px-3 py-1.5 text-xs font-semibold text-sky-700 transition hover:bg-sky-50">Edit</a>
                                    </td>
                                    <td class="px-5 py-4 font-medium" nowrap="nowrap"><?= htmlspecialchars($row['recorded_at']) ?></td>
                                    <td class="px-5 py-4"><?= htmlspecialchars($row['weight']) ?></td>
                                    <td class="px-5 py-4"><?= htmlspecialchars($row['heart_rate']) ?></td>
                                    <td class="px-5 py-4"><?= htmlspecialchars($row['systolic']) ?></td>
                                    <td class="px-5 py-4"><?= htmlspecialchars($row['diastolic']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include '../components/footer.php'; ?>

    <!-- AI ローディングモーダル -->
    <div id="ai-loading-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm">
        <div class="flex flex-col items-center gap-5 rounded-2xl bg-white px-14 py-10 shadow-2xl">
            <div class="h-12 w-12 animate-spin rounded-full border-4 border-sky-200 border-t-sky-600"></div>
            <div class="text-center">
                <p class="text-sm font-bold text-slate-700">AI 診断中</p>
                <p class="mt-1 text-xs text-slate-400">少々お待ちください…</p>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="js/health_ai.js" defer></script>
</body>

</html>