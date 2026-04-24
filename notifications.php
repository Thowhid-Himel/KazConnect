<?php
require_once __DIR__ . '/../includes/db.php';
require_login();

$notifications = fetch_all_rows(
    'SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC',
    ['user_id' => current_user()['id']]
);
execute_query('UPDATE notifications SET is_read = 1 WHERE user_id = :user_id', ['user_id' => current_user()['id']]);

$pageTitle = page_title('Notifications');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <h1>Notifications</h1>
    <div class="stack-list">
        <?php foreach ($notifications as $notification): ?>
            <article class="list-card">
                <p><?= e($notification['message']) ?></p>
                <div class="meta-row">
                    <span><?= e($notification['created_at']) ?></span>
                    <?php if ($notification['link']): ?>
                        <a href="<?= e(BASE_URL . $notification['link']) ?>">Open</a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
