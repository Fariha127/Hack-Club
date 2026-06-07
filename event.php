<?php
require_once __DIR__ . '/public-data.php';

$events = public_rows("SELECT * FROM events WHERE status != 'cancelled' ORDER BY event_date ASC, created_at DESC");
$previousEvents = [];
$upcomingEvents = [];
$previousWorkshops = [];
$upcomingWorkshops = [];

foreach ($events as $event) {
    $isWorkshop = ($event['event_type'] ?? '') === 'workshop';
    $isPast = in_array($event['status'], ['completed'], true) || strtotime($event['event_date']) < strtotime(date('Y-m-d'));
    if ($isWorkshop && $isPast) {
        $previousWorkshops[] = $event;
    } elseif ($isWorkshop) {
        $upcomingWorkshops[] = $event;
    } elseif ($isPast) {
        $previousEvents[] = $event;
    } else {
        $upcomingEvents[] = $event;
    }
}

function render_event_cards(array $events, string $empty): void {
    if (empty($events)) {
        public_empty($empty);
        return;
    }
    foreach ($events as $event): ?>
        <article class="event-card">
            <span class="event-label"><?= public_h(ucfirst(str_replace('_', ' ', $event['event_type']))) ?></span>
            <h3><?= public_h($event['title']) ?></h3>
            <p class="event-date"><?= public_h(public_date($event['event_date'])) ?></p>
            <p><?= public_h($event['description']) ?></p>
            <?php if (!empty($event['location'])): ?>
                <p><strong>Location:</strong> <?= public_h($event['location']) ?></p>
            <?php endif; ?>
            <?php if (!empty($event['registration_link'])): ?>
                <p><a href="<?= public_h($event['registration_link']) ?>" target="_blank" rel="noopener noreferrer">Register</a></p>
            <?php endif; ?>
        </article>
    <?php endforeach;
}

public_header('Events', 'events');
?>

    <main class="container">
        <section class="hero event-hero">
            <p class="eyebrow">Events Calendar</p>
            <h1>Past wins, future plans, and the workshops that connect them.</h1>
            <p class="lead">Browse what HACK KUET has already delivered and what is coming next. Events and workshops are grouped from the admin dashboard.</p>
        </section>

        <section class="section event-section">
            <h2>Previous Events</h2>
            <div class="event-grid"><?php render_event_cards($previousEvents, 'Completed events from the admin dashboard will appear here.'); ?></div>
        </section>

        <section class="section event-section">
            <h2>Upcoming Events</h2>
            <div class="event-grid"><?php render_event_cards($upcomingEvents, 'Upcoming events from the admin dashboard will appear here.'); ?></div>
        </section>

        <section class="section event-section">
            <h2>Previous Workshops</h2>
            <div class="event-grid"><?php render_event_cards($previousWorkshops, 'Completed workshops from the admin dashboard will appear here.'); ?></div>
        </section>

        <section class="section event-section">
            <h2>Upcoming Workshops</h2>
            <div class="event-grid"><?php render_event_cards($upcomingWorkshops, 'Upcoming workshops from the admin dashboard will appear here.'); ?></div>
        </section>
    </main>

<?php public_footer('HACK KUET | Build fast. Test harder. Ship smarter.'); ?>
