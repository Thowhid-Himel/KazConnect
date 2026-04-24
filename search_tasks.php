<?php
require_once __DIR__ . '/../includes/db.php';

$term = trim($_GET['q'] ?? '');
$tasks = [];

if ($term !== '') {
    $tasks = fetch_all_rows(
        "SELECT t.*, u.full_name AS client_name
         FROM tasks t
         JOIN users u ON u.id = t.client_id
         WHERE t.title LIKE :term OR t.description LIKE :term OR t.category LIKE :term
         ORDER BY t.created_at DESC",
        ['term' => '%' . $term . '%']
    );
}

$pageTitle = page_title('Search Tasks');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <h1>Search tasks</h1>
    <form class="filter-form search-bar" method="GET">
        <input type="text" name="q" value="<?= e($term) ?>" placeholder="Search by title, skill, or category">
        <button class="btn" type="submit">Search</button>
    </form>
    <?php if ($term !== ''): ?>
        <div class="card-grid">
            <?php foreach ($tasks as $task): ?>
                <article class="card">
                    <div class="card-top">
                        <span class="badge"><?= e(ucfirst($task['category'])) ?></span>
                        <span class="price-tag">৳<?= e(number_format((float) $task['budget'], 2)) ?></span>
                    </div>
                    <h3><?= e($task['title']) ?></h3>
                    <p><?= e(mb_strimwidth($task['description'], 0, 120, '...')) ?></p>
                    <div class="meta-row">
                        <span><?= e($task['client_name']) ?></span>
                        <span><?= e($task['deadline']) ?></span>
                    </div>
                    <a class="btn btn-small" href="<?= e(BASE_URL) ?>/tasks/task_details.php?id=<?= (int) $task['id'] ?>">View</a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
