<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Dhaka');

define('APP_NAME', 'KazConnect');
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/kazconnect');

const DEFAULT_TASK_CATEGORIES = [
    'design',
    'development',
    'writing',
    'marketing',
    'translation',
    'general',
];

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Please log in to continue.';
        redirect('/login.php');
    }
}

function require_role(array $roles): void
{
    require_login();
    $user = current_user();
    if (!in_array($user['role'], $roles, true)) {
        $_SESSION['flash_error'] = 'You do not have permission to access that page.';
        redirect('/index.php');
    }
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash_' . $type] = $message;
}

function flash_message(string $type): ?string
{
    $key = 'flash_' . $type;
    if (!isset($_SESSION[$key])) {
        return null;
    }

    $message = $_SESSION[$key];
    unset($_SESSION[$key]);
    return $message;
}

function asset_url(string $path): string
{
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

function page_title(string $title): string
{
    return $title . ' | ' . APP_NAME;
}

function dashboard_path_for_role(string $role): string
{
    return match ($role) {
        'client' => '/dashboards/client_dashboard.php',
        'freelancer' => '/dashboards/freelancer_dashboard.php',
        'admin' => '/admin/admin_dashboard.php',
        default => '/index.php',
    };
}

function normalize_category(?string $selectedCategory, ?string $customCategory = null): string
{
    $selectedCategory = trim((string) $selectedCategory);
    $customCategory = trim((string) $customCategory);

    if ($selectedCategory === 'custom' && $customCategory !== '') {
        return strtolower($customCategory);
    }

    if ($selectedCategory !== '' && $selectedCategory !== 'custom') {
        return strtolower($selectedCategory);
    }

    return 'general';
}
