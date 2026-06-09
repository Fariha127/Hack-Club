<?php
require_once __DIR__ . '/public-data.php';

$projects = public_rows("SELECT * FROM projects ORDER BY display_order ASC, created_at DESC");
public_header('Projects', 'projects');
?>

    <main class="container">
        <section class="hero">
            <p class="eyebrow">Project Bay</p>
            <h1>Recent hardware systems built inside HACK.</h1>
            <p class="lead">These projects combine PCB design, firmware architecture, sensor integration, and practical testing in real campus environments.</p>
        </section>

        <section class="section">
            <article class="card project-intro">
                <span class="meta">For Viewers</span>
                <p>Every HACK KUET project begins with a practical problem and grows through repeated testing. Some teams build robots, some work on safety systems, and others focus on monitoring, attendance, assistive technology, or automation. What connects them is the same workshop habit: build a small version first, test it honestly, learn from the failure, and improve the next version.</p>
                <p>The projects below are snapshots of that process. Each card opens a full project page with the project idea, technical direction, team members, departments, mentors, and tools used. These are not just finished objects; they are examples of how students turn sensors, circuits, code, and mechanical design into working systems.</p>
            </article>
        </section>

        <section class="section">
            <div class="grid three">
                <?php if (empty($projects)): ?>
                    <?php public_empty('Projects added in the admin dashboard will appear here automatically.'); ?>
                <?php endif; ?>

                <?php foreach ($projects as $project): ?>
                    <a class="card-link project-card-link" href="project-detail.php?slug=<?= urlencode($project['slug']) ?>">
                        <article class="card project-card">
                            <?php if (!empty($project['cover_image'])): ?>
                                <img class="project-thumb" src="<?= public_h($project['cover_image']) ?>" alt="<?= public_h($project['title']) ?> thumbnail" onerror="this.remove()">
                            <?php endif; ?>
                            <span class="meta"><?= public_h(ucfirst(str_replace('_', ' ', $project['project_status']))) ?></span>
                            <h3><?= public_h($project['title']) ?></h3>
                            <p><?= public_h($project['description']) ?></p>
                            <?php $chips = public_chips($project['technologies'] ?? ''); ?>
                            <?php if (!empty($chips)): ?>
                                <div class="chips">
                                    <?php foreach (array_slice($chips, 0, 3) as $chip): ?>
                                        <span class="chip"><?= public_h($chip) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </article>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

<?php public_footer('HACK KUET | Turning prototypes into repeatable systems.'); ?>
