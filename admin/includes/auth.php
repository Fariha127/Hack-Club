<?php
require_once __DIR__ . '/config.php';

function start_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function require_admin(): void {
    start_session();
    if (empty($_SESSION['admin_id'])) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/admin/login.php?timeout=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

function is_logged_in(): bool {
    start_session();
    return !empty($_SESSION['admin_id']);
}

function current_admin(): array {
    return [
        'id'    => $_SESSION['admin_id']    ?? null,
        'name'  => $_SESSION['admin_name']  ?? '',
        'email' => $_SESSION['admin_email'] ?? '',
        'role'  => $_SESSION['admin_role']  ?? '',
    ];
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verify_csrf(): void {
    $token = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(403);
        die(json_encode(['ok' => false, 'error' => 'Invalid CSRF token.']));
    }
}
