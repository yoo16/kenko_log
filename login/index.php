<?php
require_once '../app.php';

// エラーメッセージをセッションから取得
$errors = $_SESSION['login_errors'] ?? [];
// 成功メッセージをセッションから取得
$message = $_SESSION['login_message'] ?? '';
// 古い入力値を取得（なければ空の配列）
$old = $_SESSION['login_old'] ?? [ 'email' => '', ];

// セッションのエラーメッセージ、成功メッセージ、古い入力値をクリア
unset($_SESSION['login_errors'], $_SESSION['login_message'], $_SESSION['login_old']);

// CSRFトークンを生成してセッションに保存（なければ）
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="ja">

<?php include '../components/head.php'; ?>

<body class="bg-slate-50 text-slate-800">
    <?php include '../components/nav.php'; ?>

    <main class="min-h-[calc(100vh-160px)] px-6 py-14">
        <section class="mx-auto grid max-w-5xl overflow-hidden rounded-2xl border border-sky-100 bg-white shadow-xl shadow-sky-100/70 md:grid-cols-[0.95fr_1.05fr]">
            <div class="bg-gradient-to-br from-sky-700 to-blue-500 p-8 text-white md:p-10">
                <div class="flex h-full flex-col justify-between gap-10">
                    <div>
                        <p class="mb-4 text-sm font-semibold text-sky-100">KENKO LOG</p>
                        <h1 class="text-3xl font-bold leading-tight md:text-4xl">
                            今日の記録を、続きから。
                        </h1>
                        <p class="mt-5 text-sm leading-7 text-sky-50">
                            ログインすると、健康・運動・睡眠・食事の記録をまとめて確認できます。
                        </p>
                    </div>

                    <div class="rounded-xl bg-white/15 p-5 text-sm leading-7 backdrop-blur">
                        毎日の記録を積み重ねて、自分の体調リズムを見つけましょう。
                    </div>
                </div>
            </div>

            <div class="p-8 md:p-10">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-slate-900">ログイン</h2>
                </div>

                <?php if ($message): ?>
                    <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                    <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <ul class="space-y-1">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="login/auth.php" method="post" class="space-y-5">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">メールアドレス</label>
                        <input id="email" type="email" name="email" required
                            value="<?= htmlspecialchars($old['email']) ?>"
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">パスワード</label>
                        <input id="password" type="password" name="password" required
                            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                    </div>

                    <div class="mt-2 text-sm text-slate-500">
                        <a id="auto-input-user" class="px-4 py-2 text-sky-600 border border-sky-200 rounded-xl">テストユーザーを入力</a>
                    </div>

                    <div class="flex flex-col gap-3 pt-3 sm:flex-row">
                        <button type="submit" class="w-full rounded-lg kenko-gradient px-6 py-3 text-sm font-bold text-white shadow-md shadow-sky-200 transition hover:opacity-90">
                            ログイン
                        </button>
                        <a href="register/" class="w-full rounded-lg border border-sky-200 bg-white px-6 py-3 text-center text-sm font-bold text-sky-700 transition hover:bg-sky-50">
                            新規登録
                        </a>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <?php include '../components/footer.php'; ?>

    <script src="js/test_user.js"></script>
</body>

</html>
