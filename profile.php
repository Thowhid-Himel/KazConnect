<?php
require_once __DIR__ . '/../includes/db.php';

$viewer = current_user();
$profileId = (int) ($_GET['id'] ?? ($viewer['id'] ?? 0));
$profile = fetch_one('SELECT * FROM users WHERE id = :id', ['id' => $profileId]);

if (!$profile) {
    set_flash('error', 'Profile not found.');
    redirect('/index.php');
}

$reviews = fetch_all_rows(
    "SELECT r.*, u.full_name AS reviewer_name
     FROM reviews r
     JOIN users u ON u.id = r.reviewer_id
     WHERE r.reviewed_user_id = :id
     ORDER BY r.created_at DESC",
    ['id' => $profileId]
);

$pageTitle = page_title('Profile');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="detail-layout">
        <article class="detail-card">
            <h1><?= e($profile['full_name']) ?></h1>
            <p class="badge"><?= e(ucfirst($profile['role'])) ?></p>
            <p><?= nl2br(e($profile['bio'] ?: 'No bio added yet.')) ?></p>
            <div class="detail-meta">
                <div><strong>Email</strong><span><?= e($profile['email']) ?></span></div>
                <div><strong>Skills</strong><span><?= e($profile['skills'] ?: 'Not listed') ?></span></div>
                <div><strong>Joined</strong><span><?= e(substr($profile['created_at'], 0, 10)) ?></span></div>
            </div>
            <?php if ($viewer && (int) $viewer['id'] === (int) $profile['id']): ?>
                <a class="btn" href="<?= e(BASE_URL) ?>/profile/edit_profile.php">Edit profile</a>
            <?php endif; ?>
        </article>
        <aside class="sidebar-card">
            <h3>Reviews</h3>
            <?php foreach ($reviews as $review): ?>
                <div class="mini-user">
                    <strong><?= e($review['reviewer_name']) ?> - <?= (int) $review['rating'] ?>/5</strong>
                    <span><?= e($review['comment']) ?></span>
                </div>
            <?php endforeach; ?>
        </aside>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
