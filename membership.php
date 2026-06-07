<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: home.php');
    exit;
}

function clean_value($value) {
    return trim(str_replace(["\r", "\n"], ' ', (string) $value));
}

$name       = clean_value($_POST['name']       ?? '');
$email      = clean_value($_POST['email']      ?? '');
$department = clean_value($_POST['department'] ?? '');
$interest   = clean_value($_POST['interest']   ?? '');
$message    = trim((string) ($_POST['message'] ?? ''));
$year       = clean_value($_POST['year']       ?? 'N/A');

if ($name === '' || $email === '' || $department === '' || $interest === '' || $message === '') {
    header('Location: home.php?membership=error');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: home.php?membership=error');
    exit;
}

// Try MySQL first, fall back to CSV
$saved = false;

try {
    require_once __DIR__ . '/admin/includes/config.php';
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $stmt = $pdo->prepare(
        "INSERT INTO membership_applications (full_name, email, department, year, area_of_interest, why_join) VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$name, $email, $department, $year, $interest, $message]);
    $saved = true;
} catch (Exception $e) {
    // DB unavailable — fall through to CSV backup
}

if (!$saved) {
    $recordFile = __DIR__ . '/membership-submissions.csv';
    $isNewFile  = !file_exists($recordFile);
    $handle     = fopen($recordFile, 'ab');
    if ($handle !== false) {
        if ($isNewFile) {
            fputcsv($handle, ['submitted_at', 'name', 'email', 'department', 'year', 'interest', 'message']);
        }
        fputcsv($handle, [gmdate('c'), $name, $email, $department, $year, $interest, $message]);
        fclose($handle);
    } else {
        header('Location: home.php?membership=error');
        exit;
    }
}

header('Location: home.php?membership=success');
exit;
