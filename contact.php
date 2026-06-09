<?php
require_once __DIR__ . '/public-data.php';

$contacts = public_contact_info();
$email = $contacts['email']['value'] ?? 'hack.kuet.club@gmail.com';
$messageStatus = $_GET['contact'] ?? '';
$contactIcons = [
    'email' => ['Mail us!', 'Email icon', 'email.jpg', 'mailto:' . ($contacts['email']['value'] ?? '')],
    'facebook_page' => ['Follow our facebook page!', 'Facebook page image', 'facebook-page.jpg', $contacts['facebook_page']['value'] ?? ''],
    'facebook_group' => ['Join on our facebook group!', 'Facebook group image', 'facebook-group.jpg', $contacts['facebook_group']['value'] ?? ''],
    'address' => ['Visit our club!', 'Address image', 'address.jpg', 'https://maps.app.goo.gl/aqtLwX2DVue4XFGp6'],
];
public_header('Contact', 'contact');
?>

    <main class="container">
        <section class="hero">
            <p class="eyebrow">Get In Touch</p>
            <h1>Bring your idea. We will help you build it.</h1>
            <p class="lead">Reach out for membership, mentorship, collaboration, or workshop invitations. The contact details below are maintained from the admin dashboard.</p>
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

<?php public_footer('HACK KUET | Collaboration starts with a message.'); ?>
