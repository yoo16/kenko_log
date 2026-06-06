<?php
require_once '../app.php';

use Lib\Database;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./');
    exit;
}

$posts = [
    'email' => trim($_POST['email'] ?? ''),
    'password' => $_POST['password'] ?? '',
    'csrf_token' => $_POST['csrf_token'] ?? '',
];

$_SESSION['login_old'] = [
    'email' => $posts['email'],
];

$errors = validateLoginInput($posts);
$user = null;

if (!$errors) {
    $user = findUserByEmail($posts['email']);

    // ユーザが存在しないか、パスワードが一致しない場合はエラー
    if (!$user || !password_verify($posts['password'], $user['password_hash'])) {
        $errors[] = 'メールアドレスまたはパスワードが正しくありません。';
    }
}

if ($errors) {
    $_SESSION['login_errors'] = $errors;
    header('Location: ./');
    exit;
}

$_SESSION['user'] = [
    'id' => (int) $user['id'],
    'name' => $user['name'],
    'email' => $user['email'],
];

// ログイン成功後は古い入力データをクリア
unset($_SESSION['login_old']);

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
