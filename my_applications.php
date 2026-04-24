<?php
require_once __DIR__ . '/../includes/db.php';
require_role(['freelancer']);

$applications = fetch_all_rows(
    "SELECT ta.*, t.title, t.deadline, t.status AS task_status
     FROM task_applications ta
     JOIN tasks t ON t.id = ta.task_id
     WHERE ta.freelancer_id = :freelancer_id
     ORDER BY ta.created_at DESC",
    ['freelancer_id' => current_user()['id']]
);

$pageTitle = page_title('My Applications');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <h1>My applications</h1>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Task</th>
                <th>Bid</th>
                <th>Application status</th>
                <th>Task status</th>
                <th>Deadline</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($applications as $application): ?>
                <tr>
                    <td><?= e($application['title']) ?></td>
                    <td>৳<?= e(number_format((float) $application['bid_amount'], 2)) ?></td>
                    <td><?= e(ucfirst($application['status'])) ?></td>
                    <td><?= e(ucfirst($application['task_status'])) ?></td>
                    <td><?= e($application['deadline']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
