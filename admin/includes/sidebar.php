<?php
$admin   = current_admin();
$current = basename($_SERVER['PHP_SELF']);
$module  = basename(dirname($_SERVER['PHP_SELF']));

function nav_link(string $href, string $label, string $icon, string $current, string $match): string {
    $active = (basename($href) === $current || str_contains($href, $current)) ? ' active' : '';
    return '<a href="' . $href . '" class="nav-link' . $active . '">' . $icon . '<span>' . $label . '</span></a>';
}

$icons = [
    'dashboard' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
    'members'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
    'blogs'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10,9 9,9 8,9"/></svg>',
    'executives'=> '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
    'projects'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16,18 22,12 16,6"/><polyline points="8,6 2,12 8,18"/></svg>',
    'events'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
    'contact'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
    'logout'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
];
?>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="brand-icon"></div>
            <div class="brand-text">HACK Admin</div>
        </div>
        <button class="sidebar-close" id="sidebarClose" aria-label="Close menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Overview</div>
        <?= nav_link(BASE_URL.'/admin/dashboard.php', 'Dashboard', $icons['dashboard'], $current, 'dashboard') ?>

        <div class="nav-section-label">Management</div>
        <?= nav_link(BASE_URL.'/admin/modules/members.php',    'Members',     $icons['members'],    $current, 'members') ?>
        <?= nav_link(BASE_URL.'/admin/modules/blogs.php',      'Blogs',       $icons['blogs'],      $current, 'blogs') ?>
        <?= nav_link(BASE_URL.'/admin/modules/executives.php', 'Executives',  $icons['executives'], $current, 'executives') ?>
        <?= nav_link(BASE_URL.'/admin/modules/projects.php',   'Projects',    $icons['projects'],   $current, 'projects') ?>
        <?= nav_link(BASE_URL.'/admin/modules/events.php',     'Events',      $icons['events'],     $current, 'events') ?>

        <div class="nav-section-label">Settings</div>
        <?= nav_link(BASE_URL.'/admin/modules/contact.php', 'Contact Info', $icons['contact'], $current, 'contact') ?>
    </nav>

    <div class="sidebar-footer">
        <div class="admin-user-info">
            <div class="user-avatar"><?= strtoupper(substr($admin['name'], 0, 1)) ?></div>
            <div>
                <div class="user-name"><?= h($admin['name']) ?></div>
                <div class="user-role"><?= h(str_replace('_', ' ', $admin['role'])) ?></div>
            </div>
        </div>
        <a href="<?= BASE_URL ?>/admin/logout.php" class="logout-btn" title="Logout">
            <?= $icons['logout'] ?>
        </a>
    </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
