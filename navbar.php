<?php
$navUser = current_user();
$unreadNotifications = $navUser ? unread_notification_count((int) $navUser['id']) : 0;
$unreadMessages = $navUser ? unread_message_count((int) $navUser['id']) : 0;
?>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="<?= e(BASE_URL) ?>/index.php">
            <img src="<?= e(asset_url('images/logo.png')) ?>" alt="KazConnect Logo" class="site-logo">
        </a>

        <nav class="nav-links">
            <a href="<?= e(BASE_URL) ?>/index.php">Home</a>
            <a href="<?= e(BASE_URL) ?>/tasks/browse_tasks.php">Browse Tasks</a>
            <a href="<?= e(BASE_URL) ?>/search/search_tasks.php">Search</a>
            <?php if ($navUser): ?>
                <a href="<?= e(BASE_URL . dashboard_path_for_role($navUser['role'])) ?>">Dashboard</a>
                <a class="nav-notification-link" href="<?= e(BASE_URL) ?>/messages/inbox.php">
                    Messages
                    <?php if ($unreadMessages > 0): ?>
                        <span class="notification-dot"><?= $unreadMessages > 9 ? '9+' : $unreadMessages ?></span>
                    <?php endif; ?>
                </a>
                <a class="nav-notification-link" href="<?= e(BASE_URL) ?>/notifications/notifications.php">
                    Notifications
                    <?php if ($unreadNotifications > 0): ?>
                        <span class="notification-dot"><?= $unreadNotifications > 9 ? '9+' : $unreadNotifications ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?= e(BASE_URL) ?>/profile/profile.php?id=<?= (int) $navUser['id'] ?>">Profile</a>
                <a class="btn btn-small btn-outline" href="<?= e(BASE_URL) ?>/logout.php">Logout</a>
            <?php else: ?>
                <a href="<?= e(BASE_URL) ?>/login.php">Login</a>
                <a class="btn btn-small" href="<?= e(BASE_URL) ?>/register.php">Join Now</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
