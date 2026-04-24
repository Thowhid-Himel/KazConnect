<?php
require_once __DIR__ . '/../includes/db.php';
require_login();

$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    execute_query(
        'UPDATE users SET full_name = :full_name, bio = :bio, skills = :skills WHERE id = :id',
        [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'bio' => trim($_POST['bio'] ?? ''),
            'skills' => trim($_POST['skills'] ?? ''),
            'id' => $user['id'],
        ]
    );
    refresh_session_user((int) $user['id']);
    set_flash('success', 'Profile updated.');
    redirect('/profile/profile.php?id=' . $user['id']);
}

$pageTitle = page_title('Edit Profile');
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section narrow">
    <form class="form-card validate-form" method="POST">
        <h1>Edit profile</h1>
        <label>Full name
            <input type="text" name="full_name" value="<?= e($user['full_name']) ?>" required>
        </label>
        <label>Bio
            <textarea name="bio" rows="6"><?= e($user['bio']) ?></textarea>
        </label>
        <label>Skills
            <input type="text" name="skills" value="<?= e($user['skills']) ?>">
        </label>
        <button class="btn" type="submit">Update profile</button>
    </form>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
