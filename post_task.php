<?php
require_once __DIR__ . '/../includes/db.php';
require_role(['client']);

$selectedCategory = 'general';
$customCategory = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedCategory = trim($_POST['category'] ?? 'general');
    $customCategory = trim($_POST['custom_category'] ?? '');
    $finalCategory = normalize_category($selectedCategory, $customCategory);

    execute_query(
        "INSERT INTO tasks (client_id, title, description, budget, category, deadline, location, status)
         VALUES (:client_id, :title, :description, :budget, :category, :deadline, :location, 'open')",
        [
            'client_id' => current_user()['id'],
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'budget' => $_POST['budget'] ?? 0,
            'category' => $finalCategory,
            'deadline' => $_POST['deadline'] ?? null,
            'location' => trim($_POST['location'] ?? ''),
        ]
    );

    set_flash('success', 'Task posted successfully.');
    redirect('/tasks/my_tasks.php');
}

$pageTitle = page_title('Post Task');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section narrow">
    <form class="form-card validate-form" method="POST">
        <h1>Post a task</h1>
        <label>Task title
            <input type="text" name="title" required>
        </label>
        <label>Description
            <textarea name="description" rows="6" required></textarea>
        </label>
        <label>Budget (BDT)
            <input type="number" name="budget" min="1" step="0.01" required>
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
            <input type="date" name="deadline" required>
        </label>
        <label>Location / Remote note
            <input type="text" name="location" placeholder="Remote or city name">
        </label>
        <button class="btn" type="submit">Publish task</button>
    </form>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('category-select');
    const customCategoryField = document.getElementById('custom-category-field');

    if (!categorySelect || !customCategoryField) {
        return;
    }

    const toggleCustomCategory = function () {
        customCategoryField.style.display = categorySelect.value === 'custom' ? 'grid' : 'none';
    };

    toggleCustomCategory();
    categorySelect.addEventListener('change', toggleCustomCategory);
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
