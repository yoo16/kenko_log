<?php
require_once '../app.php';

use Lib\Database;
use Lib\App;

// POSTリクエスト以外は受け付けない
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

// POSTデータのサニタイズ
$posts = App::sanitize($_POST);
// 古い入力データをセッションに保存
$_SESSION['login_old'] = $posts;
// パスワードは古い入力値としてセッションに保存しない
unset($_SESSION['login_old']['password']);

// バリデーション
$errors = validateLoginInput($posts);

// 認証
$user = null;
if (!$errors) {
    $user = auth($posts['email'], $posts['password']);
    if (!$user) {
        $errors[] = 'メールアドレスまたはパスワードが正しくありません。';
    }
}

// エラーがある場合はセッションに保存してリダイレクト
if ($errors) {
    $_SESSION['login_errors'] = $errors;
    header('Location: ./');
    exit;
}

// パスワードハッシュはセッションに保存しない
unset($_SESSION['user']['password_hash']);
// ログイン成功後は古い入力データをクリア
unset($_SESSION['login_old']);

// ログイン成功後はダッシュボードにリダイレクト
header('Location: ' . BASE_URL . 'dashboard/');
exit;

function validateLoginInput(array $posts): array
{
    $errors = [];

    if (!hash_equals($_SESSION['csrf_token'] ?? '', $posts['csrf_token'])) {
        $errors[] = '不正なリクエストです。';
    }

    if ($posts['email'] === '' || !filter_var($posts['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = '正しいメールアドレスを入力してください。';
    }

    if ($posts['password'] === '') {
        $errors[] = 'パスワードを入力してください。';
    }

    return $errors;
}

function findUserByEmail(string $email): ?array
{
    $pdo = Database::getInstance();
    $sql = "SELECT id, name, email, password_hash FROM users WHERE email = :email LIMIT 1";
    // プリペアドステートメント
    $stmt = $pdo->prepare($sql);
    // SQLの実行
    $stmt->execute([':email' => $email]);
    // ユーザを取得
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}

function auth(string $email, string $password)
{
    // ユーザーをメールアドレスで検索
    $user = findUserByEmail($email);
    // ユーザがいて、かつパスワードが一致する場合はログイン成功
    if ($user && password_verify($password, $user['password_hash'])) {
        // セッションにユーザー情報を保存する
        $_SESSION['user'] = $user;
        return $user;
    }
    return null;
}
