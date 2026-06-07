<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? '';
$adminId = current_admin()['id'];

verify_csrf();

$db = get_db();

switch ($action) {
    case 'review_application':
        $id       = (int) ($input['id'] ?? 0);
        $decision = $input['decision'] ?? '';
        if (!$id || !in_array($decision, ['approve', 'reject'], true)) json_err('Invalid input.');

        $st = $db->prepare("SELECT * FROM membership_applications WHERE id = ?");
        $st->execute([$id]);
        $app = $st->fetch();
        if (!$app) json_err('Application not found.', 404);

        $newStatus = $decision === 'approve' ? 'approved' : 'rejected';
        $db->prepare("UPDATE membership_applications SET status = ?, reviewed_at = NOW(), reviewed_by = ? WHERE id = ?")
           ->execute([$newStatus, $adminId, $id]);

        if ($decision === 'approve') {
            // Add to members table (ignore duplicate email)
            try {
                $db->prepare("INSERT INTO members (application_id, full_name, email, department, year, area_of_interest) VALUES (?, ?, ?, ?, ?, ?)")
                   ->execute([$app['id'], $app['full_name'], $app['email'], $app['department'], $app['year'], $app['area_of_interest']]);
            } catch (PDOException $e) {
                // Duplicate email — member already exists, just update application status
            }
            json_ok(['message' => 'Application approved and member added.']);
        }
        json_ok(['message' => 'Application rejected.']);
        break;

    case 'remove_member':
        $id = (int) ($input['id'] ?? 0);
        if (!$id) json_err('Invalid ID.');
        $db->prepare("DELETE FROM members WHERE id = ?")->execute([$id]);
        json_ok(['message' => 'Member removed.']);
        break;

    default:
        json_err('Unknown action.', 400);
}
