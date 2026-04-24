<?php
require_once __DIR__ . '/../includes/db.php';
require_role(['admin']);

$tasks = fetch_all_rows(
    "SELECT t.*, u.full_name AS client_name
     FROM tasks t
     JOIN users u ON u.id = t.client_id
     ORDER BY t.created_at DESC"
);

$pageTitle = page_title('Manage Tasks');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <h1>Manage tasks</h1>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Task</th>
                <th>Client</th>
                <th>Status</th>
                <th>Budget</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= e($task['title']) ?></td>
                    <td><?= e($task['client_name']) ?></td>
                    <td><?= e(ucfirst($task['status'])) ?></td>
                    <td>৳<?= e(number_format((float) $task['budget'], 2)) ?></td>
                    <td>
                        <a href="<?= e(BASE_URL) ?>/tasks/task_details.php?id=<?= (int) $task['id'] ?>">View</a>
                        |
                        <a href="<?= e(BASE_URL) ?>/tasks/delete_task.php?id=<?= (int) $task['id'] ?>" onclick="return confirm('Delete this task?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
