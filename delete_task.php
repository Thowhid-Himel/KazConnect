<?php
require_once __DIR__ . '/../includes/db.php';
require_role(['client', 'admin']);

$taskId = (int) ($_GET['id'] ?? 0);
$user = current_user();

if ($user['role'] === 'admin') {
    execute_query('DELETE FROM tasks WHERE id = :id', ['id' => $taskId]);
} else {
    $task = fetch_one('SELECT id, status FROM tasks WHERE id = :id AND client_id = :client_id', ['id' => $taskId, 'client_id' => $user['id']]);
    if (!$task) {
        set_flash('error', 'Task not found.');
        redirect('/tasks/my_tasks.php');
    }

    if ($task['status'] === 'completed') {
        set_flash('error', 'Completed tasks cannot be deleted.');
        redirect('/tasks/task_details.php?id=' . $taskId);
    }

    execute_query('DELETE FROM tasks WHERE id = :id AND client_id = :client_id', ['id' => $taskId, 'client_id' => $user['id']]);
}

set_flash('success', 'Task deleted.');
redirect($user['role'] === 'admin' ? '/admin/manage_tasks.php' : '/tasks/my_tasks.php');
