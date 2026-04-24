<?php
require_once __DIR__ . '/../includes/db.php';
require_login();

$reviewedId = (int) ($_GET['user_id'] ?? $_POST['user_id'] ?? 0);
$taskId = (int) ($_GET['task_id'] ?? $_POST['task_id'] ?? 0);
$reviewedUser = fetch_one('SELECT id, full_name FROM users WHERE id = :id', ['id' => $reviewedId]);
$taskContext = null;

if (!$reviewedUser) {
    set_flash('error', 'User not found.');
    redirect('/index.php');
}

if ($taskId > 0) {
    $taskContext = fetch_one(
        'SELECT id, title, client_id, assigned_freelancer_id, status FROM tasks WHERE id = :id',
        ['id' => $taskId]
    );

    if (
        !$taskContext ||
        (int) $taskContext['client_id'] !== (int) current_user()['id'] ||
        (int) $taskContext['assigned_freelancer_id'] !== $reviewedId ||
        $taskContext['status'] !== 'completed'
    ) {
        set_flash('error', 'You can only review the freelancer assigned to your completed task.');
        redirect('/tasks/my_tasks.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    execute_query(
        'INSERT INTO reviews (reviewer_id, reviewed_user_id, rating, comment) VALUES (:reviewer_id, :reviewed_user_id, :rating, :comment)',
        [
            'reviewer_id' => current_user()['id'],
            'reviewed_user_id' => $reviewedId,
            'rating' => (int) ($_POST['rating'] ?? 5),
            'comment' => trim($_POST['comment'] ?? ''),
        ]
    );
    add_notification($reviewedId, 'You received a new review.', '/reviews/view_reviews.php?user_id=' . $reviewedId);
    set_flash('success', 'Review submitted.');
    if ($taskId > 0) {
        redirect('/tasks/task_details.php?id=' . $taskId);
    }
    redirect('/profile/profile.php?id=' . $reviewedId);
}

$pageTitle = page_title('Give Review');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section narrow">
    <form class="form-card validate-form" method="POST">
        <h1>Review <?= e($reviewedUser['full_name']) ?></h1>
        <?php if ($taskContext): ?>
            <p>Task completed: <strong><?= e($taskContext['title']) ?></strong></p>
        <?php endif; ?>
        <input type="hidden" name="user_id" value="<?= (int) $reviewedUser['id'] ?>">
        <input type="hidden" name="task_id" value="<?= (int) $taskId ?>">
        <label>Rating
            <select name="rating">
                <option value="5">5</option>
                <option value="4">4</option>
                <option value="3">3</option>
                <option value="2">2</option>
                <option value="1">1</option>
            </select>
        </label>
        <label>Comment
            <textarea name="comment" rows="5" required></textarea>
        </label>
        <button class="btn" type="submit">Submit review</button>
    </form>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
