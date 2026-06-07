<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$db     = get_db();
$tab    = $_GET['tab'] ?? 'submissions';
$blogs  = $db->query("SELECT * FROM blogs ORDER BY submitted_at DESC")->fetchAll();

$pageTitle = 'Blogs';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="admin-layout">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-topbar">
            <button class="menu-toggle" id="menuToggle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
            <div class="topbar-title">Blogs</div>
            <div class="topbar-actions">
                <button class="btn btn-primary" onclick="openModal('addBlogModal')">+ New Blog</button>
                <button class="theme-btn" id="themeToggle"><svg id="sunIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg><svg id="moonIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg></button>
            </div>
        </div>
        <div class="admin-content">
            <div class="page-header">
                <h1 class="page-title">Blog Management</h1>
            </div>

            <div class="table-card">
                <div class="table-toolbar">
                    <input class="table-search" type="search" placeholder="Search blogs…" id="blogSearch">
                    <select class="table-filter" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="published">Published</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="table-wrap">
                    <table class="data-table" id="blogTable">
                        <thead>
                            <tr><th>#</th><th>Title</th><th>Author</th><th>Tags</th><th>Status</th><th>Submitted</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($blogs as $i => $b): ?>
                            <tr data-status="<?= $b['status'] ?>">
                                <td class="muted"><?= $i + 1 ?></td>
                                <td><strong><?= h(truncate($b['title'], 50)) ?></strong></td>
                                <td><?= h($b['author_name']) ?></td>
                                <td><span class="tag-list"><?= h($b['tags'] ?? '—') ?></span></td>
                                <td><?= status_badge($b['status']) ?></td>
                                <td class="muted"><?= date('d M Y', strtotime($b['submitted_at'])) ?></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-icon btn-view" title="View"
                                            onclick="viewBlog(<?= htmlspecialchars(json_encode($b), ENT_QUOTES) ?>)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                        <button class="btn-icon btn-edit" title="Edit"
                                            onclick="editBlog(<?= htmlspecialchars(json_encode($b), ENT_QUOTES) ?>)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
                                        <?php if ($b['status'] === 'pending'): ?>
                                        <button class="btn-icon btn-success" title="Approve" onclick="reviewBlog(<?= $b['id'] ?>, 'publish')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20,6 9,17 4,12"/></svg>
                                        </button>
                                        <button class="btn-icon btn-danger" title="Reject" onclick="reviewBlog(<?= $b['id'] ?>, 'reject')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                        <?php endif; ?>
                                        <button class="btn-icon btn-danger" title="Delete" onclick="deleteBlog(<?= $b['id'] ?>, '<?= h($b['title']) ?>')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/><path d="M10,11v6"/><path d="M14,11v6"/><path d="M9,6V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($blogs)): ?>
                            <tr><td colspan="7"><div class="empty-state">No blog posts yet.</div></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Blog Modal -->
<div class="modal-overlay" id="viewBlogModal" style="display:none">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h3 class="modal-title" id="viewBlogTitle">Blog Post</h3>
            <button class="modal-close" onclick="closeModal('viewBlogModal')">✕</button>
        </div>
        <div class="modal-body" id="viewBlogBody" style="max-height:65vh;overflow-y:auto"></div>
    </div>
</div>

<!-- Add/Edit Blog Modal -->
<div class="modal-overlay" id="addBlogModal" style="display:none">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h3 class="modal-title" id="blogModalTitle">Add Blog Post</h3>
            <button class="modal-close" onclick="closeModal('addBlogModal')">✕</button>
        </div>
        <form id="blogForm" enctype="multipart/form-data">
            <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" id="blogId">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Title *</label>
                        <input type="text" name="title" id="blogTitle" required>
                    </div>
                    <div class="form-group">
                        <label>Author Name *</label>
                        <input type="text" name="author_name" id="blogAuthor" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Author Email</label>
                        <input type="email" name="author_email" id="blogAuthorEmail">
                    </div>
                    <div class="form-group">
                        <label>Tags (comma-separated)</label>
                        <input type="text" name="tags" id="blogTags" placeholder="PCB, Firmware, IoT">
                    </div>
                </div>
                <div class="form-group">
                    <label>Excerpt</label>
                    <textarea name="excerpt" id="blogExcerpt" rows="2" placeholder="Short summary…"></textarea>
                </div>
                <div class="form-group">
                    <label>Content *</label>
                    <textarea name="content" id="blogContent" rows="10" required></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Cover Image</label>
                        <input type="file" name="cover_image" accept="image/*" id="blogImg">
                        <img id="blogImgPreview" src="" style="display:none;max-height:80px;margin-top:8px;border-radius:6px">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="blogStatus">
                            <option value="pending">Pending</option>
                            <option value="published">Published</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('addBlogModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Blog</button>
            </div>
        </form>
    </div>
</div>

<div id="toast-container"></div>
<script>
const CSRF = '<?= csrf_token() ?>';

function viewBlog(b) {
    document.getElementById('viewBlogTitle').textContent = b.title;
    document.getElementById('viewBlogBody').innerHTML = `
        <div class="detail-grid">
            <div class="detail-row"><span class="detail-label">Author</span><span class="detail-val">${b.author_name} ${b.author_email ? '('+b.author_email+')' : ''}</span></div>
            <div class="detail-row"><span class="detail-label">Tags</span><span class="detail-val">${b.tags||'—'}</span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-val">${b.status}</span></div>
            <div class="detail-row"><span class="detail-label">Submitted</span><span class="detail-val">${b.submitted_at}</span></div>
        </div>
        <hr style="margin:16px 0;border-color:var(--border)">
        ${b.cover_image ? `<img src="${b.cover_image}" style="max-width:100%;border-radius:8px;margin-bottom:16px">` : ''}
        <div style="white-space:pre-wrap;line-height:1.7">${b.content}</div>
    `;
    openModal('viewBlogModal');
}

function editBlog(b) {
    document.getElementById('blogModalTitle').textContent = 'Edit Blog Post';
    document.getElementById('blogId').value     = b.id;
    document.getElementById('blogTitle').value  = b.title;
    document.getElementById('blogAuthor').value = b.author_name;
    document.getElementById('blogAuthorEmail').value = b.author_email || '';
    document.getElementById('blogTags').value   = b.tags || '';
    document.getElementById('blogExcerpt').value = b.excerpt || '';
    document.getElementById('blogContent').value = b.content;
    document.getElementById('blogStatus').value  = b.status;
    if (b.cover_image) {
        const prev = document.getElementById('blogImgPreview');
        prev.src = b.cover_image; prev.style.display = 'block';
    }
    openModal('addBlogModal');
}

function reviewBlog(id, action) {
    if (!confirm(action === 'publish' ? 'Publish this blog?' : 'Reject this blog?')) return;
    fetch(BASE_URL + '/admin/api/blogs-api.php', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF},
        body: JSON.stringify({action: 'review', id, decision: action})
    }).then(r=>r.json()).then(d => {
        if(d.ok){toast('Blog '+action+'ed','success');setTimeout(()=>location.reload(),800);}
        else toast(d.error,'danger');
    });
}

function deleteBlog(id, title) {
    if (!confirm(`Delete "${title}"?`)) return;
    fetch(BASE_URL + '/admin/api/blogs-api.php', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF},
        body: JSON.stringify({action:'delete', id})
    }).then(r=>r.json()).then(d => {
        if(d.ok){toast('Blog deleted','success');setTimeout(()=>location.reload(),800);}
        else toast(d.error,'danger');
    });
}

document.getElementById('blogForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    const id = document.getElementById('blogId').value;
    fd.append('action', id ? 'update' : 'create');
    fetch(BASE_URL + '/admin/api/blogs-api.php', {method:'POST', body:fd})
    .then(r=>r.json()).then(d => {
        if(d.ok){closeModal('addBlogModal');toast('Blog saved','success');setTimeout(()=>location.reload(),800);}
        else toast(d.error,'danger');
    });
});

document.getElementById('blogImg').addEventListener('change', function() {
    const prev = document.getElementById('blogImgPreview');
    if(this.files[0]){
        const reader = new FileReader();
        reader.onload = e => { prev.src = e.target.result; prev.style.display='block'; };
        reader.readAsDataURL(this.files[0]);
    }
});

document.getElementById('blogSearch').addEventListener('input', function() { filterTable('blogTable', this.value); });
document.getElementById('statusFilter').addEventListener('change', function() {
    document.querySelectorAll('#blogTable tbody tr').forEach(row => {
        row.style.display = (!this.value || row.dataset.status === this.value) ? '' : 'none';
    });
});
</script>
<script src="<?= BASE_URL ?>/admin/js/admin.js"></script>
</body></html>
