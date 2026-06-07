<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$db         = get_db();
$executives = $db->query("SELECT * FROM executives ORDER BY display_order ASC, created_at ASC")->fetchAll();

$pageTitle = 'Executives';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="admin-layout">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-topbar">
            <button class="menu-toggle" id="menuToggle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
            <div class="topbar-title">Executive Panel</div>
            <div class="topbar-actions">
                <button class="btn btn-primary" onclick="openAddExec()">+ Add Executive</button>
                <button class="theme-btn" id="themeToggle"><svg id="sunIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg><svg id="moonIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg></button>
            </div>
        </div>
        <div class="admin-content">
            <div class="page-header">
                <h1 class="page-title">Executive Panel Management</h1>
                <p class="page-subtitle">Manage the executive committee displayed on the About Us page.</p>
            </div>

            <!-- Executive Cards Grid -->
            <div class="exec-grid" id="execGrid">
                <?php foreach ($executives as $ex): ?>
                <div class="exec-card <?= $ex['is_active'] ? '' : 'inactive' ?>">
                    <div class="exec-photo-wrap">
                        <?php if ($ex['photo']): ?>
                            <img src="<?= h($ex['photo']) ?>" alt="<?= h($ex['name']) ?>" class="exec-photo">
                        <?php else: ?>
                            <div class="exec-photo-placeholder"><?= strtoupper(substr($ex['name'], 0, 1)) ?></div>
                        <?php endif; ?>
                        <?php if (!$ex['is_active']): ?>
                            <div class="inactive-badge">Inactive</div>
                        <?php endif; ?>
                    </div>
                    <div class="exec-info">
                        <div class="exec-name"><?= h($ex['name']) ?></div>
                        <div class="exec-role"><?= h($ex['role']) ?></div>
                        <div class="exec-category badge badge-muted"><?= h(str_replace('_', ' ', $ex['category'])) ?></div>
                        <?php if ($ex['department']): ?><div class="exec-dept muted"><?= h($ex['department']) ?></div><?php endif; ?>
                    </div>
                    <div class="exec-actions">
                        <button class="btn-icon btn-edit" title="Edit"
                            onclick="editExec(<?= htmlspecialchars(json_encode($ex), ENT_QUOTES) ?>)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button class="btn-icon <?= $ex['is_active'] ? 'btn-warning' : 'btn-success' ?>" title="<?= $ex['is_active'] ? 'Deactivate' : 'Activate' ?>"
                            onclick="toggleExec(<?= $ex['id'] ?>, <?= $ex['is_active'] ? '0' : '1' ?>)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><?= $ex['is_active'] ? '<line x1="8" y1="12" x2="16" y2="12"/>' : '<line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>' ?></svg>
                        </button>
                        <button class="btn-icon btn-danger" title="Delete"
                            onclick="deleteExec(<?= $ex['id'] ?>, '<?= h($ex['name']) ?>')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/><path d="M10,11v6"/><path d="M14,11v6"/><path d="M9,6V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/></svg>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($executives)): ?>
                    <div class="empty-state" style="grid-column:1/-1">No executives added yet. Click "Add Executive" to start.</div>
                <?php endif; ?>
            </div>

            <!-- Table view -->
            <div class="table-card" style="margin-top:32px">
                <div class="table-toolbar">
                    <h3 style="margin:0;font-size:0.95rem">All Executives (Table View)</h3>
                </div>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead><tr><th>Order</th><th>Name</th><th>Role</th><th>Category</th><th>Dept</th><th>Active</th><th>Actions</th></tr></thead>
                        <tbody>
                        <?php foreach ($executives as $ex): ?>
                            <tr>
                                <td class="muted"><?= $ex['display_order'] ?></td>
                                <td><strong><?= h($ex['name']) ?></strong></td>
                                <td><?= h($ex['role']) ?></td>
                                <td><?= status_badge($ex['category']) ?></td>
                                <td><?= h($ex['department'] ?? '—') ?></td>
                                <td><?= $ex['is_active'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-muted">No</span>' ?></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-icon btn-edit" onclick="editExec(<?= htmlspecialchars(json_encode($ex), ENT_QUOTES) ?>)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
                                        <button class="btn-icon btn-danger" onclick="deleteExec(<?= $ex['id'] ?>, '<?= h($ex['name']) ?>')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Executive Modal -->
<div class="modal-overlay" id="execModal" style="display:none">
    <div class="modal modal-md">
        <div class="modal-header">
            <h3 class="modal-title" id="execModalTitle">Add Executive</h3>
            <button class="modal-close" onclick="closeModal('execModal')">✕</button>
        </div>
        <form id="execForm" enctype="multipart/form-data">
            <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" id="execId">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" id="execName" required>
                    </div>
                    <div class="form-group">
                        <label>Role/Position *</label>
                        <input type="text" name="role" id="execRole" placeholder="e.g. President, Moderator" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" id="execCategory">
                            <option value="moderator">Moderator</option>
                            <option value="president">President</option>
                            <option value="vice_president">Vice President</option>
                            <option value="secretary">Secretary</option>
                            <option value="executive">Executive</option>
                            <option value="member">Member</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" name="department" id="execDept" placeholder="e.g. EEE, CSE">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="execEmail">
                    </div>
                    <div class="form-group">
                        <label>Display Order</label>
                        <input type="number" name="display_order" id="execOrder" value="0" min="0">
                    </div>
                </div>
                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio" id="execBio" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Profile Photo</label>
                    <input type="file" name="photo" accept="image/*" id="execPhoto">
                    <img id="execPhotoPreview" src="" style="display:none;max-height:80px;margin-top:8px;border-radius:50%">
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" id="execActive" value="1" checked>
                        Active (show on About Us page)
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('execModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Executive</button>
            </div>
        </form>
    </div>
</div>

<div id="toast-container"></div>
<script>
const CSRF = '<?= csrf_token() ?>';

function openAddExec() {
    document.getElementById('execModalTitle').textContent = 'Add Executive';
    document.getElementById('execForm').reset();
    document.getElementById('execId').value = '';
    document.getElementById('execPhotoPreview').style.display = 'none';
    document.getElementById('execActive').checked = true;
    openModal('execModal');
}

function editExec(ex) {
    document.getElementById('execModalTitle').textContent = 'Edit Executive';
    document.getElementById('execId').value       = ex.id;
    document.getElementById('execName').value     = ex.name;
    document.getElementById('execRole').value     = ex.role;
    document.getElementById('execCategory').value = ex.category;
    document.getElementById('execDept').value     = ex.department || '';
    document.getElementById('execEmail').value    = ex.email || '';
    document.getElementById('execBio').value      = ex.bio || '';
    document.getElementById('execOrder').value    = ex.display_order;
    document.getElementById('execActive').checked = ex.is_active == 1;
    if (ex.photo) {
        const p = document.getElementById('execPhotoPreview');
        p.src = ex.photo; p.style.display = 'block';
    } else {
        document.getElementById('execPhotoPreview').style.display = 'none';
    }
    openModal('execModal');
}

document.getElementById('execForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fd.append('action', document.getElementById('execId').value ? 'update' : 'create');
    if (!document.getElementById('execActive').checked) fd.set('is_active', '0');
    fetch(BASE_URL + '/admin/api/executives-api.php', {method:'POST', body:fd})
    .then(r=>r.json()).then(d => {
        if(d.ok){closeModal('execModal');toast('Executive saved','success');setTimeout(()=>location.reload(),800);}
        else toast(d.error,'danger');
    });
});

function toggleExec(id, active) {
    fetch(BASE_URL + '/admin/api/executives-api.php', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF},
        body: JSON.stringify({action:'toggle', id, is_active: active})
    }).then(r=>r.json()).then(d => {
        if(d.ok){toast(active?'Activated':'Deactivated','success');setTimeout(()=>location.reload(),600);}
        else toast(d.error,'danger');
    });
}

function deleteExec(id, name) {
    if(!confirm(`Delete executive "${name}"?`)) return;
    fetch(BASE_URL + '/admin/api/executives-api.php', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF},
        body: JSON.stringify({action:'delete', id})
    }).then(r=>r.json()).then(d => {
        if(d.ok){toast('Deleted','success');setTimeout(()=>location.reload(),600);}
        else toast(d.error,'danger');
    });
}

document.getElementById('execPhoto').addEventListener('change', function() {
    if(this.files[0]) {
        const r = new FileReader();
        r.onload = e => { const p=document.getElementById('execPhotoPreview'); p.src=e.target.result; p.style.display='block'; };
        r.readAsDataURL(this.files[0]);
    }
});
</script>
<script src="<?= BASE_URL ?>/admin/js/admin.js"></script>
</body></html>
