<?php
require_once __DIR__ . '/../includes/db.php';
require_role(['client']);

$taskId = (int) ($_GET['id'] ?? 0);
$task = fetch_one(
    'SELECT id, client_id, assigned_freelancer_id, title, status FROM tasks WHERE id = :id AND client_id = :client_id',
    [
        'id' => $taskId,
        'client_id' => current_user()['id'],
    ]
);

if (!$task) {
    set_flash('error', 'Task not found.');
    redirect('/tasks/my_tasks.php');
}

if ($task['status'] !== 'assigned' || empty($task['assigned_freelancer_id'])) {
    set_flash('error', 'Only assigned tasks can be marked as completed.');
    redirect('/tasks/task_details.php?id=' . $taskId);
}

execute_query(
    "UPDATE tasks SET status = 'completed' WHERE id = :id AND client_id = :client_id",
    [
        'id' => $taskId,
        'client_id' => current_user()['id'],
    ]
);

add_notification(
    (int) $task['assigned_freelancer_id'],
    'Your assigned task was marked as completed: ' . $task['title'],
    '/tasks/task_details.php?id=' . $taskId
);

set_flash('success', 'Task marked as completed.');
redirect('/reviews/give_review.php?user_id=' . (int) $task['assigned_freelancer_id'] . '&task_id=' . $taskId);
