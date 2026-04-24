<?php
require_once __DIR__ . '/../includes/db.php';
require_role(['freelancer']);

$taskId = (int) ($_GET['task_id'] ?? $_POST['task_id'] ?? 0);
$task = fetch_one('SELECT * FROM tasks WHERE id = :id AND status = :status', ['id' => $taskId, 'status' => 'open']);

if (!$task) {
    set_flash('error', 'This task is not available for applications.');
    redirect('/tasks/browse_tasks.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exists = fetch_one(
        'SELECT id FROM task_applications WHERE task_id = :task_id AND freelancer_id = :freelancer_id',
        ['task_id' => $taskId, 'freelancer_id' => current_user()['id']]
    );

    if ($exists) {
        set_flash('error', 'You already applied for this task.');
    } else {
        execute_query(
            "INSERT INTO task_applications (task_id, freelancer_id, proposal, bid_amount, status)
             VALUES (:task_id, :freelancer_id, :proposal, :bid_amount, 'pending')",
            [
                'task_id' => $taskId,
                'freelancer_id' => current_user()['id'],
                'proposal' => trim($_POST['proposal'] ?? ''),
                'bid_amount' => $_POST['bid_amount'] ?? 0,
            ]
        );

        add_notification($task['client_id'], 'A freelancer applied to your task: ' . $task['title'], '/applications/view_applicants.php?task_id=' . $taskId);
        set_flash('success', 'Application submitted.');
        redirect('/applications/my_applications.php');
    }
}

$pageTitle = page_title('Apply to Task');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section narrow">
    <form class="form-card validate-form" method="POST">
        <h1>Apply to: <?= e($task['title']) ?></h1>
        <input type="hidden" name="task_id" value="<?= (int) $task['id'] ?>">
        <label>Your proposal
            <textarea name="proposal" rows="6" required></textarea>
        </label>
        <label>Your bid (BDT)
            <input type="number" name="bid_amount" min="1" step="0.01" required>
        </label>
        <button class="btn" type="submit">Send application</button>
    </form>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
