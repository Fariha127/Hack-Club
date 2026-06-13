<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_admin();

$db = get_db();

// Stats
$stats = [
    'members'      => (int) $db->query("SELECT COUNT(*) FROM members")->fetchColumn(),
    'applications' => (int) $db->query("SELECT COUNT(*) FROM membership_applications WHERE status='pending'")->fetchColumn(),
    'blogs'        => (int) $db->query("SELECT COUNT(*) FROM blogs WHERE status='pending'")->fetchColumn(),
    'projects'     => (int) $db->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
    'events'       => (int) $db->query("SELECT COUNT(*) FROM events WHERE status='upcoming'")->fetchColumn(),
    'executives'   => (int) $db->query("SELECT COUNT(*) FROM executives WHERE is_active=1")->fetchColumn(),
    'messages'     => (int) $db->query("SELECT COUNT(*) FROM contact_messages WHERE status='unread'")->fetchColumn(),
];

// Recent applications
$recentApps = $db->query("SELECT * FROM membership_applications ORDER BY submitted_at DESC LIMIT 5")->fetchAll();

// Recent blog submissions
$recentBlogs = $db->query("SELECT * FROM blogs ORDER BY submitted_at DESC LIMIT 5")->fetchAll();

// Upcoming events
$upcomingEvents = $db->query("SELECT * FROM events WHERE status='upcoming' ORDER BY event_date ASC LIMIT 3")->fetchAll();

// Recent viewer messages
$recentMessages = $db->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC LIMIT 5")->fetchAll();

$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-layout">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

    <div class="admin-main">
        <div class="admin-topbar">
            <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div class="topbar-title">Dashboard</div>
            <div class="topbar-actions">
                <button class="theme-btn" id="themeToggle" aria-label="Toggle theme">
                    <svg id="sunIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                    <svg id="moonIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
            </div>
        </div>

        <div class="admin-content">
            <div class="page-header">
                <h1 class="page-title">Welcome back, <?= h(explode(' ', current_admin()['name'])[0]) ?></h1>
                <p class="page-subtitle">Here's what's happening with HACK KUET today.</p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card stat-green">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $stats['members'] ?></div>
                        <div class="stat-label">Total Members</div>
                    </div>
                </div>
                <div class="stat-card stat-yellow">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $stats['applications'] ?></div>
                        <div class="stat-label">Pending Applications</div>
                    </div>
                </div>
                <div class="stat-card stat-blue">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $stats['blogs'] ?></div>
                        <div class="stat-label">Pending Blogs</div>
                    </div>
                </div>
                <div class="stat-card stat-purple">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16,18 22,12 16,6"/><polyline points="8,6 2,12 8,18"/></svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $stats['projects'] ?></div>
                        <div class="stat-label">Projects</div>
                    </div>
                </div>
                <div class="stat-card stat-teal">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $stats['events'] ?></div>
                        <div class="stat-label">Upcoming Events</div>
                    </div>
                </div>
                <div class="stat-card stat-orange">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $stats['executives'] ?></div>
                        <div class="stat-label">Active Executives</div>
                    </div>
                </div>
                <div class="stat-card stat-blue">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $stats['messages'] ?></div>
                        <div class="stat-label">Unread Messages</div>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <!-- Recent Applications -->
                <div class="dash-panel">
                    <div class="panel-header">
                        <h2 class="panel-title">Recent Applications</h2>
                        <a href="<?= BASE_URL ?>/admin/modules/members.php" class="panel-link">View all</a>
                    </div>
                    <?php if ($recentApps): ?>
                    <div class="mini-table-wrap">
                        <table class="mini-table">
                            <thead><tr><th>Name</th><th>Dept</th><th>Area of Interest</th><th>Why Join</th><th>Status</th><th>When</th></tr></thead>
                            <tbody>
                            <?php foreach ($recentApps as $app): ?>
                                <tr>
                                    <td><strong><?= h($app['full_name']) ?></strong></td>
                                    <td><?= h($app['department']) ?></td>
                                    <td><?= h(truncate($app['area_of_interest'] ?? '', 35)) ?></td>
                                    <td><?= h(truncate($app['why_join'] ?? '', 45)) ?></td>
                                    <td><?= status_badge($app['status']) ?></td>
                                    <td class="muted"><?= time_ago($app['submitted_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <div class="empty-state-sm">No applications yet.</div>
                    <?php endif; ?>
                </div>

                <!-- Recent Blog Submissions -->
                <div class="dash-panel">
                    <div class="panel-header">
                        <h2 class="panel-title">Recent Blog Submissions</h2>
                        <a href="<?= BASE_URL ?>/admin/modules/blogs.php" class="panel-link">View all</a>
                    </div>
                    <?php if ($recentBlogs): ?>
                    <div class="mini-table-wrap">
                        <table class="mini-table">
                            <thead><tr><th>Title</th><th>Author</th><th>Status</th><th>When</th></tr></thead>
                            <tbody>
                            <?php foreach ($recentBlogs as $b): ?>
                                <tr>
                                    <td><strong><?= h(truncate($b['title'], 40)) ?></strong></td>
                                    <td><?= h($b['author_name']) ?></td>
                                    <td><?= status_badge($b['status']) ?></td>
                                    <td class="muted"><?= time_ago($b['submitted_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <div class="empty-state-sm">No blog submissions yet.</div>
                    <?php endif; ?>
                </div>

                <!-- Recent Viewer Messages -->
                <div class="dash-panel">
                    <div class="panel-header">
                        <h2 class="panel-title">Recent Viewer Messages</h2>
                        <a href="<?= BASE_URL ?>/admin/modules/contact.php" class="panel-link">View all</a>
                    </div>
                    <?php if ($recentMessages): ?>
                    <div class="mini-table-wrap">
                        <table class="mini-table">
                            <thead><tr><th>Name</th><th>Email</th><th>Status</th><th>When</th></tr></thead>
                            <tbody>
                            <?php foreach ($recentMessages as $m): ?>
                                <tr>
                                    <td><strong><?= h($m['full_name']) ?></strong></td>
                                    <td><?= h($m['email']) ?></td>
                                    <td><?= status_badge($m['status']) ?></td>
                                    <td class="muted"><?= time_ago($m['submitted_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <div class="empty-state-sm">No viewer messages yet.</div>
                    <?php endif; ?>
                </div>

                <!-- Upcoming Events -->
                <div class="dash-panel">
                    <div class="panel-header">
                        <h2 class="panel-title">Upcoming Events</h2>
                        <a href="<?= BASE_URL ?>/admin/modules/events.php" class="panel-link">Manage</a>
                    </div>
                    <?php if ($upcomingEvents): ?>
                        <?php foreach ($upcomingEvents as $ev): ?>
                        <div class="event-mini">
                            <div class="event-mini-date">
                                <div class="ev-day"><?= date('d', strtotime($ev['event_date'])) ?></div>
                                <div class="ev-mon"><?= date('M', strtotime($ev['event_date'])) ?></div>
                            </div>
                            <div class="event-mini-info">
                                <div class="ev-title"><?= h($ev['title']) ?></div>
                                <div class="ev-meta muted"><?= h($ev['location'] ?? 'TBD') ?> · <?= h(ucfirst($ev['event_type'])) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state-sm">No upcoming events.</div>
                    <?php endif; ?>
                </div>

                <!-- Quick Actions -->
                <div class="dash-panel">
                    <div class="panel-header">
                        <h2 class="panel-title">Quick Actions</h2>
                    </div>
                    <div class="quick-actions">
                        <a href="<?= BASE_URL ?>/admin/modules/members.php?tab=applications" class="quick-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                            Review Applications
                        </a>
                        <a href="<?= BASE_URL ?>/admin/modules/blogs.php" class="quick-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Review Blogs
                        </a>
                        <a href="<?= BASE_URL ?>/admin/modules/events.php?action=add" class="quick-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg>
                            Add New Event
                        </a>
                        <a href="<?= BASE_URL ?>/admin/modules/projects.php?action=add" class="quick-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Add New Project
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/admin/js/admin.js"></script>
</body>
</html>
