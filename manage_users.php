<?php
require_once __DIR__ . '/../includes/db.php';
require_role(['admin']);

$users = fetch_all_rows('SELECT id, full_name, email, role, created_at FROM users ORDER BY created_at DESC');

$pageTitle = page_title('Manage Users');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <h1>Manage users</h1>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Joined</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= e($user['full_name']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td><?= e(ucfirst($user['role'])) ?></td>
                    <td><?= e(substr($user['created_at'], 0, 10)) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
