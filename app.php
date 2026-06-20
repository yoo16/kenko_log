<?php
// 環境設定を読み込む
require_once 'env.php';
// アプリケーション共通処理を読み込む
require_once 'lib/App.php';
// アプリケーションのベースURLを定義
\Lib\App::boot();


// AIサービスを読み込む
require_once 'services/GeminiService.php';
// データベース接続を読み込む
require_once 'lib/Database.php';

// サイトタイトル
const SITE_TITLE = 'KENKO LOG';

$auth_user = $_SESSION['user'] ?? null;
