<?php
require_once '../app.php';

use Lib\Database;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./');
    exit;
}

$posts = [
    'name' => trim($_POST['name'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'password' => $_POST['password'] ?? '',
    'password_confirmation' => $_POST['password_confirmation'] ?? '',
    'csrf_token' => $_POST['csrf_token'] ?? '',
];

$_SESSION['register_old'] = [
    'name' => $posts['name'],
    'email' => $posts['email'],
];

$errors = validateRegisterInput($posts);

if (!$errors && emailExists($posts['email'])) {
    $errors[] = 'このメールアドレスはすでに登録されています。';
}

if ($errors) {
    $_SESSION['register_errors'] = $errors;
    header('Location: ./');
    exit;
}

createUser($posts);

unset($_SESSION['register_old']);
$_SESSION['register_message'] = 'ユーザー登録が完了しました。';

header('Location: ./');
exit;

function validateRegisterInput(array $posts): array
{
    $errors = [];

    if (!hash_equals($_SESSION['csrf_token'] ?? '', $posts['csrf_token'])) {
        $errors[] = '不正なリクエストです。';
    }

    if ($posts['name'] === '') {
        $errors[] = '名前を入力してください。';
    }

    if ($posts['email'] === '' || !filter_var($posts['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = '正しいメールアドレスを入力してください。';
    }

    if (strlen($posts['password']) < 8) {
        $errors[] = 'パスワードは8文字以上で入力してください。';
    }

    if ($posts['password'] !== $posts['password_confirmation']) {
        $errors[] = 'パスワード確認が一致しません。';
    }

    return $errors;
}

function emailExists(string $email): bool
{
    $pdo = Database::getInstance();
    $sql = 'SELECT id FROM users WHERE email = :email LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);

    return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
}

function createUser(array $posts): void
{
    $pdo = Database::getInstance();
    $sql = 'INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)';
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':name' => $posts['name'],
        ':email' => $posts['email'],
        ':password_hash' => password_hash($posts['password'], PASSWORD_DEFAULT),
    ]);
}
