<?php
require_once '../app.php';

use Lib\Database;

if (empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'login/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'meal/add.php');
    exit;
}

$posts = normalizeMealPosts($_POST);
$_SESSION['meal_form'] = $posts;

$message = validateMealPosts($posts);
if ($message !== '') {
    $_SESSION['meal_message'] = $message;
    header('Location: ' . BASE_URL . 'meal/add.php');
    exit;
}

insertMeal((int) $_SESSION['user']['id'], $posts);
unset($_SESSION['meal_form']);

header('Location: ' . BASE_URL . 'meal/');
exit;

function normalizeMealPosts(array $posts): array
{
    return [
        'meal_date' => trim($posts['meal_date'] ?? ''),
        'meal_type' => trim($posts['meal_type'] ?? ''),
        'food_name' => trim($posts['food_name'] ?? ''),
        'calories' => trim($posts['calories'] ?? ''),
        'protein_g' => trim($posts['protein_g'] ?? ''),
        'fat_g' => trim($posts['fat_g'] ?? ''),
        'carbohydrate_g' => trim($posts['carbohydrate_g'] ?? ''),
        'memo' => trim($posts['memo'] ?? ''),
    ];
}

function validateMealPosts(array $posts): string
{
    if ($posts['meal_date'] === '') {
        return '食事日を入力してください。';
    }
    if (!in_array($posts['meal_type'], ['breakfast', 'lunch', 'dinner', 'snack'], true)) {
        return '食事の種類を選択してください。';
    }
    if ($posts['food_name'] === '') {
        return 'メニューを入力してください。';
    }

    return '';
}

function insertMeal(int $userId, array $posts): void
{
    $pdo = Database::getInstance();
    $sql = 'INSERT INTO meal_records (
                user_id,
                meal_date,
                meal_type,
                food_name,
                calories,
                protein_g,
                fat_g,
                carbohydrate_g,
                memo
            ) VALUES (
                :user_id,
                :meal_date,
                :meal_type,
                :food_name,
                :calories,
                :protein_g,
                :fat_g,
                :carbohydrate_g,
                :memo
            )';
    // プリペアドステートメントを作成
    $stmt = $pdo->prepare($sql);
    // バインドするデータを準備
    $data = [
        ':user_id' => $userId,
        ':meal_date' => $posts['meal_date'],
        ':meal_type' => $posts['meal_type'],
        ':food_name' => $posts['food_name'],
        ':calories' => $posts['calories'] === '' ? null : (int) $posts['calories'],
        ':protein_g' => $posts['protein_g'] === '' ? null : $posts['protein_g'],
        ':fat_g' => $posts['fat_g'] === '' ? null : $posts['fat_g'],
        ':carbohydrate_g' => $posts['carbohydrate_g'] === '' ? null : $posts['carbohydrate_g'],
        ':memo' => $posts['memo'] === '' ? null : $posts['memo'],
    ];
    // SQLを実行
    $stmt->execute($data);
}
