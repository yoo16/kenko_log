<?php
require_once '../app.php';

use Lib\Database;

\Lib\App::authUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'sleep/');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $pdo = Database::getInstance();
    $sql = 'DELETE FROM sleep_records WHERE id = :id AND user_id = :user_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([]);
}

header('Location: ' . BASE_URL . 'sleep/');
exit;
