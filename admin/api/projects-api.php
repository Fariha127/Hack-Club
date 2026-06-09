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

$db       = get_db();
$statuses = ['ongoing','completed','featured'];

switch ($action) {
    case 'create':
    case 'update':
        $id    = (int) ($input['id'] ?? 0);
        $title = trim($input['title'] ?? '');
        if (!$title) json_err('Title is required.');
        if (empty($input['description'])) json_err('Description is required.');

        $slug   = slugify($title, 'projects', 'slug', $id);
        $status = in_array($input['project_status'] ?? '', $statuses) ? $input['project_status'] : 'ongoing';
        $order  = (int) ($input['display_order'] ?? 0);

        $imagePath = null;
        if (!empty($_FILES['cover_image']['name'])) {
            $imagePath = upload_file($_FILES['cover_image'], 'projects');
            if (!$imagePath) json_err('Image upload failed.');
        }

        if ($action === 'create') {
            $db->prepare("INSERT INTO projects (title, slug, description, full_description, cover_image, team_members, mentors, technologies, github_link, project_status, display_order, created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")
               ->execute([$title, $slug, $input['description'], $input['full_description'] ?? null, $imagePath, $input['team_members'] ?? null, $input['mentors'] ?? null, $input['technologies'] ?? null, $input['github_link'] ?? null, $status, $order, $adminId]);
            json_ok(['id' => $db->lastInsertId(), 'message' => 'Project created.']);
        } else {
            $st = $db->prepare("SELECT cover_image FROM projects WHERE id = ?");
            $st->execute([$id]);
            $old   = $st->fetch();
            $image = $imagePath ?? ($old['cover_image'] ?? null);
            $db->prepare("UPDATE projects SET title=?,slug=?,description=?,full_description=?,cover_image=?,team_members=?,mentors=?,technologies=?,github_link=?,project_status=?,display_order=? WHERE id=?")
               ->execute([$title, $slug, $input['description'], $input['full_description'] ?? null, $image, $input['team_members'] ?? null, $input['mentors'] ?? null, $input['technologies'] ?? null, $input['github_link'] ?? null, $status, $order, $id]);
            if ($imagePath && $old['cover_image']) delete_file($old['cover_image']);
            json_ok(['message' => 'Project updated.']);
        }
        break;

    case 'delete':
        $id = (int) ($input['id'] ?? 0);
        if (!$id) json_err('Invalid ID.');
        $st = $db->prepare("SELECT cover_image FROM projects WHERE id = ?");
        $st->execute([$id]);
        $p = $st->fetch();
        $db->prepare("DELETE FROM projects WHERE id = ?")->execute([$id]);
        if ($p) delete_file($p['cover_image']);
        json_ok(['message' => 'Project deleted.']);
        break;

    default:
        json_err('Unknown action.', 400);
}
