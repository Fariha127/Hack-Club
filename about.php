<?php
require_once __DIR__ . '/public-data.php';

$executives = public_rows("SELECT * FROM executives WHERE is_active = 1 ORDER BY display_order ASC, created_at ASC");
public_header('About', 'about');
?>

    <main class="container">
        <section class="hero">
            <p class="eyebrow">Who We Are</p>
            <h1>Hardware Acceleration Club of KUET builds practical engineers.</h1>
            <p class="lead">We are a student-led community where curiosity meets fabrication. From embedded systems to rover control boards, HACK helps members move from diagrams to deployable hardware.</p>
        </section>

        <section class="section team-section">
            <h2>Our Team</h2>
            <p class="lead">Leadership and moderators maintained from the admin dashboard.</p>
            <div class="team-grid">
                <?php if (empty($executives)): ?>
                    <?php public_empty('Active executives added in the admin dashboard will appear here.'); ?>
                <?php endif; ?>

                <?php foreach ($executives as $person): ?>
                    <article class="team-card card">
                        <?php if (!empty($person['photo'])): ?>
                            <img src="<?= public_h($person['photo']) ?>" alt="<?= public_h($person['name']) ?>">
                        <?php endif; ?>
                        <h3><?= public_h($person['name']) ?></h3>
                        <p><?= public_h($person['role']) ?></p>
                        <?php if (!empty($person['department'])): ?>
                            <p class="meta"><?= public_h($person['department']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($person['bio'])): ?>
                            <p><?= public_h($person['bio']) ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section">
            <div class="grid three">
                <article class="card">
                    <h3>Mission</h3>
                    <p>Create a high-energy environment where students learn by building, debugging, and showcasing impactful hardware systems.</p>
                </article>
                <article class="card">
                    <h3>Vision</h3>
                    <p>Develop a generation of KUET innovators capable of shipping reliable, efficient, and scalable hardware products.</p>
                </article>
                <article class="card">
                    <h3>Culture</h3>
                    <p>Open collaboration, rapid experimentation, and mentorship-first learning across batches and disciplines.</p>
                </article>
            </div>
        </section>

        <section class="section">
            <article class="card">
                <h2>Club Journey</h2>
                <div class="timeline">
                    <div class="timeline-item">
                        <span class="meta">2022</span>
                        <h3>Community Launch</h3>
                        <p>Started with a small group of hardware enthusiasts running weekend soldering and microcontroller sessions.</p>
                    </div>
                    <div class="timeline-item">
                        <span class="meta">2024</span>
                        <h3>Inter-University Build Season</h3>
                        <p>Expanded to competitive build tracks including IoT prototypes and robotics subsystems.</p>
                    </div>
                    <div class="timeline-item">
                        <span class="meta">2026</span>
                        <h3>Research to Product Focus</h3>
                        <p>Current focus includes accelerating ideas from lab notebooks into demo-ready, field-tested systems.</p>
                    </div>
                </div>
            </article>
        </section>
    </main>

<?php public_footer('HACK KUET | Build fast. Test harder. Ship smarter.'); ?>
