<?php
require_once __DIR__ . '/../includes/db.php';

$category = trim($_GET['category'] ?? '');
$categories = array_unique(array_merge(
    DEFAULT_TASK_CATEGORIES,
    array_column(fetch_all_rows("SELECT DISTINCT category FROM tasks ORDER BY category ASC"), 'category')
));
sort($categories);
$sql = "SELECT t.*, u.full_name AS client_name
        FROM tasks t
        JOIN users u ON u.id = t.client_id
        WHERE t.status = 'open'";
$params = [];

if ($category !== '') {
    $sql .= ' AND t.category = :category';
    $params['category'] = $category;
}

$sql .= ' ORDER BY t.created_at DESC';
$tasks = fetch_all_rows($sql, $params);

$pageTitle = page_title('Browse Tasks');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="section-head">
        <div>
            <p class="eyebrow">Marketplace</p>
            <h1>Browse open tasks</h1>
        </div>
        <form class="filter-form" method="GET">
            <select name="category">
                <option value="">All categories</option>
                <?php foreach ($categories as $option): ?>
                    <option value="<?= e($option) ?>" <?= $category === $option ? 'selected' : '' ?>><?= e(ucfirst($option)) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-small" type="submit">Filter</button>
        </form>
    </div>
    <div class="card-grid">
        <?php foreach ($tasks as $task): ?>
            <article class="card">
                <div class="card-top">
                    <span class="badge"><?= e(ucfirst($task['category'])) ?></span>
                    <span class="price-tag">৳<?= e(number_format((float) $task['budget'], 2)) ?></span>
                </div>
                <h3><?= e($task['title']) ?></h3>
                <p><?= e(mb_strimwidth($task['description'], 0, 150, '...')) ?></p>
                <div class="meta-row">
                    <span><?= e($task['client_name']) ?></span>
                    <span><?= e($task['deadline']) ?></span>
                </div>
                <a class="btn btn-small" href="<?= e(BASE_URL) ?>/tasks/task_details.php?id=<?= (int) $task['id'] ?>">View details</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
