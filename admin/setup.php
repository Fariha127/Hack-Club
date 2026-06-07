<?php
/**
 * HACK KUET - One-Time Admin Setup Script
 * Run this ONCE after uploading to your server.
 * It will create the database tables and the first admin user.
 * DELETE or RENAME this file after setup is complete.
 */

// ─── Configuration ────────────────────────────────────────────
$config = [
    'db_host' => 'localhost',
    'db_name' => 'hack_kuet',
    'db_user' => 'root',
    'db_pass' => '',
    'admin_name'  => 'HACK Admin',
    'admin_email' => 'hack.kuet.club@gmail.com',
    'admin_pass'  => 'HackAdmin@2025',
];

$errors = [];
$success = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Override from form
    $config['db_host']     = trim($_POST['db_host'] ?? $config['db_host']);
    $config['db_name']     = trim($_POST['db_name'] ?? $config['db_name']);
    $config['db_user']     = trim($_POST['db_user'] ?? $config['db_user']);
    $config['db_pass']     = $_POST['db_pass'] ?? $config['db_pass'];
    $config['admin_name']  = trim($_POST['admin_name'] ?? $config['admin_name']);
    $config['admin_email'] = trim($_POST['admin_email'] ?? $config['admin_email']);
    $config['admin_pass']  = $_POST['admin_pass'] ?? $config['admin_pass'];

    if (empty($config['admin_email']) || !filter_var($config['admin_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid admin email is required.';
    }
    if (strlen($config['admin_pass']) < 8) {
        $errors[] = 'Admin password must be at least 8 characters.';
    }

    if (empty($errors)) {
        try {
            // Connect without DB first to create it
            $pdo = new PDO(
                "mysql:host={$config['db_host']};charset=utf8mb4",
                $config['db_user'],
                $config['db_pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Create database
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['db_name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$config['db_name']}`");
            $success[] = "Database '{$config['db_name']}' created/selected.";

            // Load and execute schema
            $schema = file_get_contents(dirname(__DIR__) . '/database/schema.sql');
            // Remove CREATE DATABASE and USE statements (already handled)
            $schema = preg_replace('/CREATE DATABASE.*?;\s*/si', '', $schema);
            $schema = preg_replace('/USE\s+`[^`]+`\s*;\s*/si', '', $schema);
            $schema = preg_replace('/SET\s+NAMES.*?;\s*/si', '', $schema);

            // Split by semicolon and run each statement
            $statements = array_filter(array_map('trim', explode(';', $schema)));
            foreach ($statements as $stmt) {
                if (!empty($stmt)) {
                    $pdo->exec($stmt);
                }
            }
            $success[] = 'All database tables created successfully.';

            // Create admin user
            $hash = password_hash($config['admin_pass'], PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $pdo->prepare("INSERT INTO `admins` (`name`, `email`, `password_hash`, `role`) VALUES (?, ?, ?, 'super_admin') ON DUPLICATE KEY UPDATE `password_hash` = VALUES(`password_hash`), `name` = VALUES(`name`)");
            $stmt->execute([$config['admin_name'], $config['admin_email'], $hash]);
            $success[] = "Admin user '{$config['admin_email']}' created/updated.";

            // Seed contact info
            $contacts = [
                ['email',          'Club Email',       'hack.kuet.club@gmail.com'],
                ['phone',          'Phone Number',     '+880 1700 000000'],
                ['address',        'Office Address',   'KUET Campus, Khulna-9203, Bangladesh'],
                ['facebook_page',  'Facebook Page',    'https://www.facebook.com/hack.kuet'],
                ['facebook_group', 'Facebook Group',   'https://www.facebook.com/groups/hack.kuet'],
                ['github',         'GitHub',           ''],
                ['linkedin',       'LinkedIn',         ''],
                ['youtube',        'YouTube',          ''],
            ];
            $stmt = $pdo->prepare("INSERT INTO `contact_info` (`key_name`, `label`, `value`) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `label` = VALUES(`label`)");
            foreach ($contacts as $c) {
                $stmt->execute($c);
            }
            $success[] = 'Contact info seeded.';

            // Write config file
            $configContent = "<?php\ndefine('DB_HOST', " . var_export($config['db_host'], true) . ");\ndefine('DB_NAME', " . var_export($config['db_name'], true) . ");\ndefine('DB_USER', " . var_export($config['db_user'], true) . ");\ndefine('DB_PASS', " . var_export($config['db_pass'], true) . ");\ndefine('DB_CHARSET', 'utf8mb4');\ndefine('UPLOAD_BASE_DIR', dirname(__DIR__) . '/uploads/');\ndefine('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);\ndefine('SESSION_TIMEOUT', 3600);\nif (!defined('BASE_URL')) {\n    \$__s = str_replace('\\\\', '/', \$_SERVER['SCRIPT_NAME'] ?? '/');\n    \$__p = strpos(\$__s, '/admin');\n    define('BASE_URL', \$__p !== false ? rtrim(substr(\$__s, 0, \$__p), '/') : '');\n    unset(\$__s, \$__p);\n}\ndefine('UPLOAD_BASE_URL', BASE_URL . '/admin/uploads/');\n";
            file_put_contents(__DIR__ . '/includes/config.php', $configContent);
            $success[] = 'Configuration file written to admin/includes/config.php.';

            $success[] = '<strong>Setup complete! <a href="login.php">Go to Admin Login</a></strong>';
            $success[] = '<em>IMPORTANT: Delete or rename this setup.php file now for security.</em>';

        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Setup | HACK Admin</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Space Grotesk', sans-serif; background: #030806; color: #d8ffe8; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .setup-card { background: rgba(10,33,20,0.9); border: 1px solid rgba(110,255,164,0.2); border-radius: 16px; padding: 40px; max-width: 520px; width: 100%; }
        h1 { color: #6effa4; margin: 0 0 8px; font-size: 1.6rem; }
        p.sub { color: #90c7a8; margin: 0 0 28px; font-size: 0.9rem; }
        .section-label { color: #6effa4; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; margin: 20px 0 8px; }
        label { display: block; font-size: 0.85rem; color: #90c7a8; margin-bottom: 4px; }
        input { width: 100%; padding: 10px 14px; background: rgba(0,0,0,0.4); border: 1px solid rgba(110,255,164,0.25); border-radius: 8px; color: #d8ffe8; font-family: inherit; font-size: 0.9rem; margin-bottom: 14px; }
        input:focus { outline: none; border-color: #6effa4; }
        button { width: 100%; padding: 12px; background: #6effa4; color: #022f1a; font-family: inherit; font-weight: 700; font-size: 0.95rem; border: none; border-radius: 10px; cursor: pointer; margin-top: 8px; }
        button:hover { background: #5de894; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 8px; font-size: 0.875rem; }
        .alert-error { background: rgba(229,62,62,0.15); border: 1px solid rgba(229,62,62,0.3); color: #fc8181; }
        .alert-success { background: rgba(110,255,164,0.1); border: 1px solid rgba(110,255,164,0.25); color: #6effa4; }
        .warning { background: rgba(214,158,46,0.1); border: 1px solid rgba(214,158,46,0.3); color: #f6c343; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-size: 0.85rem; }
    </style>
</head>
<body>
<div class="setup-card">
    <h1>HACK Admin Setup</h1>
    <p class="sub">One-time initialization for the HACK KUET Admin Panel.</p>

    <div class="warning">This script creates the database and admin account. Run it once, then delete it.</div>

    <?php foreach ($errors as $e): ?>
        <div class="alert alert-error"><?= $e ?></div>
    <?php endforeach; ?>
    <?php foreach ($success as $s): ?>
        <div class="alert alert-success"><?= $s ?></div>
    <?php endforeach; ?>

    <?php if (empty($success) || !empty($errors)): ?>
    <form method="POST">
        <div class="section-label">Database</div>
        <label>Host</label>
        <input type="text" name="db_host" value="<?= htmlspecialchars($config['db_host']) ?>" required>
        <label>Database Name</label>
        <input type="text" name="db_name" value="<?= htmlspecialchars($config['db_name']) ?>" required>
        <label>Username</label>
        <input type="text" name="db_user" value="<?= htmlspecialchars($config['db_user']) ?>" required>
        <label>Password</label>
        <input type="password" name="db_pass" value="">

        <div class="section-label">Admin Account</div>
        <label>Admin Name</label>
        <input type="text" name="admin_name" value="<?= htmlspecialchars($config['admin_name']) ?>" required>
        <label>Admin Email</label>
        <input type="email" name="admin_email" value="<?= htmlspecialchars($config['admin_email']) ?>" required>
        <label>Admin Password (min 8 chars)</label>
        <input type="password" name="admin_pass" placeholder="Enter secure password" required>

        <button type="submit">Run Setup</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
