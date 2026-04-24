<?php
require_once __DIR__ . '/../includes/db.php';
require_role(['client']);

$user = current_user();
$pageTitle = page_title('Client Dashboard');
$taskCount = fetch_one('SELECT COUNT(*) AS total FROM tasks WHERE client_id = :id', ['id' => $user['id']])['total'] ?? 0;
$openCount = fetch_one("SELECT COUNT(*) AS total FROM tasks WHERE client_id = :id AND status = 'open'", ['id' => $user['id']])['total'] ?? 0;
$assignedCount = fetch_one("SELECT COUNT(*) AS total FROM tasks WHERE client_id = :id AND status = 'assigned'", ['id' => $user['id']])['total'] ?? 0;
$recentTasks = fetch_all_rows(
    'SELECT * FROM tasks WHERE client_id = :id ORDER BY created_at DESC LIMIT 5',
    ['id' => $user['id']]
);

require_once __DIR__ . '/../includes/header.php';
?>
<section class="dashboard-header">
    <div>
        <p class="eyebrow">Client workspace</p>
        <h1>Welcome, <?= e($user['full_name']) ?></h1>
    </div>
    <a class="btn" href="<?= e(BASE_URL) ?>/tasks/post_task.php">Post a new task</a>
</section>

<section class="stats-grid">
    <div class="stat-box"><strong><?= (int) $taskCount ?></strong><span>Total tasks</span></div>
    <div class="stat-box"><strong><?= (int) $openCount ?></strong><span>Open tasks</span></div>
    <div class="stat-box"><strong><?= (int) $assignedCount ?></strong><span>Assigned tasks</span></div>
</section>

<section class="section">
    <div class="section-head">
        <h2>Your recent tasks</h2>
        <a class="text-link" href="<?= e(BASE_URL) ?>/tasks/my_tasks.php">Manage all</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Budget</th>
                <th>Deadline</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($recentTasks as $task): ?>
                <tr>
                    <td><?= e($task['title']) ?></td>
                    <td><span class="badge"><?= e(ucfirst($task['status'])) ?></span></td>
                    <td>৳<?= e(number_format((float) $task['budget'], 2)) ?></td>
                    <td><?= e($task['deadline']) ?></td>
                    <td><a href="<?= e(BASE_URL) ?>/tasks/task_details.php?id=<?= (int) $task['id'] ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
