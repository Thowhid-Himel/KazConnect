<?php
require_once __DIR__ . '/../includes/db.php';
require_role(['freelancer']);

$user = current_user();
$pageTitle = page_title('Freelancer Dashboard');
$applicationCount = fetch_one('SELECT COUNT(*) AS total FROM task_applications WHERE freelancer_id = :id', ['id' => $user['id']])['total'] ?? 0;
$acceptedCount = fetch_one("SELECT COUNT(*) AS total FROM task_applications WHERE freelancer_id = :id AND status = 'accepted'", ['id' => $user['id']])['total'] ?? 0;
$assignedTasks = fetch_all_rows(
    "SELECT t.*, u.full_name AS client_name
     FROM tasks t
     JOIN users u ON u.id = t.client_id
     WHERE t.assigned_freelancer_id = :id AND t.status IN ('assigned', 'completed')
     ORDER BY CASE WHEN t.status = 'assigned' THEN 0 ELSE 1 END, t.created_at DESC
     LIMIT 6",
    ['id' => $user['id']]
);
$openTasks = fetch_all_rows(
    "SELECT t.*, u.full_name AS client_name
     FROM tasks t
     JOIN users u ON u.id = t.client_id
     WHERE t.status = 'open'
     ORDER BY t.created_at DESC
     LIMIT 6"
);

require_once __DIR__ . '/../includes/header.php';
?>
<section class="dashboard-header">
    <div>
        <p class="eyebrow">Freelancer workspace</p>
        <h1>Welcome, <?= e($user['full_name']) ?></h1>
    </div>
    <a class="btn btn-outline" href="<?= e(BASE_URL) ?>/applications/my_applications.php">My applications</a>
</section>

<section class="stats-grid">
    <div class="stat-box"><strong><?= (int) $applicationCount ?></strong><span>Applications sent</span></div>
    <div class="stat-box"><strong><?= (int) $acceptedCount ?></strong><span>Accepted offers</span></div>
    <div class="stat-box"><strong><?= (int) count($assignedTasks) ?></strong><span>Assigned tasks</span></div>
</section>

<section class="section">
    <div class="section-head">
        <h2>Your assigned work</h2>
        <a class="text-link" href="<?= e(BASE_URL) ?>/applications/my_applications.php">See applications</a>
    </div>
    <div class="card-grid">
        <?php if ($assignedTasks): ?>
            <?php foreach ($assignedTasks as $task): ?>
                <article class="card">
                    <div class="card-top">
                        <span class="badge"><?= e(ucfirst($task['status'])) ?></span>
                        <span class="price-tag">৳<?= e(number_format((float) $task['budget'], 2)) ?></span>
                    </div>
                    <h3><?= e($task['title']) ?></h3>
                    <p><?= e(mb_strimwidth($task['description'], 0, 120, '...')) ?></p>
                    <div class="meta-row">
                        <span><?= e($task['client_name']) ?></span>
                        <span><?= e($task['deadline']) ?></span>
                    </div>
                    <div class="action-row">
                        <a class="btn btn-small" href="<?= e(BASE_URL) ?>/tasks/task_details.php?id=<?= (int) $task['id'] ?>">View task</a>
                        <a class="btn btn-small btn-outline" href="<?= e(BASE_URL) ?>/messages/chat.php?user_id=<?= (int) $task['client_id'] ?>">Message client</a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <article class="card">
                <h3>No assigned tasks yet</h3>
                <p>When a client assigns you a task, it will appear here with quick links to view the details and message the client.</p>
            </article>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="section-head">
        <h2>Open tasks for you</h2>
        <a class="text-link" href="<?= e(BASE_URL) ?>/tasks/browse_tasks.php">Browse all</a>
    </div>
    <div class="card-grid">
        <?php foreach ($openTasks as $task): ?>
            <article class="card">
                <div class="card-top">
                    <span class="badge"><?= e(ucfirst($task['category'])) ?></span>
                    <span class="price-tag">৳<?= e(number_format((float) $task['budget'], 2)) ?></span>
                </div>
                <h3><?= e($task['title']) ?></h3>
                <p><?= e(mb_strimwidth($task['description'], 0, 120, '...')) ?></p>
                <div class="meta-row">
                    <span><?= e($task['client_name']) ?></span>
                    <span><?= e($task['deadline']) ?></span>
                </div>
                <a class="btn btn-small" href="<?= e(BASE_URL) ?>/tasks/task_details.php?id=<?= (int) $task['id'] ?>">View Task</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
