<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();
// Contact info is handled via direct form POST in contact.php module.
// This endpoint handles individual field updates via AJAX if needed.
$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? '';
verify_csrf();
$db      = get_db();
$adminId = current_admin()['id'];

if ($action === 'update_field') {
    $key   = trim($input['key_name'] ?? '');
    $label = trim($input['label']    ?? '');
    $value = $input['value'] ?? '';
    if (!$key || !$label) json_err('Key and label required.');
    $db->prepare("INSERT INTO contact_info (key_name, label, value, updated_by) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE label=VALUES(label), value=VALUES(value), updated_by=VALUES(updated_by)")
       ->execute([$key, $label, $value, $adminId]);
    json_ok(['message' => 'Updated.']);
} elseif ($action === 'delete_field') {
    $id = (int) ($input['id'] ?? 0);
    if (!$id) json_err('Invalid ID.');
    $db->prepare("DELETE FROM contact_info WHERE id = ?")->execute([$id]);
    json_ok(['message' => 'Deleted.']);
} else {
    json_err('Unknown action.');
}
