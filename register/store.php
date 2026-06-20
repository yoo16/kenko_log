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
$_SESSION['register_old']['name'] = $posts['name'] ?? '';
$_SESSION['register_old']['email'] = $posts['email'] ?? '';

// バリデーション
$errors = validateRegisterInput($posts);

if (!$errors && findUserByEmail($posts['email'])) {
    $errors[] = 'このメールアドレスはすでに登録されています。';
}

// エラーがある場合はセッションに保存してリダイレクト
if ($errors) {
    $_SESSION['register_errors'] = $errors;
    header('Location: ./input.php');
    exit;
}

// ユーザーを作成
createUser($posts);

unset($_SESSION['register_old']);
$_SESSION['register_message'] = 'ユーザー登録が完了しました。';

// ログインページにリダイレクト
header('Location: ../login');
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

function findUserByEmail(string $email): ?array
{
    // データベース接続
    $pdo = Database::getInstance();
    // SQL文
    $sql = "SELECT * FROM users 
            WHERE email = :email 
            LIMIT 1";
    // プリペアドステートメント
    $stmt = $pdo->prepare($sql);
    // SQLの実行
    $stmt->execute([':email' => $email]);
    // ユーザを取得
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}

function createUser(array $posts): void
{
    // TODO: パスワードをハッシュ化して保存する
    $posts['password_hash'] = "";

    $pdo = Database::getInstance();
    $sql = 'INSERT INTO users (name, email, password_hash) 
                VALUES (:name, :email, :password_hash)';
    // プリペアドステートメント
    $stmt = $pdo->prepare($sql);
    // SQLの実行
    $stmt->execute([
        ':name' => $posts['name'],
        ':email' => $posts['email'],
        ':password_hash' => $posts['password_hash'],
    ]);
}
