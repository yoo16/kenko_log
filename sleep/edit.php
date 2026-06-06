<?php
require_once '../app.php';

use Lib\Database;

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

$id     = (int) ($_GET['id'] ?? 0);
$record = findSleep($id, (int) $_SESSION['user']['id']);
$message = $record ? '' : '該当する記録が見つかりません。';

if (isset($_SESSION['sleep_message'])) {
    $message = $_SESSION['sleep_message'];
    unset($_SESSION['sleep_message']);
}

if (isset($_SESSION['sleep_form']) && $record) {
    $record = array_merge($record, $_SESSION['sleep_form']);
    unset($_SESSION['sleep_form']);
}

function findSleep(int $id, int $userId): ?array
{
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare('SELECT * FROM sleep_records WHERE id = :id AND user_id = :user_id LIMIT 1');
    $stmt->execute([':id' => $id, ':user_id' => $userId]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    return $record ?: null;
}

// datetime-local 用に "YYYY-MM-DD HH:MM:SS" → "YYYY-MM-DDTHH:MM" に変換
function toDatetimeLocal(?string $dt): string
{
    if ($dt === null) return '';
    return substr($dt, 0, 16) === substr($dt, 0, 16) ? substr(str_replace(' ', 'T', $dt), 0, 16) : '';
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
                <p class="text-sm font-semibold text-sky-600">Edit Sleep</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">睡眠記録を編集</h1>
                <p class="mt-3 text-sm leading-7 text-slate-500">睡眠記録の内容を更新できます。</p>
            </div>

            <?php if ($message): ?>
                <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($record): ?>
                <?php
                    $bedtimeLocal  = isset($record['bedtime'])   ? toDatetimeLocal($record['bedtime'])   : '';
                    $wakeTimeLocal = isset($record['wake_time'])  ? toDatetimeLocal($record['wake_time'])  : '';
                ?>
                <form action="sleep/update.php" method="post" class="space-y-6">
                    <input type="hidden" name="id" value="<?= (int) $record['id'] ?>">

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">睡眠日</label>
                        <input type="date" name="sleep_date" required value="<?= htmlspecialchars($record['sleep_date']) ?>"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">就寝時刻</label>
                            <input type="datetime-local" name="bedtime" required value="<?= htmlspecialchars($bedtimeLocal) ?>"
                                class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">起床時刻</label>
                            <input type="datetime-local" name="wake_time" required value="<?= htmlspecialchars($wakeTimeLocal) ?>"
                                class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">睡眠の質（任意）</label>
                        <select name="sleep_quality"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                            <option value="">未選択</option>
                            <?php foreach (range(1, 5) as $q): ?>
                                <option value="<?= $q ?>" <?= (string) ($record['sleep_quality'] ?? '') === (string) $q ? 'selected' : '' ?>>
                                    <?= $q ?> <?= str_repeat('★', $q) . str_repeat('☆', 5 - $q) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">メモ（任意）</label>
                        <textarea name="memo" rows="4"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"><?= htmlspecialchars($record['memo'] ?? '') ?></textarea>
                    </div>

                    <div class="flex flex-col gap-3 pt-3 sm:flex-row">
                        <button type="submit" class="w-full rounded-lg kenko-gradient px-6 py-3 text-sm font-bold text-white shadow-md shadow-sky-200 transition hover:opacity-90">
                            更新
                        </button>
                        <a href="sleep/" class="w-full rounded-lg border border-sky-200 bg-white px-6 py-3 text-center text-sm font-bold text-sky-700 transition hover:bg-sky-50">キャンセル</a>
                    </div>
                </form>

                <form action="sleep/delete.php" method="post" onsubmit="return confirm('この記録を削除してもよろしいですか？');" class="mt-5 text-right">
                    <input type="hidden" name="id" value="<?= (int) $record['id'] ?>">
                    <button type="submit" class="rounded-lg border border-rose-200 bg-white px-6 py-3 text-sm font-bold text-rose-600 transition hover:bg-rose-50">
                        削除
                    </button>
                </form>
            <?php endif; ?>
        </section>
    </main>

    <?php include '../components/footer.php'; ?>
</body>

</html>
