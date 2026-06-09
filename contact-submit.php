<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: home.php#contact');
    exit;
}

function contact_clean(string $value): string {
    return trim(str_replace(["\r", "\n"], ' ', $value));
}

function contact_redirect(string $status): void {
    header('Location: home.php?contact=' . $status . '#contact');
    exit;
}

$name = contact_clean((string) ($_POST['name'] ?? ''));
$email = contact_clean((string) ($_POST['email'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if ($name === '' || $email === '' || $message === '' || mb_strlen($message) < 10 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    contact_redirect('error');
}

try {
    require_once __DIR__ . '/admin/includes/db.php';
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO contact_messages (full_name, email, message) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $message]);
} catch (Throwable $e) {
    contact_redirect('error');
}

contact_redirect('success');
