<?php
require_once __DIR__ . '/../includes/db.php';

$userId = (int) ($_GET['user_id'] ?? 0);
$user = fetch_one('SELECT id, full_name FROM users WHERE id = :id', ['id' => $userId]);
$reviews = fetch_all_rows(
    "SELECT r.*, u.full_name AS reviewer_name
     FROM reviews r
     JOIN users u ON u.id = r.reviewer_id
     WHERE r.reviewed_user_id = :id
     ORDER BY r.created_at DESC",
    ['id' => $userId]
);

$pageTitle = page_title('Reviews');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <h1>Reviews for <?= e($user['full_name'] ?? 'User') ?></h1>
    <div class="stack-list">
        <?php foreach ($reviews as $review): ?>
            <article class="list-card">
                <p><strong><?= e($review['reviewer_name']) ?></strong> rated <?= (int) $review['rating'] ?>/5</p>
                <p><?= e($review['comment']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
