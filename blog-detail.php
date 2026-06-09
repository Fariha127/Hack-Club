<?php
require_once __DIR__ . '/public-data.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
if ($slug === '') {
    header('Location: blogs.php');
    exit;
}

$rows = public_rows("SELECT * FROM blogs WHERE slug = ? AND status = 'published' LIMIT 1", [$slug]);
$blog = $rows[0] ?? null;
if (!$blog) {
    public_header('Blog Not Found', 'blogs');
    ?>
    <main class="container">
        <section class="hero">
            <p class="eyebrow">Blog</p>
            <h1>Blog not found</h1>
            <p class="lead">This post may still be waiting for admin approval or may have been removed.</p>
        </section>
        <section class="section back-link-section">
            <a class="back-link-btn" href="blogs.php">Back to all blogs</a>
        </section>
    </main>
    <?php
    public_footer('HACK KUET | Sharing process, not just results.');
    exit;
}

$images = array_values(array_filter([$blog['cover_image'] ?? '', $blog['image_2'] ?? '']));
public_header($blog['title'], 'blogs');
?>

    <main class="container">
        <section class="hero">
            <p class="eyebrow"><?= public_h(public_date($blog['published_at'] ?: $blog['submitted_at'])) ?></p>
            <h1><?= public_h($blog['title']) ?></h1>
            <p class="blog-byline">By <?= public_h($blog['author_name']) ?><?= !empty($blog['author_university']) ? ' | ' . public_h($blog['author_university']) : '' ?></p>
            <?php if (!empty($blog['excerpt'])): ?>
                <p class="lead"><?= public_h($blog['excerpt']) ?></p>
            <?php endif; ?>
        </section>

        <section class="section">
            <article class="card blog-intro">
                <?php if ($images): ?>
                    <div class="blog-gallery">
                        <?php foreach ($images as $image): ?>
                            <img src="<?= public_h($image) ?>" alt="<?= public_h($blog['title']) ?> image" onerror="this.remove()">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="blog-content">
                    <?php foreach (preg_split('/\R{2,}/', trim($blog['content'])) as $paragraph): ?>
                        <p><?= nl2br(public_h($paragraph)) ?></p>
                    <?php endforeach; ?>
                </div>
                <?php $chips = public_chips($blog['tags'] ?? ''); ?>
                <?php if ($chips): ?>
                    <div class="chips">
                        <?php foreach ($chips as $chip): ?>
                            <span class="chip"><?= public_h($chip) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </article>
        </section>
        <section class="section back-link-section">
            <a class="back-link-btn" href="blogs.php">Back to all blogs</a>
        </section>
    </main>

<?php public_footer('HACK KUET | Sharing process, not just results.'); ?>
