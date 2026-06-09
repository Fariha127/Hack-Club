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
$statuses = ['upcoming','ongoing','completed','cancelled'];
$types    = ['workshop','competition','seminar','meetup','other'];

switch ($action) {
    case 'create':
    case 'update':
        $id    = (int) ($input['id'] ?? 0);
        $title = trim($input['title'] ?? '');
        if (!$title)                       json_err('Title is required.');
        if (empty($input['description']))  json_err('Description is required.');
        if (empty($input['event_date']))   json_err('Event date is required.');

        $slug   = slugify($title, 'events', 'slug', $id);
        $status = in_array($input['status'] ?? '', $statuses) ? $input['status'] : 'upcoming';
        $type   = in_array($input['event_type'] ?? '', $types) ? $input['event_type'] : 'other';

        $imagePath = null;
        if (!empty($_FILES['cover_image']['name'])) {
            $imagePath = upload_file($_FILES['cover_image'], 'events');
            if (!$imagePath) json_err('Image upload failed.');
        }

        if ($action === 'create') {
            $db->prepare("INSERT INTO events (title, slug, description, cover_image, event_date, event_time, location, event_type, status, created_by) VALUES (?,?,?,?,?,?,?,?,?,?)")
               ->execute([$title, $slug, $input['description'], $imagePath, $input['event_date'], $input['event_time'] ?: null, $input['location'] ?? null, $type, $status, $adminId]);
            json_ok(['id' => $db->lastInsertId(), 'message' => 'Event created.']);
        } else {
            $st = $db->prepare("SELECT cover_image FROM events WHERE id = ?");
            $st->execute([$id]);
            $old   = $st->fetch();
            $image = $imagePath ?? ($old['cover_image'] ?? null);
            $db->prepare("UPDATE events SET title=?,slug=?,description=?,cover_image=?,event_date=?,event_time=?,location=?,event_type=?,status=? WHERE id=?")
               ->execute([$title, $slug, $input['description'], $image, $input['event_date'], $input['event_time'] ?: null, $input['location'] ?? null, $type, $status, $id]);
            if ($imagePath && $old['cover_image']) delete_file($old['cover_image']);
            json_ok(['message' => 'Event updated.']);
        }
        break;

    case 'activity_create':
    case 'activity_update':
        $id = (int) ($input['id'] ?? 0);
        $title = trim($input['title'] ?? '');
        if (!$title) json_err('Title is required.');
        if (empty($input['description'])) json_err('Description is required.');
        if (empty($input['activity_date'])) json_err('Activity date is required.');

        $slug = slugify($title, 'club_activities', 'slug', $id);
        $content = trim($input['content'] ?? '') ?: $input['description'];

        $imagePath = null;
        if (!empty($_FILES['cover_image']['name'])) {
            $imagePath = upload_file($_FILES['cover_image'], 'activities');
            if (!$imagePath) json_err('Image upload failed.');
        }

        if ($action === 'activity_create') {
            $db->prepare("INSERT INTO club_activities (title, slug, activity_date, description, content, cover_image) VALUES (?,?,?,?,?,?)")
               ->execute([$title, $slug, $input['activity_date'], $input['description'], $content, $imagePath]);
            json_ok(['id' => $db->lastInsertId(), 'message' => 'Club activity created.']);
        } else {
            if (!$id) json_err('Invalid ID.');
            $st = $db->prepare("SELECT cover_image FROM club_activities WHERE id = ?");
            $st->execute([$id]);
            $old = $st->fetch();
            if (!$old) json_err('Club activity not found.', 404);

            $image = $imagePath ?? ($old['cover_image'] ?? null);
            $db->prepare("UPDATE club_activities SET title=?, slug=?, activity_date=?, description=?, content=?, cover_image=? WHERE id=?")
               ->execute([$title, $slug, $input['activity_date'], $input['description'], $content, $image, $id]);
            if ($imagePath && $old['cover_image']) delete_file($old['cover_image']);
            json_ok(['message' => 'Club activity updated.']);
        }
        break;

    case 'activity_delete':
        $id = (int) ($input['id'] ?? 0);
        if (!$id) json_err('Invalid ID.');
        $st = $db->prepare("SELECT cover_image FROM club_activities WHERE id = ?");
        $st->execute([$id]);
        $activity = $st->fetch();
        if (!$activity) json_err('Club activity not found.', 404);
        $db->prepare("DELETE FROM club_activities WHERE id = ?")->execute([$id]);
        if ($activity['cover_image']) delete_file($activity['cover_image']);
        json_ok(['message' => 'Club activity deleted.']);
        break;

    case 'set_status':
        $id     = (int) ($input['id'] ?? 0);
        $status = $input['status'] ?? '';
        if (!$id || !in_array($status, $statuses, true)) json_err('Invalid input.');
        $db->prepare("UPDATE events SET status = ? WHERE id = ?")->execute([$status, $id]);
        json_ok(['message' => 'Status updated.']);
        break;

    case 'delete':
        $id = (int) ($input['id'] ?? 0);
        if (!$id) json_err('Invalid ID.');
        $st = $db->prepare("SELECT cover_image FROM events WHERE id = ?");
        $st->execute([$id]);
        $ev = $st->fetch();
        $db->prepare("DELETE FROM events WHERE id = ?")->execute([$id]);
        if ($ev) delete_file($ev['cover_image']);
        json_ok(['message' => 'Event deleted.']);
        break;

    default:
        json_err('Unknown action.', 400);
}
