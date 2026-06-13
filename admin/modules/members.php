<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$db  = get_db();
$tab = $_GET['tab'] ?? 'applications';

$applications = $db->query("SELECT * FROM membership_applications ORDER BY submitted_at DESC")->fetchAll();
$members      = $db->query("SELECT * FROM members ORDER BY joined_at DESC")->fetchAll();

$pageTitle = 'Members';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="admin-layout">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-topbar">
            <button class="menu-toggle" id="menuToggle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
            <div class="topbar-title">Members</div>
            <div class="topbar-actions">
                <button class="theme-btn" id="themeToggle" aria-label="Toggle theme"><svg id="sunIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg><svg id="moonIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg></button>
            </div>
        </div>
        <div class="admin-content">
            <div class="page-header">
                <h1 class="page-title">Membership Management</h1>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <a href="?tab=applications" class="tab <?= $tab === 'applications' ? 'active' : '' ?>">
                    Applications
                    <?php $pending = count(array_filter($applications, fn($a) => $a['status'] === 'pending')); ?>
                    <?php if ($pending): ?><span class="tab-badge"><?= $pending ?></span><?php endif; ?>
                </a>
                <a href="?tab=members" class="tab <?= $tab === 'members' ? 'active' : '' ?>">
                    Members <span class="tab-badge tab-badge-muted"><?= count($members) ?></span>
                </a>
            </div>

            <?php if ($tab === 'applications'): ?>
            <!-- Applications Table -->
            <div class="table-card">
                <div class="table-toolbar">
                    <input class="table-search" type="search" placeholder="Search applications…" id="appSearch">
                    <select class="table-filter" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="table-wrap">
                    <table class="data-table" id="appTable">
                        <thead>
                            <tr>
                                <th>#</th><th>Name</th><th>Email</th><th>Department</th><th>Year</th><th>Area of Interest</th><th>Why Join</th><th>Status</th><th>Submitted</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($applications as $i => $app): ?>
                            <tr data-status="<?= $app['status'] ?>">
                                <td class="muted"><?= $i + 1 ?></td>
                                <td><strong><?= h($app['full_name']) ?></strong></td>
                                <td><?= h($app['email']) ?></td>
                                <td><?= h($app['department']) ?></td>
                                <td><?= h($app['year']) ?></td>
                                <td><?= h(truncate($app['area_of_interest'] ?? '', 45)) ?></td>
                                <td><?= h(truncate($app['why_join'] ?? '', 55)) ?></td>
                                <td><?= status_badge($app['status']) ?></td>
                                <td class="muted"><?= date('d M Y', strtotime($app['submitted_at'])) ?></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-icon btn-view" title="View Details"
                                            onclick="viewApplication(<?= htmlspecialchars(json_encode($app), ENT_QUOTES) ?>)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                        <?php if ($app['status'] === 'pending'): ?>
                                        <button class="btn-icon btn-success" title="Approve"
                                            onclick="reviewApp(<?= $app['id'] ?>, 'approve')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20,6 9,17 4,12"/></svg>
                                        </button>
                                        <button class="btn-icon btn-danger" title="Reject"
                                            onclick="reviewApp(<?= $app['id'] ?>, 'reject')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($applications)): ?>
                            <tr><td colspan="10"><div class="empty-state">No applications yet.</div></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php else: ?>
            <!-- Members Table -->
            <div class="table-card">
                <div class="table-toolbar">
                    <input class="table-search" type="search" placeholder="Search members…" id="memberSearch">
                </div>
                <div class="table-wrap">
                    <table class="data-table" id="memberTable">
                        <thead>
                            <tr><th>#</th><th>Name</th><th>Email</th><th>Department</th><th>Year</th><th>Area of Interest</th><th>Joined</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($members as $i => $m): ?>
                            <tr>
                                <td class="muted"><?= $i + 1 ?></td>
                                <td><strong><?= h($m['full_name']) ?></strong></td>
                                <td><?= h($m['email']) ?></td>
                                <td><?= h($m['department']) ?></td>
                                <td><?= h($m['year']) ?></td>
                                <td><?= h(truncate($m['area_of_interest'], 50)) ?></td>
                                <td class="muted"><?= date('d M Y', strtotime($m['joined_at'])) ?></td>
                                <td>
                                    <button class="btn-icon btn-danger" title="Remove Member"
                                        onclick="removeMember(<?= $m['id'] ?>, '<?= h($m['full_name']) ?>')">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/><path d="M10,11v6"/><path d="M14,11v6"/><path d="M9,6V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/></svg>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($members)): ?>
                            <tr><td colspan="8"><div class="empty-state">No members yet. Approve applications to add members.</div></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- View Application Modal -->
<div class="modal-overlay" id="viewAppModal" style="display:none">
    <div class="modal modal-md">
        <div class="modal-header">
            <h3 class="modal-title">Application Details</h3>
            <button class="modal-close" onclick="closeModal('viewAppModal')">✕</button>
        </div>
        <div class="modal-body" id="viewAppBody"></div>
        <div class="modal-footer" id="viewAppFooter"></div>
    </div>
</div>

<div id="toast-container"></div>

<script>
const CSRF = '<?= csrf_token() ?>';

function viewApplication(app) {
    document.getElementById('viewAppBody').innerHTML = `
        <div class="detail-grid">
            <div class="detail-row"><span class="detail-label">Full Name</span><span class="detail-val">${app.full_name}</span></div>
            <div class="detail-row"><span class="detail-label">Email</span><span class="detail-val">${app.email}</span></div>
            <div class="detail-row"><span class="detail-label">Department</span><span class="detail-val">${app.department}</span></div>
            <div class="detail-row"><span class="detail-label">Year</span><span class="detail-val">${app.year}</span></div>
            <div class="detail-row"><span class="detail-label">Area of Interest</span><span class="detail-val">${app.area_of_interest}</span></div>
            <div class="detail-row"><span class="detail-label">Why Join</span><span class="detail-val">${app.why_join}</span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-val">${app.status}</span></div>
            <div class="detail-row"><span class="detail-label">Submitted</span><span class="detail-val">${app.submitted_at}</span></div>
        </div>
    `;
    const footer = document.getElementById('viewAppFooter');
    if (app.status === 'pending') {
        footer.innerHTML = `
            <button class="btn btn-success" onclick="reviewApp(${app.id},'approve');closeModal('viewAppModal')">Approve</button>
            <button class="btn btn-danger" onclick="reviewApp(${app.id},'reject');closeModal('viewAppModal')">Reject</button>
        `;
    } else {
        footer.innerHTML = '';
    }
    document.getElementById('viewAppModal').style.display = 'flex';
}

function reviewApp(id, action) {
    if (!confirm(`${action === 'approve' ? 'Approve' : 'Reject'} this application?`)) return;
    fetch(BASE_URL + '/admin/api/members-api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-Token': CSRF},
        body: JSON.stringify({action: 'review_application', id, decision: action})
    })
    .then(r => r.json())
    .then(d => {
        if (d.ok) { toast(d.message || 'Done', 'success'); setTimeout(() => location.reload(), 800); }
        else toast(d.error, 'danger');
    });
}

function removeMember(id, name) {
    if (!confirm(`Remove ${name} from members?`)) return;
    fetch(BASE_URL + '/admin/api/members-api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-Token': CSRF},
        body: JSON.stringify({action: 'remove_member', id})
    })
    .then(r => r.json())
    .then(d => {
        if (d.ok) { toast('Member removed', 'success'); setTimeout(() => location.reload(), 800); }
        else toast(d.error, 'danger');
    });
}

// Search filter
document.getElementById('appSearch')?.addEventListener('input', function() {
    filterTable('appTable', this.value);
});
document.getElementById('memberSearch')?.addEventListener('input', function() {
    filterTable('memberTable', this.value);
});
document.getElementById('statusFilter')?.addEventListener('change', function() {
    const rows = document.querySelectorAll('#appTable tbody tr');
    rows.forEach(row => {
        row.style.display = (!this.value || row.dataset.status === this.value) ? '' : 'none';
    });
});
</script>
<script src="<?= BASE_URL ?>/admin/js/admin.js"></script>
</body></html>
