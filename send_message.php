<?php
require_once __DIR__ . '/../includes/db.php';
require_login();

$senderId = current_user()['id'];
$receiverId = (int) ($_POST['receiver_id'] ?? 0);
$message = trim($_POST['message'] ?? '');

if ($receiverId > 0 && $message !== '') {
    execute_query(
        'INSERT INTO messages (sender_id, receiver_id, message, is_read) VALUES (:sender_id, :receiver_id, :message, 0)',
        ['sender_id' => $senderId, 'receiver_id' => $receiverId, 'message' => $message]
    );
    add_notification($receiverId, 'You received a new message.', '/messages/chat.php?user_id=' . $senderId);
}

redirect('/messages/chat.php?user_id=' . $receiverId);
