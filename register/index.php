<?php
require_once '../app.php';

if (isset($_SESSION['register_old'])) unset($_SESSION['register_old']);
if (isset($_SESSION['register_errors'])) unset($_SESSION['register_errors']);
if (isset($_SESSION['register_message'])) unset($_SESSION['register_message']);

header('Location: input.php');