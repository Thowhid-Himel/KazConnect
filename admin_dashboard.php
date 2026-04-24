<?php
require_once __DIR__ . '/../includes/db.php';
require_role(['admin']);

$pageTitle = page_title('Admin Dashboard');
$stats = [
    'users' => fetch_one('SELECT COUNT(*) AS total FROM users')['total'] ?? 0,
    'tasks' => fetch_one('SELECT COUNT(*) AS total FROM tasks')['total'] ?? 0,
    'messages' => fetch_one('SELECT COUNT(*) AS total FROM messages')['total'] ?? 0,
];

require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <h1>Admin dashboard</h1>
    <div class="stats-grid">
        <div class="stat-box"><strong><?= (int) $stats['users'] ?></strong><span>Total users</span></div>
        <div class="stat-box"><strong><?= (int) $stats['tasks'] ?></strong><span>Total tasks</span></div>
        <div class="stat-box"><strong><?= (int) $stats['messages'] ?></strong><span>Total messages</span></div>
    </div>
    <div class="action-row">
        <a class="btn" href="<?= e(BASE_URL) ?>/admin/manage_users.php">Manage users</a>
        <a class="btn btn-outline" href="<?= e(BASE_URL) ?>/admin/manage_tasks.php">Manage tasks</a>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
