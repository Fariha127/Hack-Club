<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$db       = get_db();
$projects = $db->query("SELECT * FROM projects ORDER BY display_order ASC, created_at DESC")->fetchAll();

$pageTitle = 'Projects';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="admin-layout">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-topbar">
            <button class="menu-toggle" id="menuToggle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
            <div class="topbar-title">Projects</div>
            <div class="topbar-actions">
                <button class="btn btn-primary" onclick="openAddProject()">+ Add Project</button>
                <button class="theme-btn" id="themeToggle"><svg id="sunIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg><svg id="moonIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg></button>
            </div>
        </div>
        <div class="admin-content">
            <div class="page-header">
                <h1 class="page-title">Project Management</h1>
            </div>

            <div class="table-card">
                <div class="table-toolbar">
                    <input class="table-search" type="search" placeholder="Search projects…" id="projSearch">
                    <select class="table-filter" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="featured">Featured</option>
                    </select>
                </div>
                <div class="table-wrap">
                    <table class="data-table" id="projTable">
                        <thead>
                            <tr><th>Ord</th><th>Title</th><th>Team</th><th>Tech</th><th>Status</th><th>Updated</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($projects as $p): ?>
                            <tr data-status="<?= $p['project_status'] ?>">
                                <td class="muted"><?= $p['display_order'] ?></td>
                                <td>
                                    <div class="flex-cell">
                                        <?php if ($p['cover_image']): ?><img src="<?= h($p['cover_image']) ?>" class="thumb" alt=""><?php endif; ?>
                                        <strong><?= h($p['title']) ?></strong>
                                    </div>
                                </td>
                                <td class="muted"><?= h(truncate($p['team_members'] ?? '—', 40)) ?></td>
                                <td><span class="tag-list"><?= h(truncate($p['technologies'] ?? '—', 40)) ?></span></td>
                                <td><?= status_badge($p['project_status']) ?></td>
                                <td class="muted"><?= date('d M Y', strtotime($p['updated_at'])) ?></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-icon btn-edit" title="Edit"
                                            onclick="editProject(<?= htmlspecialchars(json_encode($p), ENT_QUOTES) ?>)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
                                        <button class="btn-icon btn-danger" title="Delete"
                                            onclick="deleteProject(<?= $p['id'] ?>, '<?= h($p['title']) ?>')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/><path d="M10,11v6"/><path d="M14,11v6"/><path d="M9,6V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($projects)): ?>
                            <tr><td colspan="7"><div class="empty-state">No projects yet.</div></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Project Modal -->
<div class="modal-overlay" id="projectModal" style="display:none">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h3 class="modal-title" id="projectModalTitle">Add Project</h3>
            <button class="modal-close" onclick="closeModal('projectModal')">✕</button>
        </div>
        <form id="projectForm" enctype="multipart/form-data">
            <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" id="projectId">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Title *</label>
                        <input type="text" name="title" id="projTitle" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="project_status" id="projStatus">
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="featured">Featured</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Short Description *</label>
                    <textarea name="description" id="projDesc" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Full Description</label>
                    <textarea name="full_description" id="projFullDesc" rows="6"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Team Members</label>
                        <input type="text" name="team_members" id="projTeam" placeholder="Alice, Bob, Charlie">
                    </div>
                    <div class="form-group">
                        <label>Technologies</label>
                        <input type="text" name="technologies" id="projTech" placeholder="Arduino, Python, KiCad">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>GitHub Link</label>
                        <input type="url" name="github_link" id="projGithub" placeholder="https://github.com/…">
                    </div>
                    <div class="form-group">
                        <label>Display Order</label>
                        <input type="number" name="display_order" id="projOrder" value="0" min="0">
                    </div>
                </div>
                <div class="form-group">
                    <label>Cover Image</label>
                    <input type="file" name="cover_image" accept="image/*" id="projImg">
                    <img id="projImgPreview" src="" style="display:none;max-height:100px;margin-top:8px;border-radius:8px">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('projectModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Project</button>
            </div>
        </form>
    </div>
</div>

<div id="toast-container"></div>
<script>
const CSRF = '<?= csrf_token() ?>';

function openAddProject() {
    document.getElementById('projectModalTitle').textContent = 'Add Project';
    document.getElementById('projectForm').reset();
    document.getElementById('projectId').value = '';
    document.getElementById('projImgPreview').style.display = 'none';
    openModal('projectModal');
}

function editProject(p) {
    document.getElementById('projectModalTitle').textContent = 'Edit Project';
    document.getElementById('projectId').value   = p.id;
    document.getElementById('projTitle').value   = p.title;
    document.getElementById('projStatus').value  = p.project_status;
    document.getElementById('projDesc').value    = p.description;
    document.getElementById('projFullDesc').value = p.full_description || '';
    document.getElementById('projTeam').value    = p.team_members || '';
    document.getElementById('projTech').value    = p.technologies || '';
    document.getElementById('projGithub').value  = p.github_link || '';
    document.getElementById('projOrder').value   = p.display_order;
    if (p.cover_image) {
        const img = document.getElementById('projImgPreview');
        img.src = p.cover_image; img.style.display = 'block';
    }
    openModal('projectModal');
}

document.getElementById('projectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fd.append('action', document.getElementById('projectId').value ? 'update' : 'create');
    fetch(BASE_URL + '/admin/api/projects-api.php', {method:'POST', body:fd})
    .then(r=>r.json()).then(d => {
        if(d.ok){closeModal('projectModal');toast('Project saved','success');setTimeout(()=>location.reload(),800);}
        else toast(d.error,'danger');
    });
});

function deleteProject(id, title) {
    if(!confirm(`Delete "${title}"?`)) return;
    fetch(BASE_URL + '/admin/api/projects-api.php', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF},
        body: JSON.stringify({action:'delete', id})
    }).then(r=>r.json()).then(d => {
        if(d.ok){toast('Deleted','success');setTimeout(()=>location.reload(),600);}
        else toast(d.error,'danger');
    });
}

document.getElementById('projImg').addEventListener('change', function() {
    if(this.files[0]) {
        const r = new FileReader();
        r.onload = e => { const p=document.getElementById('projImgPreview'); p.src=e.target.result; p.style.display='block'; };
        r.readAsDataURL(this.files[0]);
    }
});

document.getElementById('projSearch').addEventListener('input', function() { filterTable('projTable', this.value); });
document.getElementById('statusFilter').addEventListener('change', function() {
    document.querySelectorAll('#projTable tbody tr').forEach(row => {
        row.style.display = (!this.value || row.dataset.status === this.value) ? '' : 'none';
    });
});
</script>
<script src="<?= BASE_URL ?>/admin/js/admin.js"></script>
</body></html>
