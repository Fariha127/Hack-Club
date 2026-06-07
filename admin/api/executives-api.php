<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$isJson = str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');
$input  = $isJson ? (json_decode(file_get_contents('php://input'), true) ?? []) : $_POST;
$action = $input['action'] ?? '';

verify_csrf();

$db = get_db();
$categories = ['moderator','president','vice_president','secretary','executive','member'];

switch ($action) {
    case 'create':
    case 'update':
        $id   = (int) ($input['id'] ?? 0);
        $name = trim($input['name'] ?? '');
        $role = trim($input['role'] ?? '');
        if (!$name || !$role) json_err('Name and role are required.');

        $category = in_array($input['category'] ?? '', $categories) ? $input['category'] : 'member';
        $active   = isset($input['is_active']) ? (int) $input['is_active'] : 1;
        $order    = (int) ($input['display_order'] ?? 0);

        $photoPath = null;
        if (!empty($_FILES['photo']['name'])) {
            $photoPath = upload_file($_FILES['photo'], 'profiles');
            if (!$photoPath) json_err('Photo upload failed. Check type/size (max 5MB).');
        }

        if ($action === 'create') {
            $db->prepare("INSERT INTO executives (name, role, category, department, email, photo, bio, display_order, is_active) VALUES (?,?,?,?,?,?,?,?,?)")
               ->execute([$name, $role, $category, $input['department'] ?? null, $input['email'] ?? null, $photoPath, $input['bio'] ?? null, $order, $active]);
            json_ok(['id' => $db->lastInsertId(), 'message' => 'Executive added.']);
        } else {
            $st = $db->prepare("SELECT photo FROM executives WHERE id = ?");
            $st->execute([$id]);
            $old = $st->fetch();
            $photo = $photoPath ?? ($old['photo'] ?? null);
            $db->prepare("UPDATE executives SET name=?,role=?,category=?,department=?,email=?,photo=?,bio=?,display_order=?,is_active=? WHERE id=?")
               ->execute([$name, $role, $category, $input['department'] ?? null, $input['email'] ?? null, $photo, $input['bio'] ?? null, $order, $active, $id]);
            if ($photoPath && $old['photo']) delete_file($old['photo']);
            json_ok(['message' => 'Executive updated.']);
        }
        break;

    case 'toggle':
        $id     = (int) ($input['id'] ?? 0);
        $active = (int) ($input['is_active'] ?? 0);
        if (!$id) json_err('Invalid ID.');
        $db->prepare("UPDATE executives SET is_active = ? WHERE id = ?")->execute([$active, $id]);
        json_ok(['message' => 'Status updated.']);
        break;

    case 'delete':
        $id = (int) ($input['id'] ?? 0);
        if (!$id) json_err('Invalid ID.');
        $st = $db->prepare("SELECT photo FROM executives WHERE id = ?");
        $st->execute([$id]);
        $ex = $st->fetch();
        $db->prepare("DELETE FROM executives WHERE id = ?")->execute([$id]);
        if ($ex) delete_file($ex['photo']);
        json_ok(['message' => 'Executive deleted.']);
        break;

    default:
        json_err('Unknown action.', 400);
}
