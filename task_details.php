<?php
require_once __DIR__ . '/../includes/db.php';

$taskId = (int) ($_GET['id'] ?? 0);
$task = fetch_one(
    "SELECT t.*, u.full_name AS client_name, u.email AS client_email
     FROM tasks t
     JOIN users u ON u.id = t.client_id
     WHERE t.id = :id",
    ['id' => $taskId]
);

if (!$task) {
    set_flash('error', 'Task not found.');
    redirect('/tasks/browse_tasks.php');
}

$applications = fetch_all_rows(
    "SELECT ta.*, u.full_name, u.skills
     FROM task_applications ta
     JOIN users u ON u.id = ta.freelancer_id
     WHERE ta.task_id = :task_id
     ORDER BY ta.created_at DESC",
    ['task_id' => $taskId]
);

$user = current_user();
$hasApplied = false;
if ($user && $user['role'] === 'freelancer') {
    $hasApplied = (bool) fetch_one(
        'SELECT id FROM task_applications WHERE task_id = :task_id AND freelancer_id = :freelancer_id',
        ['task_id' => $taskId, 'freelancer_id' => $user['id']]
    );
}

$pageTitle = page_title('Task Details');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="detail-layout">
        <article class="detail-card">
            <div class="card-top">
                <span class="badge"><?= e(ucfirst($task['category'])) ?></span>
                <span class="price-tag">৳<?= e(number_format((float) $task['budget'], 2)) ?></span>
            </div>
            <h1><?= e($task['title']) ?></h1>
            <p><?= nl2br(e($task['description'])) ?></p>
            <div class="detail-meta">
                <div><strong>Client</strong><span><?= e($task['client_name']) ?></span></div>
                <div><strong>Status</strong><span><?= e(ucfirst($task['status'])) ?></span></div>
                <div><strong>Deadline</strong><span><?= e($task['deadline']) ?></span></div>
                <div><strong>Location</strong><span><?= e($task['location'] ?: 'Remote') ?></span></div>
            </div>
            <?php if ($user && $user['role'] === 'freelancer' && $task['status'] === 'open'): ?>
                <?php if ($hasApplied): ?>
                    <p class="helper-text">You already applied to this task.</p>
                <?php else: ?>
                    <a class="btn" href="<?= e(BASE_URL) ?>/applications/apply_task.php?task_id=<?= (int) $task['id'] ?>">Apply now</a>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($user && $user['role'] === 'client' && (int) $user['id'] === (int) $task['client_id']): ?>
                <div class="action-row">
                    <a class="btn btn-small" href="<?= e(BASE_URL) ?>/tasks/task_details.php?id=<?= (int) $task['id'] ?>">View details</a>
                    <?php if ($task['status'] === 'completed'): ?>
                        <span class="badge">Completed</span>
                    <?php else: ?>
                        <a class="btn btn-small btn-outline" href="<?= e(BASE_URL) ?>/tasks/edit_task.php?id=<?= (int) $task['id'] ?>">Edit</a>
                        <a class="btn btn-small btn-danger" href="<?= e(BASE_URL) ?>/tasks/delete_task.php?id=<?= (int) $task['id'] ?>" onclick="return confirm('Delete this task?');">Delete</a>
                        <a class="btn btn-small" href="<?= e(BASE_URL) ?>/applications/view_applicants.php?task_id=<?= (int) $task['id'] ?>">View applicants</a>
                    <?php endif; ?>
                    <?php if ($task['status'] === 'assigned' && !empty($task['assigned_freelancer_id'])): ?>
                        <a class="btn btn-small" href="<?= e(BASE_URL) ?>/tasks/mark_complete.php?id=<?= (int) $task['id'] ?>" onclick="return confirm('Mark this assigned task as completed?');">Mark as completed</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </article>

        <aside class="sidebar-card">
            <h3>Applications</h3>
            <p><?= count($applications) ?> freelancer(s) applied.</p>
            <?php foreach (array_slice($applications, 0, 4) as $application): ?>
                <div class="mini-user">
                    <strong><?= e($application['full_name']) ?></strong>
                    <span><?= e($application['skills'] ?: 'No skills listed') ?></span>
                </div>
            <?php endforeach; ?>
        </aside>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
