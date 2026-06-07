<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

start_session();

if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        try {
            $st = get_db()->prepare("SELECT id, name, email, password_hash, role FROM admins WHERE email = ? LIMIT 1");
            $st->execute([$email]);
            $admin = $st->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id']    = $admin['id'];
                $_SESSION['admin_name']  = $admin['name'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_role']  = $admin['role'];
                $_SESSION['last_activity'] = time();
                $_SESSION['csrf']        = bin2hex(random_bytes(32));

                // Update last_login
                get_db()->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?")->execute([$admin['id']]);

                header('Location: ' . BASE_URL . '/admin/dashboard.php');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            $error = 'Database error. Please check your configuration.';
        }
    } else {
        $error = 'Please fill in both fields.';
    }
}

$timeout   = isset($_GET['timeout']);
$loggedout = isset($_GET['loggedout']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>try{if(localStorage.getItem('hack-theme')==='dark')document.documentElement.classList.add('dark')}catch(e){}</script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/css/admin.css">
    <title>Admin Login | HACK KUET</title>
</head>
<body class="login-body">

<div class="login-bg"></div>

<div class="login-card">
    <div class="login-brand">
        <div class="login-brand-icon"></div>
        <div class="login-brand-text">
            <div class="brand-name">HACK Admin</div>
            <div class="brand-sub">Hardware Acceleration Club of KUET</div>
        </div>
    </div>

    <?php if ($timeout): ?>
        <div class="alert alert-warning">Your session expired. Please log in again.</div>
    <?php elseif ($loggedout): ?>
        <div class="alert alert-info">You have been logged out.</div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
    <?php endif; ?>

    <form class="login-form" method="POST" autocomplete="on">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="admin@example.com"
                   value="<?= h($_POST['email'] ?? '') ?>" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-eye">
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                <button type="button" class="eye-btn" id="eyeBtn" aria-label="Toggle password visibility">
                    <svg id="eyeOpen" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg id="eyeClosed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
        </div>
        <button type="submit" class="btn-login">Sign In</button>
    </form>

    <a href="<?= BASE_URL ?>/home.html" class="back-link">← Back to Website</a>
</div>

<script>
const eyeBtn    = document.getElementById('eyeBtn');
const passInput = document.getElementById('password');
const eyeOpen   = document.getElementById('eyeOpen');
const eyeClosed = document.getElementById('eyeClosed');
eyeBtn.addEventListener('click', () => {
    const isPass = passInput.type === 'password';
    passInput.type    = isPass ? 'text' : 'password';
    eyeOpen.style.display   = isPass ? 'none'  : '';
    eyeClosed.style.display = isPass ? ''      : 'none';
});
</script>
</body>
</html>
