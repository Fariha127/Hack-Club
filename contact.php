<?php
require_once __DIR__ . '/public-data.php';

$contacts = public_contact_info();
$email = $contacts['email']['value'] ?? 'hack.kuet.club@gmail.com';
public_header('Contact', 'contact');
?>

    <main class="container">
        <section class="hero">
            <p class="eyebrow">Get In Touch</p>
            <h1>Bring your idea. We will help you build it.</h1>
            <p class="lead">Reach out for membership, mentorship, collaboration, or workshop invitations. The contact details below are maintained from the admin dashboard.</p>
        </section>

        <section class="section">
            <div class="grid two">
                <article class="card">
                    <h2>Send a Message</h2>
                    <form class="form-wrap" action="https://formsubmit.co/<?= public_h($email) ?>" method="post">
                        <input type="hidden" name="_subject" value="HACK KUET Website Contact Request">
                        <input type="hidden" name="_captcha" value="false">
                        <input id="next-url" type="hidden" name="_next" value="">
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
                </article>

                <article class="card">
                    <h2>Club Contact Desk</h2>
                    <?php foreach ($contacts as $contact): ?>
                        <?php if (($contact['value'] ?? '') === '') continue; ?>
                        <p><strong><?= public_h($contact['label']) ?>:</strong>
                            <?php if (filter_var($contact['value'], FILTER_VALIDATE_URL)): ?>
                                <a href="<?= public_h($contact['value']) ?>" target="_blank" rel="noopener noreferrer"><?= public_h($contact['value']) ?></a>
                            <?php elseif (filter_var($contact['value'], FILTER_VALIDATE_EMAIL)): ?>
                                <a href="mailto:<?= public_h($contact['value']) ?>"><?= public_h($contact['value']) ?></a>
                            <?php else: ?>
                                <?= public_h($contact['value']) ?>
                            <?php endif; ?>
                        </p>
                    <?php endforeach; ?>
                    <div class="chips">
                        <span class="chip">Membership Help</span>
                        <span class="chip">Project Mentorship</span>
                        <span class="chip">Workshop Requests</span>
                    </div>
                </article>
            </div>
        </section>
    </main>

<?php public_footer('HACK KUET | Collaboration starts with a message.'); ?>
