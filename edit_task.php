<?php
require_once __DIR__ . '/../includes/db.php';
require_role(['client']);

$taskId = (int) ($_GET['id'] ?? 0);
$task = fetch_one('SELECT * FROM tasks WHERE id = :id AND client_id = :client_id', [
    'id' => $taskId,
    'client_id' => current_user()['id'],
]);

if (!$task) {
    set_flash('error', 'Task not found.');
    redirect('/tasks/my_tasks.php');
}

if ($task['status'] === 'completed') {
    set_flash('error', 'Completed tasks cannot be edited.');
    redirect('/tasks/task_details.php?id=' . $taskId);
}

$selectedCategory = in_array($task['category'], DEFAULT_TASK_CATEGORIES, true) ? $task['category'] : 'custom';
$customCategory = $selectedCategory === 'custom' ? $task['category'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedCategory = trim($_POST['category'] ?? 'general');
    $customCategory = trim($_POST['custom_category'] ?? '');
    $finalCategory = normalize_category($selectedCategory, $customCategory);

    execute_query(
        'UPDATE tasks SET title = :title, description = :description, budget = :budget, category = :category, deadline = :deadline, location = :location WHERE id = :id AND client_id = :client_id',
        [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'budget' => $_POST['budget'] ?? 0,
            'category' => $finalCategory,
            'deadline' => $_POST['deadline'] ?? null,
            'location' => trim($_POST['location'] ?? ''),
            'id' => $taskId,
            'client_id' => current_user()['id'],
        ]
    );
    set_flash('success', 'Task updated.');
    redirect('/tasks/task_details.php?id=' . $taskId);
}

$pageTitle = page_title('Edit Task');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section narrow">
    <form class="form-card validate-form" method="POST">
        <h1>Edit task</h1>
        <label>Task title
            <input type="text" name="title" value="<?= e($task['title']) ?>" required>
        </label>
        <label>Description
            <textarea name="description" rows="6" required><?= e($task['description']) ?></textarea>
        </label>
        <label>Budget (BDT)
            <input type="number" name="budget" min="1" step="0.01" value="<?= e($task['budget']) ?>" required>
        </label>
        <label>Category
            <select name="category" id="category-select">
                <?php foreach (DEFAULT_TASK_CATEGORIES as $category): ?>
                    <option value="<?= e($category) ?>" <?= $selectedCategory === $category ? 'selected' : '' ?>><?= e(ucfirst($category)) ?></option>
                <?php endforeach; ?>
                <option value="custom" <?= $selectedCategory === 'custom' ? 'selected' : '' ?>>Custom category</option>
            </select>
        </label>
        <label id="custom-category-field" <?= $selectedCategory === 'custom' ? '' : 'style="display:none;"' ?>>New category name
            <input type="text" name="custom_category" value="<?= e($customCategory) ?>" placeholder="Example: Video Editing">
        </label>
        <label>Deadline
            <input type="date" name="deadline" value="<?= e($task['deadline']) ?>" required>
        </label>
        <label>Location / Remote note
            <input type="text" name="location" value="<?= e($task['location']) ?>">
        </label>
        <button class="btn" type="submit">Save changes</button>
    </form>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
