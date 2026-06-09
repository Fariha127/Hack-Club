<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$db     = get_db();
$events = $db->query("SELECT * FROM events ORDER BY event_date DESC")->fetchAll();
$activities = $db->query("SELECT * FROM club_activities ORDER BY activity_date DESC, created_at DESC")->fetchAll();
$today = date('Y-m-d');
$upcomingEvents = [];
$previousEvents = [];

foreach ($events as $event) {
    $isPrevious = in_array($event['status'], ['completed'], true) || $event['event_date'] < $today;
    if ($isPrevious) {
        $previousEvents[] = $event;
    } elseif ($event['status'] !== 'cancelled') {
        $upcomingEvents[] = $event;
    }
}

function admin_event_asset_url(?string $path): string {
    $path = trim((string) $path);
    if ($path === '' || preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, '/')) {
        return $path;
    }
    return BASE_URL . '/' . ltrim($path, '/');
}

function admin_event_type_label(array $event): string {
    return ($event['event_type'] ?? '') === 'workshop' ? 'Workshop' : 'Event';
}

function render_admin_event_rows(array $events): void {
    foreach ($events as $ev): ?>
        <tr class="event-row" data-status="<?= h($ev['status']) ?>" data-type="<?= h(admin_event_type_label($ev)) ?>">
            <td>
                <div class="flex-cell">
                    <?php if ($ev['cover_image']): ?><img src="<?= h(admin_event_asset_url($ev['cover_image'])) ?>" class="thumb" alt=""><?php endif; ?>
                    <strong><?= h($ev['title']) ?></strong>
                </div>
            </td>
            <td><?= date('d M Y', strtotime($ev['event_date'])) ?><?= $ev['event_time'] ? '<br><span class="muted">'.date('g:i A', strtotime($ev['event_time'])).'</span>' : '' ?></td>
            <td class="muted"><?= h($ev['location'] ?? '-') ?></td>
            <td><span class="badge badge-info"><?= h(admin_event_type_label($ev)) ?></span></td>
            <td><?= status_badge($ev['status']) ?></td>
            <td>
                <div class="action-btns">
                    <button class="btn-icon btn-edit" title="Edit"
                        onclick="editEvent(<?= htmlspecialchars(json_encode($ev), ENT_QUOTES) ?>)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button class="btn-icon btn-success" title="Mark Completed"
                        onclick="setEventStatus(<?= (int) $ev['id'] ?>, 'completed')" <?= $ev['status']==='completed'?'disabled':'' ?>>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20,6 9,17 4,12"/></svg>
                    </button>
                    <button class="btn-icon btn-danger" title="Delete"
                        onclick="deleteEvent(<?= (int) $ev['id'] ?>, '<?= h($ev['title']) ?>')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/><path d="M10,11v6"/><path d="M14,11v6"/><path d="M9,6V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/></svg>
                    </button>
                </div>
            </td>
        </tr>
    <?php endforeach;
}

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
                <button class="btn btn-primary" onclick="openAddActivity()">+ Add Club Activity</button>
                <button class="theme-btn" id="themeToggle"><svg id="sunIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg><svg id="moonIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg></button>
            </div>
        </div>
        <div class="admin-content">
            <div class="page-header">
                <h1 class="page-title">Event & Workshop Management</h1>
            </div>

            <div class="table-card event-admin-controls">
                <div class="table-toolbar">
                    <input class="table-search" type="search" placeholder="Search events and activities..." id="eventSectionSearch">
                </div>
            </div>

            <div class="table-card event-admin-section">
                <div class="section-heading">
                    <div>
                        <h2>Upcoming Events and Workshops</h2>
                        <p class="muted">Items from the events table that are still upcoming or ongoing.</p>
                    </div>
                    <span class="badge badge-info"><?= count($upcomingEvents) ?> Items</span>
                </div>
                <div class="table-wrap">
                    <table class="data-table event-admin-table">
                        <thead>
                            <tr><th>Title</th><th>Date</th><th>Location</th><th>Type</th><th>Status</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php render_admin_event_rows($upcomingEvents); ?>
                        <?php if (empty($upcomingEvents)): ?>
                            <tr><td colspan="6"><div class="empty-state">No upcoming events or workshops yet.</div></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="table-card event-admin-section">
                <div class="section-heading">
                    <div>
                        <h2>Club Activities</h2>
                        <p class="muted">Items mapped from the club activities table and shown in the public events section.</p>
                    </div>
                    <span class="badge badge-info"><?= count($activities) ?> Items</span>
                </div>
                <div class="table-wrap">
                    <table class="data-table event-admin-table">
                        <thead>
                            <tr><th>Title</th><th>Date</th><th>Description</th><th>Source</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($activities as $activity): ?>
                            <tr class="event-row" data-status="activity" data-type="Club Activity">
                                <td>
                                    <div class="flex-cell">
                                        <?php if ($activity['cover_image']): ?><img src="<?= h(admin_event_asset_url($activity['cover_image'])) ?>" class="thumb" alt=""><?php endif; ?>
                                        <strong><?= h($activity['title']) ?></strong>
                                    </div>
                                </td>
                                <td><?= date('d M Y', strtotime($activity['activity_date'])) ?></td>
                                <td class="muted"><?= h(truncate($activity['description'], 90)) ?></td>
                                <td><span class="badge badge-info">Club Activity</span></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-icon btn-edit" title="Edit"
                                            onclick="editActivity(<?= htmlspecialchars(json_encode($activity), ENT_QUOTES) ?>)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
                                        <button class="btn-icon btn-danger" title="Delete"
                                            onclick="deleteActivity(<?= (int) $activity['id'] ?>, '<?= h($activity['title']) ?>')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/><path d="M10,11v6"/><path d="M14,11v6"/><path d="M9,6V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($activities)): ?>
                            <tr><td colspan="5"><div class="empty-state">No club activities yet.</div></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="table-card event-admin-section">
                <div class="section-heading">
                    <div>
                        <h2>Previous Events and Workshops</h2>
                        <p class="muted">Completed or past-dated items from the events table.</p>
                    </div>
                    <span class="badge badge-info"><?= count($previousEvents) ?> Items</span>
                </div>
                <div class="table-wrap">
                    <table class="data-table event-admin-table">
                        <thead>
                            <tr><th>Title</th><th>Date</th><th>Location</th><th>Type</th><th>Status</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php render_admin_event_rows($previousEvents); ?>
                        <?php if (empty($previousEvents)): ?>
                            <tr><td colspan="6"><div class="empty-state">No previous events or workshops yet.</div></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="table-card legacy-events-table" style="display:none">
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
                                        <?php if ($ev['cover_image']): ?><img src="<?= h(admin_event_asset_url($ev['cover_image'])) ?>" class="thumb" alt=""><?php endif; ?>
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
                    <label>Cover Image</label>
                    <input type="file" name="cover_image" accept="image/*" id="evImg">
                    <img id="evImgPreview" class="modal-image-preview" src="" style="display:none" alt="Event cover preview">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('eventModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Event</button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Club Activity Modal -->
<div class="modal-overlay" id="activityModal" style="display:none">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h3 class="modal-title" id="activityModalTitle">Add Club Activity</h3>
            <button class="modal-close" onclick="closeModal('activityModal')">✕</button>
        </div>
        <form id="activityForm" enctype="multipart/form-data">
            <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" id="activityId">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Activity Title *</label>
                        <input type="text" name="title" id="activityTitle" required>
                    </div>
                    <div class="form-group">
                        <label>Activity Date *</label>
                        <input type="date" name="activity_date" id="activityDate" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Short Description *</label>
                    <textarea name="description" id="activityDesc" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Full Content</label>
                    <textarea name="content" id="activityContent" rows="6" placeholder="Optional. If empty, the short description will be used."></textarea>
                </div>
                <div class="form-group">
                    <label>Cover Image</label>
                    <input type="file" name="cover_image" accept="image/*" id="activityImg">
                    <img id="activityImgPreview" class="modal-image-preview" src="" style="display:none" alt="Club activity cover preview">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('activityModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Club Activity</button>
            </div>
        </form>
    </div>
</div>

<div id="toast-container"></div>
<script>
const CSRF = '<?= csrf_token() ?>';
const SITE_BASE = '<?= BASE_URL ?>';

function assetUrl(path) {
    path = String(path || '');
    if (!path) return '';
    if (/^(https?:)?\/\//i.test(path) || path.startsWith('data:')) return path;
    if (path.startsWith('/')) return path;
    return SITE_BASE + '/' + path.replace(/^\/+/, '');
}

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
    document.getElementById('evDate').value     = ev.event_date;
    document.getElementById('evTime').value     = ev.event_time || '';
    document.getElementById('evLocation').value = ev.location || '';
    document.getElementById('evStatus').value   = ev.status;
    if (ev.cover_image) {
        const img = document.getElementById('evImgPreview');
        img.src = assetUrl(ev.cover_image); img.style.display = 'block';
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

function openAddActivity() {
    document.getElementById('activityModalTitle').textContent = 'Add Club Activity';
    document.getElementById('activityForm').reset();
    document.getElementById('activityId').value = '';
    document.getElementById('activityImgPreview').style.display = 'none';
    openModal('activityModal');
}

function editActivity(activity) {
    document.getElementById('activityModalTitle').textContent = 'Edit Club Activity';
    document.getElementById('activityId').value = activity.id;
    document.getElementById('activityTitle').value = activity.title;
    document.getElementById('activityDate').value = activity.activity_date;
    document.getElementById('activityDesc').value = activity.description;
    document.getElementById('activityContent').value = activity.content || '';
    if (activity.cover_image) {
        const img = document.getElementById('activityImgPreview');
        img.src = assetUrl(activity.cover_image);
        img.style.display = 'block';
    } else {
        document.getElementById('activityImgPreview').style.display = 'none';
    }
    openModal('activityModal');
}

document.getElementById('activityForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fd.append('action', document.getElementById('activityId').value ? 'activity_update' : 'activity_create');
    fetch(BASE_URL + '/admin/api/events-api.php', {method:'POST', body:fd})
    .then(r=>r.json()).then(d => {
        if(d.ok){closeModal('activityModal');toast('Club activity saved','success');setTimeout(()=>location.reload(),800);}
        else toast(d.error,'danger');
    });
});

function deleteActivity(id, title) {
    if(!confirm(`Delete club activity "${title}"?`)) return;
    fetch(BASE_URL + '/admin/api/events-api.php', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF},
        body: JSON.stringify({action:'activity_delete', id})
    }).then(r=>r.json()).then(d => {
        if(d.ok){toast('Club activity deleted','success');setTimeout(()=>location.reload(),600);}
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

document.getElementById('activityImg').addEventListener('change', function() {
    if(this.files[0]) {
        const r = new FileReader();
        r.onload = e => { const p=document.getElementById('activityImgPreview'); p.src=e.target.result; p.style.display='block'; };
        r.readAsDataURL(this.files[0]);
    }
});

document.getElementById('eventSectionSearch').addEventListener('input', function() {
    const term = this.value.toLowerCase();
    document.querySelectorAll('.event-admin-section .event-row').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
});
</script>
<script src="<?= BASE_URL ?>/admin/js/admin.js"></script>
</body></html>

