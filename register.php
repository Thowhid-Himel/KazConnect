<?php
require_once __DIR__ . '/includes/db.php';

if (is_logged_in()) {
    redirect(dashboard_path_for_role(current_user()['role']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'freelancer';
    $bio = trim($_POST['bio'] ?? '');
    $skills = trim($_POST['skills'] ?? '');

    $existing = fetch_one('SELECT id FROM users WHERE email = :email', ['email' => $email]);

    if ($existing) {
        set_flash('error', 'That email is already registered.');
    } elseif (!in_array($role, ['client', 'freelancer'], true)) {
        set_flash('error', 'Invalid role selected.');
    } else {
        execute_query(
            'INSERT INTO users (full_name, email, password, role, bio, skills) VALUES (:full_name, :email, :password, :role, :bio, :skills)',
            [
                'full_name' => $fullName,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role,
                'bio' => $bio,
                'skills' => $skills,
            ]
        );

        $userId = (int) db()->lastInsertId();
        add_notification($userId, 'Welcome to KazConnect! Complete your profile to attract better matches.', '/profile/edit_profile.php');

        $_SESSION['user'] = fetch_one('SELECT * FROM users WHERE id = :id', ['id' => $userId]);
        set_flash('success', 'Account created successfully.');
        redirect(dashboard_path_for_role($role));
    }
}

$pageTitle = page_title('Register');
require_once __DIR__ . '/includes/header.php';
?>
<section class="auth-shell">
    <form class="form-card validate-form" method="POST">
        <h1>Create an account</h1>
        <p>Join as a client or freelancer and start collaborating.</p>
        <label>Full Name
            <input type="text" name="full_name" required>
        </label>
        <label>Email
            <input type="email" name="email" required>
        </label>
        <label>Password
            <input type="password" name="password" minlength="6" required>
        </label>
        <label>Role
            <select name="role" required>
                <option value="client">Client</option>
                <option value="freelancer">Freelancer</option>
            </select>
        </label>
        <label>Short Bio
            <textarea name="bio" rows="4" placeholder="Tell people what you do"></textarea>
        </label>
        <label>Skills
            <input type="text" name="skills" placeholder="PHP, UI Design, Translation">
        </label>
        <button class="btn" type="submit">Register</button>
    </form>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
