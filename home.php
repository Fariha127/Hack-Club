<?php
require_once __DIR__ . '/public-data.php';

$contacts = public_contact_info();
$email = $contacts['email']['value'] ?? 'hack.kuet.club@gmail.com';
$facebookPage = $contacts['facebook_page']['value'] ?? '';
$facebookGroup = $contacts['facebook_group']['value'] ?? '';
public_header('Home', 'home');
?>

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

    <main class="container">
        <section class="hero">
            <p class="eyebrow">Hardware Acceleration Club</p>
            <h1>Designing, building, and accelerating hardware innovation at KUET.</h1>
            <p class="lead">HACK is a collaborative engineering community where students turn ambitious ideas into tested systems through hands-on learning, project sprints, and team mentorship.</p>
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
                    <p>Join multidisciplinary teams for robotics and IoT competitions across campus and beyond.</p>
                </article>
                <article class="card">
                    <h3>Grow with Mentorship</h3>
                    <p>Senior members provide guidance on architecture, debugging strategy, and project planning.</p>
                </article>
            </div>
        </section>

        <section class="section">
            <div class="grid two">
                <article class="card">
                    <span class="meta">What We Run</span>
                    <h3>Weekly Workshop Tracks</h3>
                    <p>Structured sessions covering embedded C, sensor interfacing, actuator control, and prototype validation.</p>
                    <div class="chips">
                        <span class="chip">Embedded Basics</span>
                        <span class="chip">Rapid Prototyping</span>
                        <span class="chip">System Testing</span>
                    </div>
                </article>
                <article class="card">
                    <span class="meta">Get Started</span>
                    <h3>Join the Next Build Cycle</h3>
                    <p>Explore current projects, attend an open session, and become part of a team building real hardware solutions.</p>
                    <div class="chips">
                        <span class="chip">Open Session</span>
                        <span class="chip">Team Matching</span>
                        <span class="chip">Demo Day</span>
                    </div>
                </article>
            </div>
        </section>

        <section class="section contact-section" id="contact">
            <div class="contact-panel">
                <p class="eyebrow contact-eyebrow">Contact and Community</p>
                <h2>Stay connected with HACK KUET</h2>
                <p class="contact-copy">For membership, mentorship, collaboration, or workshop enquiries, reach out to us at <?= public_h($email) ?>. You can also follow our Facebook page for updates and join the group for announcements, discussions, and community posts.</p>
                <div class="contact-grid">
                    <div class="contact-item">
                        <span class="contact-label">Email</span>
                        <a href="mailto:<?= public_h($email) ?>"><?= public_h($email) ?></a>
                    </div>
                    <?php if ($facebookPage): ?>
                    <div class="contact-item">
                        <span class="contact-label">Facebook Page</span>
                        <a href="<?= public_h($facebookPage) ?>" target="_blank" rel="noopener noreferrer">Follow HACK KUET on Facebook</a>
                    </div>
                    <?php endif; ?>
                    <?php if ($facebookGroup): ?>
                    <div class="contact-item">
                        <span class="contact-label">Facebook Group</span>
                        <a href="<?= public_h($facebookGroup) ?>" target="_blank" rel="noopener noreferrer">Join the HACK KUET group</a>
                    </div>
                    <?php endif; ?>
                </div>
                <p class="contact-note">Please follow the page and kindly join the group so you do not miss event notices, project updates, and workshop schedules.</p>
            </div>
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
