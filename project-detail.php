<?php
require_once __DIR__ . '/public-data.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
if ($slug === '') {
    header('Location: project.php');
    exit;
}

$rows = public_rows("SELECT * FROM projects WHERE slug = ? LIMIT 1", [$slug]);
$project = $rows[0] ?? null;
if (!$project) {
    public_header('Project Not Found', 'projects');
    ?>
    <main class="container">
        <section class="hero">
            <p class="eyebrow">Project Bay</p>
            <h1>Project not found</h1>
            <p class="lead">This project may have been removed or has not been added yet.</p>
        </section>
        <section class="section back-link-section">
            <a class="back-link-btn" href="project.php">Back to all projects</a>
        </section>
    </main>
    <?php
    public_footer('HACK KUET | Turning prototypes into repeatable systems.');
    exit;
}

public_header($project['title'], 'projects');
?>

    <main class="container">
        <section class="hero">
            <p class="eyebrow"><?= public_h(ucfirst(str_replace('_', ' ', $project['project_status']))) ?></p>
            <h1><?= public_h($project['title']) ?></h1>
            <p class="lead"><?= public_h($project['description']) ?></p>
        </section>

        <section class="section">
            <article class="card project-detail-card">
                <?php if (!empty($project['cover_image'])): ?>
                    <img class="project-detail-image" src="<?= public_h($project['cover_image']) ?>" alt="<?= public_h($project['title']) ?>" onerror="this.remove()">
                <?php endif; ?>
                <span class="meta">About The Project</span>
                <div class="project-content">
                    <?php foreach (preg_split('/\R{2,}/', trim((string) $project['full_description'])) as $paragraph): ?>
                        <?php if (trim($paragraph) !== ''): ?>
                            <p><?= nl2br(public_h($paragraph)) ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </article>
        </section>

        <section class="section">
            <div class="grid two">
                <article class="card">
                    <span class="meta">Team Members</span>
                    <p><?= public_h($project['team_members'] ?: 'Team information will be added soon.') ?></p>
                </article>
                <article class="card">
                    <span class="meta">Mentors</span>
                    <p><?= public_h($project['mentors'] ?? 'Mentor information will be added soon.') ?></p>
                </article>
            </div>
        </section>

        <section class="section">
            <article class="card">
                <span class="meta">Tools And Focus</span>
                <?php $chips = public_chips($project['technologies'] ?? ''); ?>
                <?php if ($chips): ?>
                    <div class="chips">
                        <?php foreach ($chips as $chip): ?>
                            <span class="chip"><?= public_h($chip) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($project['github_link'])): ?>
                    <p><a href="<?= public_h($project['github_link']) ?>" target="_blank" rel="noopener noreferrer">View project repository</a></p>
                <?php endif; ?>
            </article>
        </section>
        <section class="section back-link-section">
            <a class="back-link-btn" href="project.php">Back to all projects</a>
        </section>
    </main>

<?php public_footer('HACK KUET | Turning prototypes into repeatable systems.'); ?>
