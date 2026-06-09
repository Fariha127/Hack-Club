<?php
require_once __DIR__ . '/public-data.php';

$contacts = public_contact_info();
$email = $contacts['email']['value'] ?? 'hack.kuet.club@gmail.com';
$facebookPage = $contacts['facebook_page']['value'] ?? '';
$facebookGroup = $contacts['facebook_group']['value'] ?? '';
$messageStatus = $_GET['contact'] ?? '';
$submissionStatus = $_GET['submission'] ?? '';

$executives = public_rows("SELECT * FROM executives WHERE is_active = 1 ORDER BY display_order ASC, created_at ASC");
$events = public_rows("SELECT * FROM events WHERE status != 'cancelled' ORDER BY event_date DESC, created_at DESC");
$activities = public_rows("SELECT * FROM club_activities ORDER BY activity_date DESC, created_at DESC");
$projects = public_rows("SELECT * FROM projects ORDER BY display_order ASC, created_at DESC");
$blogs = public_rows("SELECT * FROM blogs WHERE status = 'published' ORDER BY COALESCE(published_at, submitted_at) DESC");

$upcomingEvents = [];
$previousEvents = [];
foreach ($events as $event) {
    $isPast = in_array($event['status'], ['completed'], true) || strtotime($event['event_date']) < strtotime(date('Y-m-d'));
    if ($isPast) {
        $previousEvents[] = $event;
    } else {
        $upcomingEvents[] = $event;
    }
}
usort($upcomingEvents, fn($a, $b) => strcmp((string) $a['event_date'], (string) $b['event_date']));
usort($previousEvents, fn($a, $b) => strcmp((string) $b['event_date'], (string) $a['event_date']));

function portfolio_date(?string $date, string $format = 'D, j M Y'): string {
    $time = strtotime((string) $date);
    return $time ? date($format, $time) : (string) $date;
}

function portfolio_label(array $event): string {
    return ($event['event_type'] ?? '') === 'workshop' ? 'Workshop' : 'Event';
}

function portfolio_paragraphs(?string $text): void {
    foreach (preg_split('/\R{2,}/', trim((string) $text)) as $paragraph) {
        if (trim($paragraph) !== '') {
            echo '<p>' . nl2br(public_h($paragraph)) . '</p>';
        }
    }
}

$contactIcons = [
    'email' => ['Mail us!', 'Email icon', 'email.jpg', 'mailto:' . $email],
    'facebook_page' => ['Follow our facebook page!', 'Facebook page image', 'facebook-page.jpg', $facebookPage],
    'facebook_group' => ['Join on our facebook group!', 'Facebook group image', 'facebook-group.jpg', $facebookGroup],
    'address' => ['Visit our club!', 'Address image', 'address.jpg', 'https://maps.app.goo.gl/aqtLwX2DVue4XFGp6'],
];

$teamGroups = [
    'Mentors' => array_values(array_filter($executives, fn($person) => ($person['category'] ?? '') === 'moderator')),
    'Presidents' => array_values(array_filter($executives, fn($person) => ($person['category'] ?? '') === 'president')),
    'Vice Presidents' => array_values(array_filter($executives, fn($person) => ($person['category'] ?? '') === 'vice_president')),
];

public_header('Portfolio', 'home');
?>

    <section class="portfolio-section portfolio-home" id="home">
        <div class="slider" aria-roledescription="carousel">
            <div class="slides">
                <div class="slide"><img src="Picture 1.jpg" alt="Members soldering in a workshop"></div>
                <div class="slide"><img src="Picture 3.jpg" alt="Prototype rover on a test field"></div>
                <div class="slide"><img src="Picture 2.jpg" alt="PCB assembly and inspection"></div>
            </div>
            <button class="slider-prev" aria-label="Previous slide">&lsaquo;</button>
            <button class="slider-next" aria-label="Next slide">&rsaquo;</button>
            <div class="slider-dots" aria-hidden="false"></div>
        </div>

        <div class="container">
            <section class="hero">
                <p class="eyebrow">Hardware Acceleration Club</p>
                <h1>Designing, building, and accelerating hardware innovation at KUET.</h1>
                <p class="lead">HACK is a collaborative engineering community where students turn ambitious ideas into tested systems through hands-on learning, project sprints, workshops, and team mentorship.</p>
                <div class="hero-actions">
                    <button class="btn membership-open" id="membership-open" type="button">Become a Member</button>
                </div>
                <p id="membership-status" class="form-status membership-status" aria-live="polite"></p>
            </section>

            <section class="section">
                <div class="grid three">
                    <article class="card">
                        <h3>Learn by Building</h3>
                        <p>Work directly with microcontrollers, sensors, control systems, and PCB workflows from week one.</p>
                    </article>
                    <article class="card">
                        <h3>Compete and Collaborate</h3>
                        <p>Join multidisciplinary teams for robotics, IoT, and embedded competitions across campus and beyond.</p>
                    </article>
                    <article class="card">
                        <h3>Grow with Mentorship</h3>
                        <p>Senior members provide guidance on architecture, debugging strategy, and project planning.</p>
                    </article>
                </div>
            </section>
        </div>
    </section>

    <main class="container portfolio-main">
        <section class="hero portfolio-section" id="about">
            <p class="eyebrow">Who We Are</p>
            <h1>Hardware Acceleration Club of KUET builds practical engineers.</h1>
            <p class="lead">We are a student-led community where curiosity meets fabrication. From embedded systems to rover control boards, HACK helps members move from diagrams to deployable hardware.</p>
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

        <section class="section team-section">
            <h2>Our Team</h2>
            <p class="lead">Leadership and moderators maintained from the admin dashboard.</p>
            <?php if (empty($executives)): ?>
                <?php public_empty('Active executives added in the admin dashboard will appear here.'); ?>
            <?php endif; ?>
            <?php foreach ($teamGroups as $groupTitle => $people): ?>
                <?php if (empty($people)) continue; ?>
                <div class="team-group">
                    <h3 class="team-group-title"><?= public_h($groupTitle) ?></h3>
                    <div class="team-grid">
                        <?php foreach ($people as $person): ?>
                            <article class="team-card card">
                                <?php if (!empty($person['photo'])): ?>
                                    <img src="<?= public_h($person['photo']) ?>" alt="<?= public_h($person['name']) ?>">
                                <?php endif; ?>
                                <h3><?= public_h($person['name']) ?></h3>
                                <p><?= public_h($person['role']) ?></p>
                                <?php if (!empty($person['department'])): ?>
                                    <p class="meta"><?= public_h($person['department']) ?></p>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
        </section>

        <section class="hero portfolio-section" id="events">
            <p class="eyebrow">Events Calendar</p>
            <h1>Past wins, future plans, and the workshops that connect them.</h1>
            <p class="lead">Events, workshops, and club activities are collected here as a visual portfolio with images, dates, titles, and short descriptions.</p>
        </section>

        <section class="section event-section">
            <h2>Upcoming Events and Workshops</h2>
            <div class="event-grid">
                <?php if (empty($upcomingEvents)): ?>
                    <?php public_empty('Stay tuned for upcoming HACK KUET events, workshops, and hands-on learning sessions. New announcements will appear here as soon as they are scheduled.'); ?>
                <?php endif; ?>
                <?php foreach ($upcomingEvents as $event): ?>
                    <article class="event-card">
                        <?php if (!empty($event['cover_image'])): ?>
                            <img class="event-thumb" src="<?= public_h($event['cover_image']) ?>" alt="<?= public_h($event['title']) ?>">
                        <?php endif; ?>
                        <div class="event-card-body">
                            <div class="event-card-topline">
                                <span class="event-label"><?= public_h(portfolio_label($event)) ?></span>
                                <span class="event-date"><?= public_h(portfolio_date($event['event_date'])) ?></span>
                            </div>
                            <h3><?= public_h($event['title']) ?></h3>
                            <p><?= public_h($event['description']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section event-section">
            <h2>Club Activities</h2>
            <div class="event-grid activity-grid">
                <?php foreach ($activities as $activity): ?>
                    <article class="event-card activity-card">
                        <?php if (!empty($activity['cover_image'])): ?>
                            <img class="event-thumb" src="<?= public_h($activity['cover_image']) ?>" alt="<?= public_h($activity['title']) ?>">
                        <?php endif; ?>
                        <div class="event-card-body">
                            <span class="event-date"><?= public_h(portfolio_date($activity['activity_date'])) ?></span>
                            <h3><?= public_h($activity['title']) ?></h3>
                            <div class="activity-card-content">
                                <p><?= public_h($activity['description']) ?></p>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section event-section">
            <h2>Previous Events and Workshops</h2>
            <div class="event-grid previous-events-grid">
                <?php foreach ($previousEvents as $event): ?>
                    <article class="event-card">
                        <?php if (!empty($event['cover_image'])): ?>
                            <img class="event-thumb" src="<?= public_h($event['cover_image']) ?>" alt="<?= public_h($event['title']) ?>">
                        <?php endif; ?>
                        <div class="event-card-body">
                            <div class="event-card-topline">
                                <span class="event-label"><?= public_h(portfolio_label($event)) ?></span>
                                <span class="event-date"><?= public_h(portfolio_date($event['event_date'])) ?></span>
                            </div>
                            <h3><?= public_h($event['title']) ?></h3>
                            <p><?= public_h($event['description']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="hero portfolio-section" id="projects">
            <p class="eyebrow">Project Bay</p>
            <h1>Recent hardware systems built inside HACK.</h1>
            <p class="lead">Projects are shown as portfolio entries with their image, summary, team members, departments, mentors, and tools.</p>
        </section>

        <section class="section">
            <div class="grid three">
                <?php if (empty($projects)): ?>
                    <?php public_empty('Projects added in the admin dashboard will appear here automatically.'); ?>
                <?php endif; ?>
                <?php foreach ($projects as $project): ?>
                    <article class="card project-card">
                        <?php if (!empty($project['cover_image'])): ?>
                            <img class="project-thumb" src="<?= public_h($project['cover_image']) ?>" alt="<?= public_h($project['title']) ?> thumbnail" onerror="this.remove()">
                        <?php endif; ?>
                        <span class="meta"><?= public_h(ucfirst(str_replace('_', ' ', $project['project_status']))) ?></span>
                        <h3><?= public_h($project['title']) ?></h3>
                        <p><?= public_h($project['description']) ?></p>
                        <div class="project-card-meta">
                            <p><strong>Team:</strong> <?= public_h($project['team_members'] ?: 'Team information will be added soon.') ?></p>
                            <p><strong>Mentors:</strong> <?= public_h($project['mentors'] ?? 'Mentor information will be added soon.') ?></p>
                        </div>
                        <?php $chips = public_chips($project['technologies'] ?? ''); ?>
                        <?php if ($chips): ?>
                            <div class="chips">
                                <?php foreach (array_slice($chips, 0, 3) as $chip): ?>
                                    <span class="chip"><?= public_h($chip) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="hero portfolio-section" id="blogs">
            <p class="eyebrow">Club Notes</p>
            <h1>Build logs, reflections, and lessons from the workshop floor.</h1>
            <p class="lead">Explore practical notes from our projects, workshops, and debugging sessions. These posts collect the small lessons, careful observations, and build decisions that help future teams learn faster.</p>
        </section>

        <section class="section">
            <div class="blog-list">
                <?php if (empty($blogs)): ?>
                    <?php public_empty('Published blog posts from the admin dashboard will appear here.'); ?>
                <?php endif; ?>
                <?php foreach ($blogs as $blog): ?>
                    <?php
                        $author = trim((string) ($blog['author_name'] ?? ''));
                        $department = trim((string) ($blog['author_department'] ?? ''));
                        if ($department !== '' && stripos($author, $department) === false) {
                            $author .= ', ' . $department;
                        }
                        $contentPreview = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($blog['content'] ?? ''))));
                    ?>
                    <article class="card blog-card">
                        <div class="blog-card-media">
                            <?php if (!empty($blog['cover_image'])): ?>
                                <img class="blog-thumb" src="<?= public_h($blog['cover_image']) ?>" alt="<?= public_h($blog['title']) ?> thumbnail" onerror="this.remove()">
                            <?php endif; ?>
                            <h3><?= public_h($blog['title']) ?></h3>
                            <p class="blog-author">By <?= public_h($author) ?></p>
                        </div>
                        <div class="blog-card-main">
                            <p><?= public_h($contentPreview) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section" id="submit-blog">
            <article class="card blog-submit-card">
                <span class="meta">Write For HACK KUET</span>
                <h2>Submit a blog for review</h2>
                <p>Share a build log, debugging story, project lesson, or practical guide. Your submission will go to the admin dashboard as pending, and it will appear publicly after approval.</p>
                <?php if ($submissionStatus === 'success'): ?>
                    <p class="form-status success">Thanks, your blog was submitted for admin review.</p>
                <?php elseif ($submissionStatus === 'error'): ?>
                    <p class="form-status error">We could not save your blog. Please check all required fields and try again.</p>
                <?php endif; ?>
                <form class="form-wrap blog-submit-form" action="blog-submit.php" method="post" enctype="multipart/form-data">
                    <div class="grid two">
                        <div class="field">
                            <label for="blog-name">Name</label>
                            <input id="blog-name" name="name" type="text" placeholder="Your full name" required>
                        </div>
                        <div class="field">
                            <label for="blog-university">University</label>
                            <input id="blog-university" name="university" type="text" placeholder="Your university" required>
                        </div>
                    </div>
                    <div class="grid two">
                        <div class="field">
                            <label for="blog-department">Department Name</label>
                            <input id="blog-department" name="department" type="text" placeholder="CSE, EEE, ME..." required>
                        </div>
                        <div class="field">
                            <label for="blog-tags">Tags</label>
                            <input id="blog-tags" name="tags" type="text" placeholder="PCB, Firmware, Robotics" required>
                        </div>
                    </div>
                    <div class="field">
                        <label for="blog-title">Title of the Blog</label>
                        <input id="blog-title" name="title" type="text" placeholder="Write a clear title" required>
                    </div>
                    <div class="field">
                        <label for="blog-content">Content of the Blog</label>
                        <textarea id="blog-content" name="content" rows="12" placeholder="Write your blog here..." required></textarea>
                    </div>
                    <div class="field">
                        <label for="blog-image-1">Blog Image</label>
                        <input id="blog-image-1" name="image_1" type="file" accept="image/*" required>
                    </div>
                    <button class="btn" type="submit">Submit Blog</button>
                </form>
            </article>
        </section>

        <section class="hero portfolio-section" id="contact">
            <p class="eyebrow">Get In Touch</p>
            <h1>Bring your idea. We will help you build it.</h1>
            <p class="lead">Reach out for membership, mentorship, collaboration, or workshop invitations.</p>
        </section>

        <section class="section contact-shell">
            <article class="card contact-panel contact-info-panel">
                <div class="contact-panel-head">
                    <h2>Get In Touch With Us Now!</h2>
                </div>
                <div class="contact-actions" aria-label="Contact links">
                    <?php foreach ($contactIcons as $key => [$heading, $label, $image, $href]): ?>
                        <?php if ($key !== 'address' && ($contacts[$key]['value'] ?? '') === '') continue; ?>
                        <div class="contact-action">
                            <a class="contact-icon-link" href="<?= public_h($href) ?>"<?= str_starts_with($href, 'mailto:') ? '' : ' target="_blank" rel="noopener noreferrer"' ?> aria-label="<?= public_h($label) ?>">
                                <img src="<?= public_h($image) ?>" alt="<?= public_h($label) ?>">
                            </a>
                            <h3><?= public_h($heading) ?></h3>
                            <?php if ($key === 'email'): ?>
                                <p class="contact-action-detail"><?= public_h($email) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </article>

            <article class="card contact-panel contact-form-panel">
                <div class="contact-panel-head">
                    <h2>Contact Us</h2>
                </div>
                <div class="contact-form-inner">
                    <?php if ($messageStatus === 'success'): ?>
                        <p class="form-status success">Thanks, your message was sent successfully.</p>
                    <?php elseif ($messageStatus === 'error'): ?>
                        <p class="form-status error">We could not save your message. Please try again.</p>
                    <?php endif; ?>
                    <form class="form-wrap" action="contact-submit.php" method="post">
                        <div class="field">
                            <label for="name">Full Name</label>
                            <input id="name" name="name" type="text" placeholder="Your name" required>
                        </div>
                        <div class="field">
                            <label for="email">Email Address</label>
                            <input id="email" name="email" type="email" placeholder="you@example.com" required>
                        </div>
                        <div class="field">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" placeholder="Tell us what you want to build..."></textarea>
                        </div>
                        <button class="btn" type="submit">Submit</button>
                    </form>
                </div>
            </article>
        </section>
    </main>

    <dialog class="membership-dialog" id="membership-dialog" aria-labelledby="membership-title">
        <div class="membership-panel">
            <div class="membership-panel-head">
                <div>
                    <p class="meta">Membership</p>
                    <h2 id="membership-title">Join HACK KUET</h2>
                </div>
                <button class="dialog-close" id="membership-close" type="button" aria-label="Close membership form">&times;</button>
            </div>
            <p class="membership-copy">Share your details and interests, and we will use the PHP backend to record your membership request.</p>
            <form class="form-wrap membership-form" id="membership-form" action="membership.php" method="post">
                <div class="field">
                    <label for="member-name">Full Name</label>
                    <input id="member-name" name="name" type="text" placeholder="Your name" required>
                </div>
                <div class="field">
                    <label for="member-email">Email Address</label>
                    <input id="member-email" name="email" type="email" placeholder="you@example.com" required>
                </div>
                <div class="field">
                    <label for="member-department">Department / Year</label>
                    <input id="member-department" name="department" type="text" placeholder="CSE, EEE, ME - 2nd Year" required>
                </div>
                <div class="field">
                    <label for="member-interest">Area of Interest</label>
                    <input id="member-interest" name="interest" type="text" placeholder="Robotics, PCB design, firmware..." required>
                </div>
                <div class="field">
                    <label for="member-message">Why do you want to join?</label>
                    <textarea id="member-message" name="message" placeholder="Tell us what you want to learn or build." required></textarea>
                </div>
                <button class="btn" type="submit">Submit Membership Request</button>
                <p class="form-status" id="membership-form-status" aria-live="polite"></p>
            </form>
        </div>
    </dialog>

    <div id="adminFloatBtn" style="position:fixed; bottom:28px; right:28px; z-index:900; display:flex; flex-direction:column; align-items:flex-end; gap:10px;">
        <div id="adminPopup" style="display:none; background:var(--panel,#fff); border:1px solid var(--panel-border,rgba(52,140,89,0.22)); border-radius:14px; padding:18px 20px; box-shadow:var(--shadow,0 18px 42px rgba(26,74,42,0.14)); min-width:190px; text-align:center;">
            <p style="font-size:0.82rem;color:var(--muted);margin:0 0 12px;font-weight:500;">Are you an administrator?</p>
            <a href="/Hack-Club/admin/login.php" style="display:inline-block;width:100%;padding:9px 0; background:var(--accent,#23944f);color:#fff; border-radius:8px;font-size:0.875rem;font-weight:700; text-decoration:none;margin-bottom:8px;">Admin Login</a>
            <button onclick="document.getElementById('adminPopup').style.display='none'" style="display:inline-block;width:100%;padding:7px 0; background:none;border:1px solid var(--panel-border); border-radius:8px;font-size:0.82rem;color:var(--muted);cursor:pointer;font-family:inherit;">Cancel</button>
        </div>
        <button onclick="(function(p){p.style.display=p.style.display==='none'?'block':'none'})(document.getElementById('adminPopup'))" aria-label="Admin Access" title="Admin Panel" style="width:48px;height:48px;border-radius:50%; background:var(--accent,#23944f);color:#fff; border:none;cursor:pointer; display:flex;align-items:center;justify-content:center; box-shadow:0 4px 18px rgba(35,148,79,0.35); transition:transform 160ms,box-shadow 160ms; font-size:1.2rem;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </button>
    </div>

<?php public_footer('HACK KUET | From concept to circuit.'); ?>
