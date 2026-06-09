<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: blogs.php#submit-blog');
    exit;
}

require_once __DIR__ . '/admin/includes/config.php';
require_once __DIR__ . '/admin/includes/db.php';
require_once __DIR__ . '/admin/includes/functions.php';

function blog_clean(string $value): string {
    return trim(preg_replace('/\s+/', ' ', $value));
}

function blog_redirect(string $status): void {
    header('Location: blogs.php?submission=' . $status . '#submit-blog');
    exit;
}

function blog_upload(array $file): ?string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    if (($file['size'] ?? 0) > MAX_UPLOAD_SIZE) {
        return null;
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    $type = '';
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
    } else {
        $type = $file['type'] ?? '';
    }

    if (!isset($allowed[$type])) {
        return null;
    }

    $dir = UPLOAD_BASE_DIR . 'blogs/';
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        return null;
    }

    $filename = bin2hex(random_bytes(10)) . '.' . $allowed[$type];
    if (!move_uploaded_file($file['tmp_name'], $dir . $filename)) {
        return null;
    }

    return UPLOAD_BASE_URL . 'blogs/' . $filename;
}

$name       = blog_clean((string) ($_POST['name'] ?? ''));
$university = blog_clean((string) ($_POST['university'] ?? ''));
$department = blog_clean((string) ($_POST['department'] ?? ''));
$title      = blog_clean((string) ($_POST['title'] ?? ''));
$tags       = blog_clean((string) ($_POST['tags'] ?? ''));
$content    = trim((string) ($_POST['content'] ?? ''));

if ($name === '' || $university === '' || $department === '' || $title === '' || $tags === '' || mb_strlen($content) < 50) {
    blog_redirect('error');
}

$image1 = blog_upload($_FILES['image_1'] ?? []);
$image2 = blog_upload($_FILES['image_2'] ?? []);
if (!$image1 || !$image2) {
    if ($image1) delete_file($image1);
    if ($image2) delete_file($image2);
    blog_redirect('error');
}

try {
    $db = get_db();
    $slug = slugify($title, 'blogs', 'slug');
    $excerpt = mb_substr(strip_tags($content), 0, 220);
    $author = $name . ', ' . $department;

    $stmt = $db->prepare(
        "INSERT INTO blogs (title, slug, author_name, author_university, author_department, content, excerpt, cover_image, image_2, tags, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')"
    );
    $stmt->execute([$title, $slug, $author, $university, $department, $content, $excerpt, $image1, $image2, $tags]);
} catch (Throwable $e) {
    delete_file($image1);
    delete_file($image2);
    blog_redirect('error');
}

blog_redirect('success');
