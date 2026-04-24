<?php
require_once __DIR__ . '/config.php';

const DB_HOST = 'localhost';
const DB_NAME = 'kazconnect';
const DB_USER = 'root';
const DB_PASS = '';

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    return $pdo;
}

function fetch_one(string $sql, array $params = []): ?array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row ?: null;
}

function fetch_all_rows(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function execute_query(string $sql, array $params = []): bool
{
    $stmt = db()->prepare($sql);
    return $stmt->execute($params);
}

function add_notification(int $userId, string $message, string $link = ''): void
{
    execute_query(
        'INSERT INTO notifications (user_id, message, link) VALUES (:user_id, :message, :link)',
        [
            'user_id' => $userId,
            'message' => $message,
            'link' => $link,
        ]
    );
}

function unread_notification_count(int $userId): int
{
    $result = fetch_one(
        'SELECT COUNT(*) AS total FROM notifications WHERE user_id = :user_id AND is_read = 0',
        ['user_id' => $userId]
    );

    return (int) ($result['total'] ?? 0);
}

function unread_message_count(int $userId): int
{
    $result = fetch_one(
        'SELECT COUNT(*) AS total FROM messages WHERE receiver_id = :user_id AND is_read = 0',
        ['user_id' => $userId]
    );

    return (int) ($result['total'] ?? 0);
}

function refresh_session_user(int $userId): void
{
    $user = fetch_one('SELECT * FROM users WHERE id = :id', ['id' => $userId]);
    if ($user) {
        $_SESSION['user'] = $user;
    }
}
