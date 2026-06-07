<?php
function slugify(string $text, string $table = '', string $col = 'slug', int $excludeId = 0): string {
    $text = mb_strtolower(trim($text));
    $text = preg_replace('/[^\w\s-]/u', '', $text);
    $text = preg_replace('/[\s_]+/', '-', $text);
    $slug = trim($text, '-');

    if (empty($table)) return $slug;

    $db   = get_db();
    $base = $slug;
    $i    = 1;
    do {
        $sql  = "SELECT COUNT(*) FROM `{$table}` WHERE `{$col}` = ?";
        $args = [$slug];
        if ($excludeId > 0) { $sql .= " AND id != ?"; $args[] = $excludeId; }
        $st = $db->prepare($sql);
        $st->execute($args);
        $count = (int) $st->fetchColumn();
        if ($count === 0) break;
        $slug = $base . '-' . $i++;
    } while (true);

    return $slug;
}

function upload_file(array $file, string $subdir): ?string {
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    if (!in_array($file['type'], $allowed, true)) return null;
    if ($file['size'] > MAX_UPLOAD_SIZE) return null;

    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = bin2hex(random_bytes(10)) . '.' . $ext;
    $dir      = UPLOAD_BASE_DIR . $subdir . '/';

    if (!is_dir($dir) && !mkdir($dir, 0755, true)) return null;
    if (!move_uploaded_file($file['tmp_name'], $dir . $filename)) return null;

    return UPLOAD_BASE_URL . $subdir . '/' . $filename;
}

function delete_file(?string $url): void {
    if (!$url) return;
    $path = str_replace(UPLOAD_BASE_URL, UPLOAD_BASE_DIR, $url);
    if (is_file($path)) unlink($path);
}

function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function truncate(string $text, int $len = 120): string {
    if (mb_strlen($text) <= $len) return $text;
    return mb_substr($text, 0, $len) . '…';
}

function time_ago(string $dt): string {
    $diff = time() - strtotime($dt);
    if ($diff < 60)    return 'just now';
    if ($diff < 3600)  return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    return floor($diff / 86400) . 'd ago';
}

function json_ok(array $data = [], int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['ok' => true] + $data);
    exit;
}

function json_err(string $msg, int $code = 400): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

function status_badge(string $status): string {
    $map = [
        'pending'   => 'badge-warning',
        'approved'  => 'badge-success',
        'rejected'  => 'badge-danger',
        'published' => 'badge-success',
        'upcoming'  => 'badge-info',
        'ongoing'   => 'badge-primary',
        'completed' => 'badge-muted',
        'cancelled' => 'badge-danger',
        'featured'  => 'badge-accent',
    ];
    $cls = $map[$status] ?? 'badge-muted';
    return '<span class="badge ' . $cls . '">' . h(ucfirst($status)) . '</span>';
}
