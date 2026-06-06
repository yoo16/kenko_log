<?php
require_once '../app.php';

use Lib\Database;

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'meal/');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare('DELETE FROM meal_records WHERE id = :id AND user_id = :user_id');
    $stmt->execute([
        ':id' => $id,
        ':user_id' => (int) $_SESSION['user']['id'],
    ]);
}

header('Location: ' . BASE_URL . 'meal/');
exit;
