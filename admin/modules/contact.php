<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$db      = get_db();
$message = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $adminId = current_admin()['id'];
    try {
        $stmt = $db->prepare("INSERT INTO contact_info (key_name, label, value, updated_by) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE label = VALUES(label), value = VALUES(value), updated_by = VALUES(updated_by)");
        $keys = $_POST['key_name'] ?? [];
        $labels = $_POST['label'] ?? [];
        $values = $_POST['value'] ?? [];
        foreach ($keys as $i => $key) {
            if ($key) {
                $stmt->execute([$key, $labels[$i] ?? $key, $values[$i] ?? '', $adminId]);
            }
        }
        $message = 'Contact information updated successfully.';
        $msgType = 'success';
    } catch (PDOException $e) {
        $message = 'Error saving: ' . $e->getMessage();
        $msgType = 'danger';
    }
}

$contacts = $db->query("SELECT * FROM contact_info ORDER BY id ASC")->fetchAll();

// Default fields if table is empty
if (empty($contacts)) {
    $contacts = [
        ['id' => null, 'key_name' => 'email',          'label' => 'Club Email',       'value' => ''],
        ['id' => null, 'key_name' => 'phone',           'label' => 'Phone Number',     'value' => ''],
        ['id' => null, 'key_name' => 'address',         'label' => 'Office Address',   'value' => ''],
        ['id' => null, 'key_name' => 'facebook_page',   'label' => 'Facebook Page',    'value' => ''],
        ['id' => null, 'key_name' => 'facebook_group',  'label' => 'Facebook Group',   'value' => ''],
        ['id' => null, 'key_name' => 'github',          'label' => 'GitHub',           'value' => ''],
        ['id' => null, 'key_name' => 'linkedin',        'label' => 'LinkedIn',         'value' => ''],
        ['id' => null, 'key_name' => 'youtube',         'label' => 'YouTube',          'value' => ''],
    ];
}

$pageTitle = 'Contact Info';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="admin-layout">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-topbar">
            <button class="menu-toggle" id="menuToggle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
            <div class="topbar-title">Contact Info</div>
            <div class="topbar-actions">
                <button class="theme-btn" id="themeToggle"><svg id="sunIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg><svg id="moonIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg></button>
            </div>
        </div>
        <div class="admin-content">
            <div class="page-header">
                <h1 class="page-title">Contact Information</h1>
                <p class="page-subtitle">Update the contact details displayed on the Contact page.</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $msgType ?>"><?= h($message) ?></div>
            <?php endif; ?>

            <form method="POST" class="contact-form-card">
                <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">

                <div class="form-section">
                    <h3 class="form-section-title">Contact Details</h3>
                    <?php foreach ($contacts as $c): ?>
                    <div class="contact-field-row">
                        <input type="hidden" name="key_name[]" value="<?= h($c['key_name']) ?>">
                        <div class="form-group contact-label-group">
                            <label>Field Label</label>
                            <input type="text" name="label[]" value="<?= h($c['label']) ?>" placeholder="Label">
                        </div>
                        <div class="form-group contact-value-group">
                            <label>Value</label>
                            <?php if (in_array($c['key_name'], ['email', 'phone', 'facebook_page', 'facebook_group', 'github', 'linkedin', 'youtube'])): ?>
                                <input type="text" name="value[]" value="<?= h($c['value']) ?>"
                                       placeholder="<?= h($c['key_name'] === 'email' ? 'email@example.com' : 'https://…') ?>">
                            <?php else: ?>
                                <textarea name="value[]" rows="2"><?= h($c['value']) ?></textarea>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-section">
                    <h3 class="form-section-title">Add New Field</h3>
                    <div class="contact-field-row">
                        <div class="form-group contact-label-group">
                            <label>Field Key (no spaces)</label>
                            <input type="text" name="key_name[]" placeholder="e.g. twitter" id="newKey">
                        </div>
                        <div class="form-group contact-label-group">
                            <label>Field Label</label>
                            <input type="text" name="label[]" placeholder="e.g. Twitter" id="newLabel">
                        </div>
                        <div class="form-group contact-value-group">
                            <label>Value</label>
                            <input type="text" name="value[]" placeholder="https://twitter.com/…" id="newValue">
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary btn-lg">Save All Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="toast-container"></div>
<script src="<?= BASE_URL ?>/admin/js/admin.js"></script>
</body></html>
