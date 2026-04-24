<?php
require_once __DIR__ . '/../includes/db.php';
require_login();

$user = current_user();
$otherUserId = (int) ($_GET['user_id'] ?? 0);
$otherUser = fetch_one('SELECT id, full_name FROM users WHERE id = :id', ['id' => $otherUserId]);

if (!$otherUser) {
    set_flash('error', 'User not found.');
    redirect('/messages/inbox.php');
}

execute_query(
    'UPDATE messages SET is_read = 1 WHERE sender_id = :other_id AND receiver_id = :user_id AND is_read = 0',
    ['other_id' => $otherUserId, 'user_id' => $user['id']]
);

$messages = fetch_all_rows(
    "SELECT m.*, u.full_name AS sender_name
     FROM messages m
     JOIN users u ON u.id = m.sender_id
     WHERE (m.sender_id = :user_id AND m.receiver_id = :other_id)
        OR (m.sender_id = :other_id AND m.receiver_id = :user_id)
     ORDER BY m.created_at ASC",
    ['user_id' => $user['id'], 'other_id' => $otherUserId]
);

$pageTitle = page_title('Chat');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section narrow">
    <div class="chat-shell">
        <div class="section-head">
            <h1>Chat with <?= e($otherUser['full_name']) ?></h1>
        </div>
        <div class="chat-box">
            <?php foreach ($messages as $message): ?>
                <div class="chat-message <?= (int) $message['sender_id'] === (int) $user['id'] ? 'own' : '' ?>">
                    <strong><?= e($message['sender_name']) ?></strong>
                    <p><?= nl2br(e($message['message'])) ?></p>
                    <span><?= e($message['created_at']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <form method="POST" action="<?= e(BASE_URL) ?>/messages/send_message.php" class="chat-form">
            <input type="hidden" name="receiver_id" value="<?= (int) $otherUser['id'] ?>">
            <textarea name="message" rows="4" placeholder="Write your message" required></textarea>
            <button class="btn" type="submit">Send</button>
        </form>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
