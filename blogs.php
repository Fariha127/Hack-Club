<?php
require_once __DIR__ . '/public-data.php';

$blogs = public_rows("SELECT * FROM blogs WHERE status = 'published' ORDER BY COALESCE(published_at, submitted_at) DESC");
$submissionStatus = $_GET['submission'] ?? '';
public_header('Blogs', 'blogs');
?>

    <main class="container">
        <section class="hero">
            <p class="eyebrow">Club Notes</p>
            <h1>Build logs, reflections, and lessons from the workshop floor.</h1>
            <p class="lead">Every project leaves behind practical insights. Published admin blog posts appear here automatically.</p>
        </section>

        <section class="section">
            <article class="card blog-intro">
                <span class="meta">For Readers</span>
                <p>Welcome to the HACK KUET blog, a space for the small lessons, careful observations, and practical stories that come out of our hardware work. Projects often look clean when they are shown at the end: the robot follows the line, the PCB powers on, the sensor graph becomes stable, or the demo finally runs in front of an audience. But the most useful learning usually happens before that polished moment. It happens when a board refuses to upload code, when a motor driver overheats, when a sensor behaves differently under classroom lights, or when a team realizes that the first design was too complicated to finish on time.</p>
                <p>These posts are written for students who are building, debugging, reviewing, testing, and presenting real systems. Some articles focus on electronics, such as PCB review, power paths, connector choices, and common microcontroller mistakes. Others look at firmware structure, sensor noise, control tuning, and the habits that make embedded projects easier to maintain as a team. We also share process notes from demo preparation, because a working prototype is only part of the challenge. A team also has to explain what it built, make the setup repeatable, and prepare for the unexpected things that happen when hardware leaves the lab bench.</p>
                <p>If you are new to hardware, do not worry if every term is not familiar yet. Read these posts as field notes from people who are learning in public. Notice the order of checks, the questions we ask, and the way problems are narrowed down. Good hardware work is rarely about guessing the perfect answer immediately. It is about building a steady process: measure before assuming, test one change at a time, write down what changed, and keep the system simple enough that teammates can understand it.</p>
                <p>If you already have experience, we hope these blogs still give you something useful. You may find a checklist to reuse before fabrication, a reminder to keep firmware layers separate, or a tuning habit that saves time before a competition run. You may also disagree with some choices, and that is welcome. Engineering grows through review, comparison, and discussion. The purpose of this page is not to present final authority; it is to collect practical knowledge that helps the next team start with fewer avoidable mistakes.</p>
                <p>As you read, think about your own projects. Which problems keep repeating? Which checks would have saved time last semester? Which idea could become a workshop, a build session, or a better team standard? The blog is also an invitation. If you have built something, failed at something, fixed something, or learned a lesson that another student could use, your experience belongs here too. HACK KUET is strongest when members share not only results, but the thinking, testing, and persistence behind them.</p>
                <p>We hope this page becomes a living archive for our club. Come back when you are stuck, when you are planning a new build, or when you simply want to see how other students approach real engineering problems. Each post is one small snapshot, but together they show a culture of curiosity, patience, teamwork, and hands-on learning.</p>
            </article>
        </section>

        <section class="section">
            <div class="grid three">
                <?php if (empty($blogs)): ?>
                    <?php public_empty('Published blog posts from the admin dashboard will appear here.'); ?>
                <?php endif; ?>

                <?php foreach ($blogs as $blog): ?>
                    <?php $blogHref = is_file(__DIR__ . '/' . $blog['slug'] . '.html') ? $blog['slug'] . '.html' : 'blog-detail.php?slug=' . urlencode($blog['slug']); ?>
                    <a class="card-link blog-card-link" href="<?= public_h($blogHref) ?>">
                        <article class="card blog-card">
                            <?php if (!empty($blog['cover_image'])): ?>
                                <img class="blog-thumb" src="<?= public_h($blog['cover_image']) ?>" alt="<?= public_h($blog['title']) ?> thumbnail" onerror="this.remove()">
                            <?php endif; ?>
                            <span class="meta"><?= public_h(public_date($blog['published_at'] ?: $blog['submitted_at'])) ?></span>
                            <h3><?= public_h($blog['title']) ?></h3>
                            <p class="blog-author">By <?= public_h($blog['author_name']) ?></p>
                            <?php $chips = public_chips($blog['tags'] ?? ''); ?>
                            <?php if (!empty($chips)): ?>
                                <div class="chips">
                                    <?php foreach ($chips as $chip): ?>
                                        <span class="chip"><?= public_h($chip) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </article>
                    </a>
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
                    <div class="grid two">
                        <div class="field">
                            <label for="blog-image-1">First Image</label>
                            <input id="blog-image-1" name="image_1" type="file" accept="image/*" required>
                        </div>
                        <div class="field">
                            <label for="blog-image-2">Second Image</label>
                            <input id="blog-image-2" name="image_2" type="file" accept="image/*" required>
                        </div>
                    </div>
                    <button class="btn" type="submit">Submit Blog</button>
                </form>
            </article>
        </section>
    </main>

<?php public_footer('HACK KUET | Sharing process, not just results.'); ?>
