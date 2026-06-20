<?php

namespace Lib;

use Throwable;

class App
{
    public static function boot(): void
    {
        self::defineBaseUrl();
        self::defineBaseDir();
        self::startSession();
    }

    public static function baseUrl(): string
    {
        return BASE_URL;
    }

    public static function baseDir(): string
    {
        return BASE_DIR;
    }

    public static function sanitize(array | string $posts)
    {
        if (is_array($posts)) {
            return array_map([self::class, 'sanitize'], $posts);
        }
        return htmlspecialchars(trim((string) $posts), ENT_QUOTES, 'UTF-8');
    }


    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            session_regenerate_id(true);
        }
    }

    private static function checkDatabaseConnection(): bool
    {
        try {
            Database::getInstance();
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    private static function isDatabaseSetupPage(): bool
    {
        return basename($_SERVER['SCRIPT_NAME'] ?? '') === 'create_database.php';
    }

    private static function defineBaseUrl(): void
    {
        if (defined('BASE_URL')) {
            return;
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $scriptFile = str_replace('\\', '/', realpath($_SERVER['SCRIPT_FILENAME'] ?? '') ?: '');
        $appRoot = str_replace('\\', '/', realpath(dirname(__DIR__)) ?: dirname(__DIR__));

        $relativeScriptDir = '';
        if ($scriptFile !== '' && str_starts_with($scriptFile, $appRoot)) {
            $relativeScriptPath = ltrim(substr($scriptFile, strlen($appRoot)), '/');
            $relativeScriptDir = trim(dirname($relativeScriptPath), '.');
        }

        $publicDir = trim(dirname($scriptName), '/');
        if ($relativeScriptDir !== '') {
            $relativeScriptDir = trim($relativeScriptDir, '/');
            if ($publicDir === $relativeScriptDir) {
                $publicDir = '';
            } else {
                $publicDir = preg_replace(
                    '#/' . preg_quote($relativeScriptDir, '#') . '$#',
                    '',
                    $publicDir
                );
            }
        }

        $baseUrl = $scheme . '://' . $host . '/';
        if ($publicDir !== '') {
            $baseUrl .= trim($publicDir, '/') . '/';
        }

        define('BASE_URL', $baseUrl);
    }

    private static function defineBaseDir(): void
    {
        if (defined('BASE_DIR')) {
            return;
        }
        define('BASE_DIR', '/var/www/html/hal/2026/PH31/kenko_log/');
    }

    public static function isAuthenticated(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function authUser(): void
    {
        if (!self::isAuthenticated()) {
            header('Location: ' . BASE_URL . 'login/');
            exit;
        }
    }
}
