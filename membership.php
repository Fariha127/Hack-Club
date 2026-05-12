<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: home.html');
    exit;
}

function clean_value($value) {
    return trim(str_replace(["\r", "\n"], ' ', (string) $value));
}

$name = clean_value($_POST['name'] ?? '');
$email = clean_value($_POST['email'] ?? '');
$department = clean_value($_POST['department'] ?? '');
$interest = clean_value($_POST['interest'] ?? '');
$message = trim((string) ($_POST['message'] ?? ''));

if ($name === '' || $email === '' || $department === '' || $interest === '' || $message === '') {
    header('Location: home.html?membership=error');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: home.html?membership=error');
    exit;
}

$recordFile = __DIR__ . '/membership-submissions.csv';
$isNewFile = !file_exists($recordFile);
$handle = fopen($recordFile, 'ab');

if ($handle === false) {
    header('Location: home.html?membership=error');
    exit;
}

if ($isNewFile) {
    fputcsv($handle, ['submitted_at', 'name', 'email', 'department', 'interest', 'message']);
}

fputcsv($handle, [gmdate('c'), $name, $email, $department, $interest, $message]);
fclose($handle);

header('Location: home.html?membership=success');
exit;