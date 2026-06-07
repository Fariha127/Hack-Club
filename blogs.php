<?php
require_once __DIR__ . '/public-data.php';

$blogs = public_rows("SELECT * FROM blogs WHERE status = 'published' ORDER BY COALESCE(published_at, submitted_at) DESC");
public_header('Blogs', 'blogs');
?>

    <main class="container">
        <section class="hero">
            <p class="eyebrow">Club Notes</p>
            <h1>Build logs, reflections, and lessons from the workshop floor.</h1>
            <p class="lead">Every project leaves behind practical insights. Published admin blog posts appear here automatically.</p>
        </section>

        <section class="section">
            <div class="grid three">
                <?php if (empty($blogs)): ?>
                    <?php public_empty('Published blog posts from the admin dashboard will appear here.'); ?>
                <?php endif; ?>

                <?php foreach ($blogs as $blog): ?>
                    <article class="card">
                        <span class="meta"><?= public_h(public_date($blog['published_at'] ?: $blog['submitted_at'])) ?></span>
                        <h3><?= public_h($blog['title']) ?></h3>
                        <p><?= public_h($blog['excerpt'] ?: substr(strip_tags($blog['content']), 0, 220)) ?></p>
                        <?php if (!empty($blog['content'])): ?>
                            <p><?= public_h(substr(strip_tags($blog['content']), 0, 420)) ?></p>
                        <?php endif; ?>
                        <?php $chips = public_chips($blog['tags'] ?? ''); ?>
                        <?php if (!empty($chips)): ?>
                            <div class="chips">
                                <?php foreach ($chips as $chip): ?>
                                    <span class="chip"><?= public_h($chip) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

<?php public_footer('HACK KUET | Sharing process, not just results.'); ?>
