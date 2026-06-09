<?php
require_once __DIR__ . '/public-data.php';

$events = public_rows("SELECT * FROM events WHERE status != 'cancelled' ORDER BY event_date DESC, created_at DESC");
$activities = public_rows("SELECT * FROM club_activities ORDER BY activity_date DESC, created_at DESC");
$previousItems = [];
$upcomingItems = [];

foreach ($events as $event) {
    $isPast = in_array($event['status'], ['completed'], true) || strtotime($event['event_date']) < strtotime(date('Y-m-d'));
    if ($isPast) {
        $previousItems[] = $event;
    } else {
        $upcomingItems[] = $event;
    }
}

usort($upcomingItems, fn($a, $b) => strcmp((string) $a['event_date'], (string) $b['event_date']));
usort($previousItems, fn($a, $b) => strcmp((string) $b['event_date'], (string) $a['event_date']));

function event_public_label(array $event): string {
    return ($event['event_type'] ?? '') === 'workshop' ? 'Workshop' : 'Event';
}

function render_event_cards(array $events, string $empty): void {
    if (empty($events)) {
        public_empty($empty);
        return;
    }
    foreach ($events as $event): ?>
        <article class="event-card">
            <?php if (!empty($event['cover_image'])): ?>
                <img class="event-thumb" src="<?= public_h($event['cover_image']) ?>" alt="<?= public_h($event['title']) ?>">
            <?php endif; ?>
            <div class="event-card-body">
                <div class="event-card-topline">
                    <span class="event-label"><?= public_h(event_public_label($event)) ?></span>
                    <span class="event-date"><?= public_h(date('D, j M Y', strtotime($event['event_date']))) ?></span>
                </div>
                <h3><?= public_h($event['title']) ?></h3>
                <p><?= public_h($event['description']) ?></p>
            <?php if (!empty($event['location'])): ?>
                <p><strong>Location:</strong> <?= public_h($event['location']) ?></p>
            <?php endif; ?>
            <?php if (!empty($event['registration_link'])): ?>
                <p><a href="<?= public_h($event['registration_link']) ?>" target="_blank" rel="noopener noreferrer">Register</a></p>
            <?php endif; ?>
                <?php if (!empty($event['full_description'])): ?>
                    <details class="event-details">
                        <summary>View details</summary>
                        <div><?= nl2br(public_h($event['full_description'])) ?></div>
                    </details>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach;
}

function render_activity_cards(array $activities, string $empty): void {
    if (empty($activities)) {
        public_empty($empty);
        return;
    }
    foreach ($activities as $activity): ?>
        <article class="event-card activity-card">
            <?php if (!empty($activity['cover_image'])): ?>
                <img class="event-thumb" src="<?= public_h($activity['cover_image']) ?>" alt="<?= public_h($activity['title']) ?>">
            <?php endif; ?>
            <div class="event-card-body">
                <span class="event-date"><?= public_h(date('D, j M Y', strtotime($activity['activity_date']))) ?></span>
                <h3><?= public_h($activity['title']) ?></h3>
                <a class="back-link-btn activity-detail-btn" href="activity-detail.php?slug=<?= urlencode($activity['slug']) ?>">View details</a>
            </div>
        </article>
    <?php endforeach;
}

public_header('Events', 'events');
?>

    <main class="container">
        <section class="hero event-hero">
            <p class="eyebrow">Events Calendar</p>
            <h1>Past wins, future plans, and the workshops that connect them.</h1>
            <p class="lead">Browse what HACK KUET has already delivered and what is coming next. Events and workshops are grouped by schedule, while club activities show the regular community work that keeps the lab active between major programs.</p>
        </section>

        <section class="section event-section">
            <h2>Upcoming Events and Workshops</h2>
            <div class="event-grid"><?php render_event_cards($upcomingItems, 'Upcoming events and workshops from the admin dashboard will appear here.'); ?></div>
        </section>

        <section class="section event-section">
            <h2>Club Activities</h2>
            <div class="event-grid"><?php render_activity_cards($activities, 'Club activity updates will appear here.'); ?></div>
        </section>

        <section class="section event-section">
            <h2>Previous Events and Workshops</h2>
            <div class="event-grid event-grid-single"><?php render_event_cards($previousItems, 'Completed events and workshops from the admin dashboard will appear here.'); ?></div>
        </section>
    </main>

<?php public_footer('HACK KUET | Build fast. Test harder. Ship smarter.'); ?>
