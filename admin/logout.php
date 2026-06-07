<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/config.php';
start_session();
session_unset();
session_destroy();
header('Location: ' . BASE_URL . '/admin/login.php?loggedout=1');
exit;
