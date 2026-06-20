<?php
// 共通処理を読み込む
require_once '../../../app.php';

use Lib\Database;

// ログインチェック
\Lib\App::authUser();

// データベース接続
$pdo = Database::getInstance();
// TODO: SQLクエリを作成 
$sql = "";

// プリペアドステートメントを作成して実行
$stmt = $pdo->prepare($sql);
// クエリを実行
$stmt->execute([':user_id' => (int) $_SESSION['user']['id']]);
// 結果を取得
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 出力バッファを使って直接出力
$output = fopen('php://output', 'w');
// CSVファイル名
$csv_file = 'health_records_latest.csv';
// ヘッダー：ダウンロード用にCSV形式を指定
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename={$csv_file}");

// BOMを出力してExcelで文字化けしないようにする
fwrite($output, "\xEF\xBB\xBF");
// CSVのヘッダー行
fputcsv($output, ['recorded_at', 'weight', 'heart_rate', 'systolic', 'diastolic'], ',', '"', '\\');
// CSVのデータ行
foreach ($rows as $row) {
    fputcsv($output, $row, ',', '"', '\\');
}
fclose($output);
exit;
