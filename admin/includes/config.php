<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'hack_kuet');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('UPLOAD_BASE_DIR', dirname(__DIR__) . '/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);
define('SESSION_TIMEOUT', 3600);
if (!defined('BASE_URL')) {
    $__s = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
    $__p = strpos($__s, '/admin');
    if ($__p !== false) {
        define('BASE_URL', rtrim(substr($__s, 0, $__p), '/'));
    } else {
        $__d = rtrim(dirname($__s), '/');
        define('BASE_URL', $__d === '/' || $__d === '\\' ? '' : $__d);
        unset($__d);
    }
    unset($__s, $__p);
}
define('UPLOAD_BASE_URL', BASE_URL . '/admin/uploads/');
