<?php
require_once __DIR__ . '/../includes/db.php';
require_role(['client']);

$applicationId = (int) ($_GET['application_id'] ?? 0);
$application = fetch_one(
    "SELECT ta.*, t.client_id, t.title
     FROM task_applications ta
     JOIN tasks t ON t.id = ta.task_id
     WHERE ta.id = :id AND t.client_id = :client_id",
    [
        'id' => $applicationId,
        'client_id' => current_user()['id'],
    ]
);

if (!$application) {
    set_flash('error', 'Application not found.');
    redirect('/tasks/my_tasks.php');
}

if ($application['status'] === 'accepted') {
    set_flash('success', 'This freelancer is already assigned to the task.');
    redirect('/applications/view_applicants.php?task_id=' . $application['task_id']);
}

$existingAssignment = fetch_one(
    "SELECT assigned_freelancer_id, status FROM tasks WHERE id = :task_id AND client_id = :client_id",
    [
        'task_id' => $application['task_id'],
        'client_id' => current_user()['id'],
    ]
);

if (!$existingAssignment || $existingAssignment['status'] !== 'open') {
    set_flash('error', 'This task is no longer open for assignment.');
    redirect('/applications/view_applicants.php?task_id=' . $application['task_id']);
}

execute_query("UPDATE task_applications SET status = 'accepted' WHERE id = :id", ['id' => $applicationId]);
execute_query(
    "UPDATE task_applications SET status = 'rejected' WHERE task_id = :task_id AND id != :id",
    ['task_id' => $application['task_id'], 'id' => $applicationId]
);
execute_query(
    "UPDATE tasks SET assigned_freelancer_id = :freelancer_id, status = 'assigned' WHERE id = :task_id",
    ['freelancer_id' => $application['freelancer_id'], 'task_id' => $application['task_id']]
);

add_notification($application['freelancer_id'], 'You were assigned to task: ' . $application['title'], '/applications/my_applications.php');
set_flash('success', 'Freelancer assigned successfully.');
redirect('/applications/view_applicants.php?task_id=' . $application['task_id']);
