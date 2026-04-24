<?php
require_once __DIR__ . '/../includes/db.php';
require_role(['client']);

$taskId = (int) ($_GET['task_id'] ?? 0);
$task = fetch_one('SELECT * FROM tasks WHERE id = :id AND client_id = :client_id', [
    'id' => $taskId,
    'client_id' => current_user()['id'],
]);

if (!$task) {
    set_flash('error', 'Task not found.');
    redirect('/tasks/my_tasks.php');
}

$applicants = fetch_all_rows(
    "SELECT ta.*, u.full_name, u.email, u.skills
     FROM task_applications ta
     JOIN users u ON u.id = ta.freelancer_id
     WHERE ta.task_id = :task_id
     ORDER BY ta.created_at DESC",
    ['task_id' => $taskId]
);

$pageTitle = page_title('View Applicants');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="section-head">
        <h1>Applicants for <?= e($task['title']) ?></h1>
        <a class="text-link" href="<?= e(BASE_URL) ?>/tasks/task_details.php?id=<?= (int) $task['id'] ?>">Back to task</a>
    </div>
    <div class="card-grid">
        <?php foreach ($applicants as $applicant): ?>
            <article class="card">
                <h3><?= e($applicant['full_name']) ?></h3>
                <p><?= nl2br(e($applicant['proposal'])) ?></p>
                <div class="meta-row">
                    <span>Bid: ৳<?= e(number_format((float) $applicant['bid_amount'], 2)) ?></span>
                    <span>Status: <?= e(ucfirst($applicant['status'])) ?></span>
                </div>
                <p><strong>Skills:</strong> <?= e($applicant['skills'] ?: 'Not listed') ?></p>
                <div class="action-row">
                    <a href="<?= e(BASE_URL) ?>/profile/profile.php?id=<?= (int) $applicant['freelancer_id'] ?>">Profile</a>
                    <?php if ((int) $task['assigned_freelancer_id'] === (int) $applicant['freelancer_id'] || $applicant['status'] === 'accepted'): ?>
                        <span class="badge">Assigned</span>
                    <?php elseif ($task['status'] === 'open'): ?>
                        <a href="<?= e(BASE_URL) ?>/applications/assign_freelancer.php?application_id=<?= (int) $applicant['id'] ?>">Assign</a>
                    <?php else: ?>
                        <span class="badge"><?= e(ucfirst($task['status'])) ?></span>
                    <?php endif; ?>
                    <a href="<?= e(BASE_URL) ?>/messages/chat.php?user_id=<?= (int) $applicant['freelancer_id'] ?>">Message</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
