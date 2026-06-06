<?php
require_once '../app.php';

if ($_SESSION['user'] ?? null) {
    unset($_SESSION['user']);
}

header("Location: /");