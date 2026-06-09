<?php
require_once __DIR__ . '/public-data.php';

$slug = trim($_GET['slug'] ?? '');
$activity = null;
if ($slug !== '') {
    $rows = public_rows("SELECT * FROM club_activities WHERE slug = ? LIMIT 1", [$slug]);
    $activity = $rows[0] ?? null;
}

if (!$activity) {
    public_header('Activity Not Found', 'events');
    ?>
    <main class="container">
        <section class="hero">
            <p class="eyebrow">Club Activity</p>
            <h1>Activity not found</h1>
            <p class="lead">The activity you are looking for may have been moved or removed.</p>
        </section>
        <section class="section back-link-section">
            <a class="back-link-btn" href="event.php">Back to all events</a>
        </section>
    </main>
    <?php
    public_footer('HACK KUET | Build fast. Test harder. Ship smarter.');
    exit;
}

$images = array_values(array_filter(array_map('trim', explode('|', (string) ($activity['gallery_images'] ?? '')))));
if (empty($images) && !empty($activity['cover_image'])) {
    $images = [$activity['cover_image']];
}

public_header($activity['title'], 'events');
?>

    <main class="container">
        <section class="hero">
            <p class="eyebrow">Club Activity</p>
            <h1><?= public_h($activity['title']) ?></h1>
            <p class="lead"><?= public_h(date('D, j M Y', strtotime($activity['activity_date']))) ?></p>
        </section>

        <article class="card activity-detail-card">
            <?php if (!empty($images)): ?>
                <div class="activity-gallery">
                    <?php foreach ($images as $image): ?>
                        <img src="<?= public_h($image) ?>" alt="<?= public_h($activity['title']) ?>">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="activity-content">
                <?= nl2br(public_h($activity['content'])) ?>
            </div>

            <?php if (!empty($activity['registration_link'])): ?>
                <p><a class="back-link-btn" href="<?= public_h($activity['registration_link']) ?>" target="_blank" rel="noopener noreferrer">Registration link</a></p>
            <?php endif; ?>
        </article>

        <section class="section back-link-section">
            <a class="back-link-btn" href="event.php">Back to all events</a>
        </section>
    </main>

<?php public_footer('HACK KUET | Build fast. Test harder. Ship smarter.'); ?>
