<?php
require_once __DIR__ . '/includes/db.php';

$pageTitle = page_title('Home');
$featuredTasks = fetch_all_rows(
    "SELECT t.*, u.full_name AS client_name
     FROM tasks t
     JOIN users u ON u.id = t.client_id
     WHERE t.status = 'open'
     ORDER BY t.created_at DESC
     LIMIT 6"
);
$stats = [
    'users' => fetch_one('SELECT COUNT(*) AS total FROM users')['total'] ?? 0,
    'tasks' => fetch_one('SELECT COUNT(*) AS total FROM tasks')['total'] ?? 0,
    'applications' => fetch_one('SELECT COUNT(*) AS total FROM task_applications')['total'] ?? 0,
];

require_once __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="hero-copy">
        <p class="eyebrow">Freelance task marketplace</p>
        <h1>Connect clients with skilled freelancers across Bangladesh.</h1>
        <p class="hero-text">KazConnect helps clients post jobs, review applicants, assign work, message talent, and manage delivery in one place.</p>
        <div class="hero-actions">
            <a class="btn" href="<?= e(BASE_URL) ?>/register.php">Create Account</a>
            <a class="btn btn-outline" href="<?= e(BASE_URL) ?>/tasks/browse_tasks.php">Explore Tasks</a>
        </div>
    </div>
    <div class="hero-panel">
        <div class="metric-card">
            <strong><?= (int) $stats['users'] ?></strong>
            <span>Community members</span>
        </div>
        <div class="metric-card">
            <strong><?= (int) $stats['tasks'] ?></strong>
            <span>Tasks posted</span>
        </div>
        
    </div>
</section>

<section class="section">
    <div class="section-head">
        <div>
            <p class="eyebrow">Fresh opportunities</p>
            <h2>Latest open tasks</h2>
        </div>
        <a class="text-link" href="<?= e(BASE_URL) ?>/tasks/browse_tasks.php">See all tasks</a>
    </div>
    <div class="card-grid">
        <?php foreach ($featuredTasks as $task): ?>
            <article class="card">
                <div class="card-top">
                    <span class="badge"><?= e(ucfirst($task['category'])) ?></span>
                    <span class="price-tag">৳<?= e(number_format((float) $task['budget'], 2)) ?></span>
                </div>
                <h3><?= e($task['title']) ?></h3>
                <p><?= e(mb_strimwidth($task['description'], 0, 140, '...')) ?></p>
                <div class="meta-row">
                    <span>Client: <?= e($task['client_name']) ?></span>
                    <span>Due: <?= e($task['deadline']) ?></span>
                </div>
                <a class="btn btn-small" href="<?= e(BASE_URL) ?>/tasks/task_details.php?id=<?= (int) $task['id'] ?>">View Details</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section section-alt">
    <div class="feature-grid">
        <div class="feature-card">
            <h3>Post tasks in minutes</h3>
            <p>Clients can publish jobs with scope, deadlines, budget, and category filters.</p>
        </div>
        <div class="feature-card">
            <h3>Apply with confidence</h3>
            <p>Freelancers submit proposals, showcase their profiles, and track application status.</p>
        </div>
        <div class="feature-card">
            <h3>Stay aligned</h3>
            <p>Built-in messaging, notifications, and reviews keep the workflow transparent.</p>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
