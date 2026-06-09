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
$messages = $db->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC")->fetchAll();

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
                <p class="page-subtitle">Update public contact details and review messages sent by viewers.</p>
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

            <div class="table-card" style="margin-top:24px">
                <div class="table-toolbar">
                    <input class="table-search" type="search" placeholder="Search messages..." id="messageSearch">
                    <select class="table-filter" id="messageStatusFilter">
                        <option value="">All Messages</option>
                        <option value="unread">Unread</option>
                        <option value="read">Read</option>
                    </select>
                </div>
                <div class="table-wrap">
                    <table class="data-table" id="messageTable">
                        <thead>
                            <tr><th>#</th><th>Name</th><th>Email</th><th>Message</th><th>Status</th><th>Submitted</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($messages as $i => $m): ?>
                            <tr data-status="<?= h($m['status']) ?>">
                                <td class="muted"><?= $i + 1 ?></td>
                                <td><strong><?= h($m['full_name']) ?></strong></td>
                                <td><a href="mailto:<?= h($m['email']) ?>"><?= h($m['email']) ?></a></td>
                                <td><?= h(truncate($m['message'], 70)) ?></td>
                                <td><?= status_badge($m['status'] === 'unread' ? 'pending' : 'approved') ?></td>
                                <td class="muted"><?= date('d M Y', strtotime($m['submitted_at'])) ?></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-icon btn-view" title="View" onclick="viewMessage(<?= htmlspecialchars(json_encode($m), ENT_QUOTES) ?>)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                        <?php if ($m['status'] === 'unread'): ?>
                                        <button class="btn-icon btn-success" title="Mark read" onclick="markMessageRead(<?= $m['id'] ?>)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20,6 9,17 4,12"/></svg>
                                        </button>
                                        <?php endif; ?>
                                        <button class="btn-icon btn-danger" title="Delete" onclick="deleteMessage(<?= $m['id'] ?>, <?= htmlspecialchars(json_encode($m['full_name']), ENT_QUOTES) ?>)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/><path d="M10,11v6"/><path d="M14,11v6"/><path d="M9,6V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($messages)): ?>
                            <tr><td colspan="7"><div class="empty-state">No viewer messages yet.</div></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="viewMessageModal" style="display:none">
    <div class="modal modal-md">
        <div class="modal-header">
            <h3 class="modal-title" id="viewMessageTitle">Viewer Message</h3>
            <button class="modal-close" onclick="closeModal('viewMessageModal')">✕</button>
        </div>
        <div class="modal-body" id="viewMessageBody"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('viewMessageModal')">Close</button>
        </div>
    </div>
</div>

<div id="toast-container"></div>
<script>
const CSRF = '<?= csrf_token() ?>';

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[char]));
}

function contactApi(payload) {
    return fetch(BASE_URL + '/admin/api/contact-api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-Token': CSRF},
        body: JSON.stringify(payload)
    }).then(r => r.json());
}

function viewMessage(m) {
    document.getElementById('viewMessageTitle').textContent = 'Message from ' + m.full_name;
    document.getElementById('viewMessageBody').innerHTML = `
        <div class="detail-grid">
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-val">${escapeHtml(m.full_name)}</span></div>
            <div class="detail-row"><span class="detail-label">Email</span><span class="detail-val"><a href="mailto:${escapeHtml(m.email)}">${escapeHtml(m.email)}</a></span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-val">${escapeHtml(m.status)}</span></div>
            <div class="detail-row"><span class="detail-label">Submitted</span><span class="detail-val">${escapeHtml(m.submitted_at)}</span></div>
        </div>
        <hr style="margin:16px 0;border-color:var(--border)">
        <div style="white-space:pre-wrap;line-height:1.7">${escapeHtml(m.message)}</div>
    `;
    openModal('viewMessageModal');
    if (m.status === 'unread') {
        contactApi({action: 'mark_message_read', id: Number(m.id)}).then(() => {});
    }
}

function markMessageRead(id) {
    contactApi({action: 'mark_message_read', id}).then(d => {
        if (d.ok) { toast('Message marked as read', 'success'); setTimeout(() => location.reload(), 700); }
        else toast(d.error || 'Could not update message', 'danger');
    });
}

function deleteMessage(id, name) {
    if (!confirm(`Delete message from "${name}"?`)) return;
    contactApi({action: 'delete_message', id}).then(d => {
        if (d.ok) { toast('Message deleted', 'success'); setTimeout(() => location.reload(), 700); }
        else toast(d.error || 'Could not delete message', 'danger');
    });
}

document.getElementById('messageSearch')?.addEventListener('input', function() { filterTable('messageTable', this.value); });
document.getElementById('messageStatusFilter')?.addEventListener('change', function() {
    document.querySelectorAll('#messageTable tbody tr').forEach(row => {
        row.style.display = (!this.value || row.dataset.status === this.value) ? '' : 'none';
    });
});
</script>
<script src="<?= BASE_URL ?>/admin/js/admin.js"></script>
</body></html>
