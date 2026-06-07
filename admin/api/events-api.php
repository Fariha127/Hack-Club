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
            $db->prepare("INSERT INTO events (title, slug, description, full_description, cover_image, event_date, event_time, location, event_type, status, registration_link, created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")
               ->execute([$title, $slug, $input['description'], $input['full_description'] ?? null, $imagePath, $input['event_date'], $input['event_time'] ?: null, $input['location'] ?? null, $type, $status, $input['registration_link'] ?? null, $adminId]);
            json_ok(['id' => $db->lastInsertId(), 'message' => 'Event created.']);
        } else {
            $st = $db->prepare("SELECT cover_image FROM events WHERE id = ?");
            $st->execute([$id]);
            $old   = $st->fetch();
            $image = $imagePath ?? ($old['cover_image'] ?? null);
            $db->prepare("UPDATE events SET title=?,slug=?,description=?,full_description=?,cover_image=?,event_date=?,event_time=?,location=?,event_type=?,status=?,registration_link=? WHERE id=?")
               ->execute([$title, $slug, $input['description'], $input['full_description'] ?? null, $image, $input['event_date'], $input['event_time'] ?: null, $input['location'] ?? null, $type, $status, $input['registration_link'] ?? null, $id]);
            if ($imagePath && $old['cover_image']) delete_file($old['cover_image']);
            json_ok(['message' => 'Event updated.']);
        }
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
