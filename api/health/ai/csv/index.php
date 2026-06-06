<?php
require_once '../../../../app.php';

use Lib\Database;

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

$pdo  = Database::getInstance();
$stmt = $pdo->prepare(
    'SELECT id, diagnosis_type, result, created_at
     FROM ai_diagnosis_logs
     WHERE user_id = :user_id
     ORDER BY created_at DESC'
);
$stmt->execute([':user_id' => (int) $_SESSION['user']['id']]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=ai_diagnosis_history.csv');

$output = fopen('php://output', 'w');
fwrite($output, "\xEF\xBB\xBF");
fputcsv($output, ['id', '診断種別', '診断結果', '診断日時'], ',', '"', '\\');
foreach ($rows as $row) {
    fputcsv($output, $row, ',', '"', '\\');
}
fclose($output);
exit;
