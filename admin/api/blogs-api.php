<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$isJson  = str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');
$input   = $isJson ? (json_decode(file_get_contents('php://input'), true) ?? []) : $_POST;
$action  = $input['action'] ?? '';
$adminId = current_admin()['id'];

verify_csrf();

$db = get_db();

switch ($action) {
    case 'create':
    case 'update':
        $id    = (int) ($input['id'] ?? 0);
        $title = trim($input['title'] ?? '');
        if (empty($title)) json_err('Title is required.');
        if (empty($input['content'])) json_err('Content is required.');
        if (empty($input['author_name'])) json_err('Author name is required.');

        $slug   = slugify($title, 'blogs', 'slug', $id);
        $status = in_array($input['status'] ?? '', ['pending','published','rejected']) ? $input['status'] : 'pending';

        $imagePath = null;
        if (!empty($_FILES['cover_image']['name'])) {
            $imagePath = upload_file($_FILES['cover_image'], 'blogs');
            if (!$imagePath) json_err('Image upload failed. Check file type and size (max 5MB).');
        }

        if ($action === 'create') {
            $st = $db->prepare("INSERT INTO blogs (title, slug, author_name, author_email, content, excerpt, cover_image, tags, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $st->execute([
                $title, $slug, $input['author_name'], $input['author_email'] ?? null,
                $input['content'], $input['excerpt'] ?? null,
                $imagePath, $input['tags'] ?? null, $status
            ]);
            json_ok(['id' => $db->lastInsertId(), 'message' => 'Blog created.']);
        } else {
            $blog = $db->prepare("SELECT cover_image FROM blogs WHERE id = ?");
            $blog->execute([$id]);
            $old = $blog->fetch();

            $img = $imagePath ?? ($old['cover_image'] ?? null);
            $st = $db->prepare("UPDATE blogs SET title=?,slug=?,author_name=?,author_email=?,content=?,excerpt=?,cover_image=?,tags=?,status=?,reviewed_by=? WHERE id=?");
            $st->execute([
                $title, $slug, $input['author_name'], $input['author_email'] ?? null,
                $input['content'], $input['excerpt'] ?? null,
                $img, $input['tags'] ?? null, $status, $adminId, $id
            ]);
            if ($imagePath && $old['cover_image']) delete_file($old['cover_image']);
            json_ok(['message' => 'Blog updated.']);
        }
        break;

    case 'review':
        $id       = (int) ($input['id'] ?? 0);
        $decision = $input['decision'] ?? '';
        if (!$id || !in_array($decision, ['publish', 'reject'], true)) json_err('Invalid input.');
        $newStatus = $decision === 'publish' ? 'published' : 'rejected';
        $db->prepare("UPDATE blogs SET status=?, published_at=?, reviewed_by=? WHERE id=?")
           ->execute([$newStatus, $decision === 'publish' ? date('Y-m-d H:i:s') : null, $adminId, $id]);
        json_ok(['message' => 'Blog ' . $decision . 'ed.']);
        break;

    case 'delete':
        $id = (int) ($input['id'] ?? 0);
        if (!$id) json_err('Invalid ID.');
        $st = $db->prepare("SELECT cover_image FROM blogs WHERE id = ?");
        $st->execute([$id]);
        $b = $st->fetch();
        $db->prepare("DELETE FROM blogs WHERE id = ?")->execute([$id]);
        if ($b) delete_file($b['cover_image']);
        json_ok(['message' => 'Blog deleted.']);
        break;

    default:
        json_err('Unknown action.', 400);
}
