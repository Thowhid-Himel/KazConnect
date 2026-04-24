<?php
require_once __DIR__ . '/includes/db.php';

if (is_logged_in()) {
    redirect(dashboard_path_for_role(current_user()['role']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = fetch_one('SELECT * FROM users WHERE email = :email', ['email' => $email]);

    if (!$user || !password_verify($password, $user['password'])) {
        set_flash('error', 'Invalid email or password.');
    } else {
        $_SESSION['user'] = $user;
        set_flash('success', 'Welcome back, ' . $user['full_name'] . '!');
        redirect(dashboard_path_for_role($user['role']));
    }
}

$pageTitle = page_title('Login');
require_once __DIR__ . '/includes/header.php';
?>
<section class="auth-shell">
    <form class="form-card validate-form" method="POST">
        <h1>Login</h1>
        <p>Access your dashboard, messages, and active projects.</p>
        <label>Email
            <input type="email" name="email" required>
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <button class="btn" type="submit">Login</button>
        <p class="helper-text">No account yet? <a href="<?= e(BASE_URL) ?>/register.php">Register here</a>.</p>
    </form>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
