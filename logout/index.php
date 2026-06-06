<?php
require_once '../app.php';

// ログアウト処理
if ($_SESSION['user'] ?? null) {
    unset($_SESSION['user']);
}
// ログアウト後はトップページへリダイレクト
header("Location: " . BASE_URL);
exit;