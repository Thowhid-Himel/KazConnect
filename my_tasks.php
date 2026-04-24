<?php
require_once __DIR__ . '/../includes/db.php';
require_role(['client']);

$tasks = fetch_all_rows(
    'SELECT * FROM tasks WHERE client_id = :client_id ORDER BY created_at DESC',
    ['client_id' => current_user()['id']]
);

$pageTitle = page_title('My Tasks');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="section-head">
        <h1>My tasks</h1>
        <a class="btn" href="<?= e(BASE_URL) ?>/tasks/post_task.php">Post another task</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Title</th>
                <th>Budget</th>
                <th>Status</th>
                <th>Deadline</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= e($task['title']) ?></td>
                    <td>৳<?= e(number_format((float) $task['budget'], 2)) ?></td>
                    <td><?= e(ucfirst($task['status'])) ?></td>
                    <td><?= e($task['deadline']) ?></td>
                    <td class="action-row">
                        <a href="<?= e(BASE_URL) ?>/tasks/task_details.php?id=<?= (int) $task['id'] ?>">View</a>
                        <?php if ($task['status'] === 'completed'): ?>
                            <span class="badge">Completed</span>
                        <?php else: ?>
                            <a href="<?= e(BASE_URL) ?>/tasks/edit_task.php?id=<?= (int) $task['id'] ?>">Edit</a>
                            <a href="<?= e(BASE_URL) ?>/applications/view_applicants.php?task_id=<?= (int) $task['id'] ?>">Applicants</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
