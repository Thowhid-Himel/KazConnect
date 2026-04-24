<?php
require_once __DIR__ . '/../includes/db.php';
require_login();

$user = current_user();
$conversations = fetch_all_rows(
    "SELECT u.id, u.full_name, MAX(m.created_at) AS last_message_at,
            SUM(CASE WHEN m.receiver_id = :user_id AND m.sender_id = u.id AND m.is_read = 0 THEN 1 ELSE 0 END) AS unread_count
     FROM messages m
     JOIN users u ON u.id = CASE WHEN m.sender_id = :user_id THEN m.receiver_id ELSE m.sender_id END
     WHERE m.sender_id = :user_id OR m.receiver_id = :user_id
     GROUP BY u.id, u.full_name
     ORDER BY last_message_at DESC",
    ['user_id' => $user['id']]
);

$pageTitle = page_title('Inbox');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <h1>Inbox</h1>
    <div class="card-grid">
        <?php foreach ($conversations as $conversation): ?>
            <article class="card">
                <h3>
                    <?= e($conversation['full_name']) ?>
                    <?php if ((int) $conversation['unread_count'] > 0): ?>
                        <span class="notification-dot"><?= (int) $conversation['unread_count'] > 9 ? '9+' : (int) $conversation['unread_count'] ?></span>
                    <?php endif; ?>
                </h3>
                <p>Last activity: <?= e($conversation['last_message_at']) ?></p>
                <a class="btn btn-small" href="<?= e(BASE_URL) ?>/messages/chat.php?user_id=<?= (int) $conversation['id'] ?>">Open chat</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
