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
            <div class="grid two">
                <?php if (empty($projects)): ?>
                    <?php public_empty('Projects added in the admin dashboard will appear here automatically.'); ?>
                <?php endif; ?>

                <?php foreach ($projects as $project): ?>
                    <article class="card">
                        <span class="meta"><?= public_h(ucfirst(str_replace('_', ' ', $project['project_status']))) ?></span>
                        <h3><?= public_h($project['title']) ?></h3>
                        <p><?= public_h($project['description']) ?></p>
                        <?php if (!empty($project['full_description'])): ?>
                            <p><?= public_h($project['full_description']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($project['team_members'])): ?>
                            <p><strong>Team:</strong> <?= public_h($project['team_members']) ?></p>
                        <?php endif; ?>
                        <?php $chips = public_chips($project['technologies'] ?? ''); ?>
                        <?php if (!empty($chips)): ?>
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
                <?php endforeach; ?>
            </div>
        </section>
    </main>

<?php public_footer('HACK KUET | Turning prototypes into repeatable systems.'); ?>
