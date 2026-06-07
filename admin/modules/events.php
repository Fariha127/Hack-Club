<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$db     = get_db();
$events = $db->query("SELECT * FROM events ORDER BY event_date DESC")->fetchAll();

$pageTitle = 'Events';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="admin-layout">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-topbar">
            <button class="menu-toggle" id="menuToggle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
            <div class="topbar-title">Events</div>
            <div class="topbar-actions">
                <button class="btn btn-primary" onclick="openAddEvent()">+ Add Event</button>
                <button class="theme-btn" id="themeToggle"><svg id="sunIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg><svg id="moonIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg></button>
            </div>
        </div>
        <div class="admin-content">
            <div class="page-header">
                <h1 class="page-title">Event & Workshop Management</h1>
            </div>

            <div class="table-card">
                <div class="table-toolbar">
                    <input class="table-search" type="search" placeholder="Search events…" id="evSearch">
                    <select class="table-filter" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <select class="table-filter" id="typeFilter">
                        <option value="">All Types</option>
                        <option value="workshop">Workshop</option>
                        <option value="competition">Competition</option>
                        <option value="seminar">Seminar</option>
                        <option value="meetup">Meetup</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="table-wrap">
                    <table class="data-table" id="evTable">
                        <thead>
                            <tr><th>Title</th><th>Date</th><th>Location</th><th>Type</th><th>Status</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($events as $ev): ?>
                            <tr data-status="<?= $ev['status'] ?>" data-type="<?= $ev['event_type'] ?>">
                                <td>
                                    <div class="flex-cell">
                                        <?php if ($ev['cover_image']): ?><img src="<?= h($ev['cover_image']) ?>" class="thumb" alt=""><?php endif; ?>
                                        <strong><?= h($ev['title']) ?></strong>
                                    </div>
                                </td>
                                <td><?= date('d M Y', strtotime($ev['event_date'])) ?><?= $ev['event_time'] ? '<br><span class="muted">'.date('g:i A', strtotime($ev['event_time'])).'</span>' : '' ?></td>
                                <td class="muted"><?= h($ev['location'] ?? '—') ?></td>
                                <td><span class="badge badge-info"><?= h(ucfirst($ev['event_type'])) ?></span></td>
                                <td><?= status_badge($ev['status']) ?></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-icon btn-edit" title="Edit"
                                            onclick="editEvent(<?= htmlspecialchars(json_encode($ev), ENT_QUOTES) ?>)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
                                        <button class="btn-icon btn-success" title="Mark Completed"
                                            onclick="setEventStatus(<?= $ev['id'] ?>, 'completed')" <?= $ev['status']==='completed'?'disabled':'' ?>>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20,6 9,17 4,12"/></svg>
                                        </button>
                                        <button class="btn-icon btn-danger" title="Delete"
                                            onclick="deleteEvent(<?= $ev['id'] ?>, '<?= h($ev['title']) ?>')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/><path d="M10,11v6"/><path d="M14,11v6"/><path d="M9,6V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($events)): ?>
                            <tr><td colspan="6"><div class="empty-state">No events yet.</div></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Event Modal -->
<div class="modal-overlay" id="eventModal" style="display:none">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h3 class="modal-title" id="eventModalTitle">Add Event</h3>
            <button class="modal-close" onclick="closeModal('eventModal')">✕</button>
        </div>
        <form id="eventForm" enctype="multipart/form-data">
            <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" id="eventId">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Event Title *</label>
                        <input type="text" name="title" id="evTitle" required>
                    </div>
                    <div class="form-group">
                        <label>Event Type *</label>
                        <select name="event_type" id="evType">
                            <option value="workshop">Workshop</option>
                            <option value="competition">Competition</option>
                            <option value="seminar">Seminar</option>
                            <option value="meetup">Meetup</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" id="evDesc" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Full Details</label>
                    <textarea name="full_description" id="evFullDesc" rows="5"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Event Date *</label>
                        <input type="date" name="event_date" id="evDate" required>
                    </div>
                    <div class="form-group">
                        <label>Event Time</label>
                        <input type="time" name="event_time" id="evTime">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" id="evLocation" placeholder="e.g. KUET Auditorium">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="evStatus">
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Registration Link</label>
                    <input type="url" name="registration_link" id="evRegLink" placeholder="https://forms.google.com/…">
                </div>
                <div class="form-group">
                    <label>Cover Image</label>
                    <input type="file" name="cover_image" accept="image/*" id="evImg">
                    <img id="evImgPreview" src="" style="display:none;max-height:100px;margin-top:8px;border-radius:8px">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('eventModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Event</button>
            </div>
        </form>
    </div>
</div>

<div id="toast-container"></div>
<script>
const CSRF = '<?= csrf_token() ?>';

function openAddEvent() {
    document.getElementById('eventModalTitle').textContent = 'Add Event';
    document.getElementById('eventForm').reset();
    document.getElementById('eventId').value = '';
    document.getElementById('evImgPreview').style.display = 'none';
    openModal('eventModal');
}

function editEvent(ev) {
    document.getElementById('eventModalTitle').textContent = 'Edit Event';
    document.getElementById('eventId').value    = ev.id;
    document.getElementById('evTitle').value    = ev.title;
    document.getElementById('evType').value     = ev.event_type;
    document.getElementById('evDesc').value     = ev.description;
    document.getElementById('evFullDesc').value = ev.full_description || '';
    document.getElementById('evDate').value     = ev.event_date;
    document.getElementById('evTime').value     = ev.event_time || '';
    document.getElementById('evLocation').value = ev.location || '';
    document.getElementById('evStatus').value   = ev.status;
    document.getElementById('evRegLink').value  = ev.registration_link || '';
    if (ev.cover_image) {
        const img = document.getElementById('evImgPreview');
        img.src = ev.cover_image; img.style.display = 'block';
    }
    openModal('eventModal');
}

document.getElementById('eventForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fd.append('action', document.getElementById('eventId').value ? 'update' : 'create');
    fetch(BASE_URL + '/admin/api/events-api.php', {method:'POST', body:fd})
    .then(r=>r.json()).then(d => {
        if(d.ok){closeModal('eventModal');toast('Event saved','success');setTimeout(()=>location.reload(),800);}
        else toast(d.error,'danger');
    });
});

function setEventStatus(id, status) {
    fetch(BASE_URL + '/admin/api/events-api.php', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF},
        body: JSON.stringify({action:'set_status', id, status})
    }).then(r=>r.json()).then(d => {
        if(d.ok){toast('Status updated','success');setTimeout(()=>location.reload(),600);}
        else toast(d.error,'danger');
    });
}

function deleteEvent(id, title) {
    if(!confirm(`Delete event "${title}"?`)) return;
    fetch(BASE_URL + '/admin/api/events-api.php', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF},
        body: JSON.stringify({action:'delete', id})
    }).then(r=>r.json()).then(d => {
        if(d.ok){toast('Deleted','success');setTimeout(()=>location.reload(),600);}
        else toast(d.error,'danger');
    });
}

document.getElementById('evImg').addEventListener('change', function() {
    if(this.files[0]) {
        const r = new FileReader();
        r.onload = e => { const p=document.getElementById('evImgPreview'); p.src=e.target.result; p.style.display='block'; };
        r.readAsDataURL(this.files[0]);
    }
});

document.getElementById('evSearch').addEventListener('input', function() { filterTable('evTable', this.value); });
document.getElementById('statusFilter').addEventListener('change', function() {
    document.querySelectorAll('#evTable tbody tr').forEach(row => {
        const statusOk = !this.value || row.dataset.status === this.value;
        const typeEl = document.getElementById('typeFilter');
        const typeOk  = !typeEl.value || row.dataset.type === typeEl.value;
        row.style.display = (statusOk && typeOk) ? '' : 'none';
    });
});
document.getElementById('typeFilter').addEventListener('change', function() {
    document.getElementById('statusFilter').dispatchEvent(new Event('change'));
});
</script>
<script src="<?= BASE_URL ?>/admin/js/admin.js"></script>
</body></html>
