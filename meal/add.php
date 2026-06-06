<?php
require_once '../app.php';

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

$record = [
    'meal_date' => date('Y-m-d'),
    'meal_type' => 'breakfast',
    'food_name' => '',
    'calories' => '',
    'protein_g' => '',
    'fat_g' => '',
    'carbohydrate_g' => '',
    'memo' => '',
];

$message = '';

if (isset($_SESSION['meal_form'])) {
    $record = array_merge($record, $_SESSION['meal_form']);
    unset($_SESSION['meal_form']);
}

if (isset($_SESSION['meal_message'])) {
    $message = $_SESSION['meal_message'];
    unset($_SESSION['meal_message']);
}
?>

<!DOCTYPE html>
<html lang="ja">

<?php include '../components/head.php'; ?>

<body class="bg-slate-50 text-slate-800">
    <?php include '../components/nav.php'; ?>

    <main class="px-6 py-10 md:px-10">
        <section class="mx-auto max-w-3xl rounded-2xl border border-sky-100 bg-white p-8 shadow-xl shadow-sky-100/70 md:p-10">
            <div class="mb-8">
                <p class="text-sm font-semibold text-sky-600">New Meal</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">食事記録を追加</h1>
                <p class="mt-3 text-sm leading-7 text-slate-500">食事内容や栄養素を記録して、摂取量を振り返れるようにします。</p>
            </div>

            <?php if ($message): ?>
                <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form action="meal/insert.php" method="post" class="space-y-6">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">食事日</label>
                    <input type="date" name="meal_date" required value="<?= htmlspecialchars($record['meal_date']) ?>"
                        class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">食事の種類</label>
                        <select name="meal_type" required
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                            <option value="breakfast" <?= $record['meal_type'] === 'breakfast' ? 'selected' : '' ?>>朝食</option>
                            <option value="lunch" <?= $record['meal_type'] === 'lunch' ? 'selected' : '' ?>>昼食</option>
                            <option value="dinner" <?= $record['meal_type'] === 'dinner' ? 'selected' : '' ?>>夕食</option>
                            <option value="snack" <?= $record['meal_type'] === 'snack' ? 'selected' : '' ?>>間食</option>
                        </select>
                    </div>
                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <label class="text-sm font-semibold text-slate-700">メニュー</label>
                        </div>
                        <input type="text" name="food_name" id="food_name" required value="<?= htmlspecialchars($record['food_name']) ?>" placeholder="例: 焼き鮭定食"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                        <p id="meal-ai-message" class="mt-2 text-xs"></p>
                    </div>
                    <button type="button" id="meal-ai-btn"
                        class="inline-flex items-center gap-1 rounded-md border border-sky-200 px-3 py-2 font-semibold text-sky-700 transition hover:bg-sky-50 disabled:opacity-50">
                        AI で栄養を予測
                    </button>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">カロリー（kcal）</label>
                    <input type="number" name="calories" id="calories" min="0" value="<?= htmlspecialchars($record['calories']) ?>"
                        class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                </div>

                <div class="grid gap-5 md:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">たんぱく質（g）</label>
                        <input type="number" name="protein_g" id="protein_g" min="0" step="0.1" value="<?= htmlspecialchars($record['protein_g']) ?>"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">脂質（g）</label>
                        <input type="number" name="fat_g" id="fat_g" min="0" step="0.1" value="<?= htmlspecialchars($record['fat_g']) ?>"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">炭水化物（g）</label>
                        <input type="number" name="carbohydrate_g" id="carbohydrate_g" min="0" step="0.1" value="<?= htmlspecialchars($record['carbohydrate_g']) ?>"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">メモ</label>
                    <textarea name="memo" rows="4"
                        class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"><?= htmlspecialchars($record['memo']) ?></textarea>
                </div>

                <div class="flex flex-col gap-3 pt-3 sm:flex-row">
                    <button type="submit" class="w-full rounded-lg kenko-gradient px-6 py-3 text-sm font-bold text-white shadow-md shadow-sky-200 transition hover:opacity-90">
                        登録
                    </button>
                    <a href="meal/" class="w-full rounded-lg border border-sky-200 bg-white px-6 py-3 text-center text-sm font-bold text-sky-700 transition hover:bg-sky-50">キャンセル</a>
                </div>
            </form>
        </section>
    </main>

    <!-- AI予測中モーダル -->
    <div id="ai-loading-modal" class="hidden fixed inset-0 z-50 flex flex-col items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="flex flex-col items-center gap-4 rounded-2xl bg-white px-10 py-8 shadow-2xl">
            <svg class="h-10 w-10 animate-spin text-sky-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <p class="text-sm font-semibold text-slate-700">AI が栄養成分を予測中…</p>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
    <script src="js/meal_ai.js" defer></script>
</body>

</html>