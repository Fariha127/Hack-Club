<?php
require_once __DIR__ . '/admin/includes/db.php';

function public_h(?string $value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function public_db(): ?PDO {
    try {
        return get_db();
    } catch (Throwable $e) {
        return null;
    }
}

function public_rows(string $sql, array $params = []): array {
    $db = public_db();
    if (!$db) {
        return [];
    }
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

function public_contact_info(): array {
    $defaults = [
        'email' => ['label' => 'Email', 'value' => 'hack.kuet.club@gmail.com'],
        'facebook_page' => ['label' => 'Facebook Page', 'value' => 'https://www.facebook.com/people/HACK-Hardware-Acceleration-Club-of-KUET/100088262984928/'],
        'facebook_group' => ['label' => 'Facebook Group', 'value' => 'https://www.facebook.com/groups/hack.kuet/'],
        'address' => ['label' => 'Office Address', 'value' => 'KUET Campus, Khulna-9203, Bangladesh'],
    ];

    $rows = public_rows("SELECT key_name, label, value FROM contact_info ORDER BY id ASC");
    foreach ($rows as $row) {
        if (($row['value'] ?? '') !== '') {
            $defaults[$row['key_name']] = [
                'label' => $row['label'],
                'value' => $row['value'],
            ];
        }
    }
    return $defaults;
}

function public_chips(?string $value): array {
    if (!$value) {
        return [];
    }
    return array_values(array_filter(array_map('trim', preg_split('/[,|]/', $value))));
}

function public_date(?string $date): string {
    if (!$date) {
        return '';
    }
    $time = strtotime($date);
    return $time ? date('F Y', $time) : $date;
}

function public_header(string $title, string $active): void {
    $links = [
        'home' => ['Home', 'home.php'],
        'about' => ['About Us', 'about.php'],
        'events' => ['Events', 'event.php'],
        'projects' => ['Projects', 'project.php'],
        'blogs' => ['Blogs', 'blogs.php'],
        'contact' => ['Contact', 'contact.php'],
    ];
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        try {
            if (localStorage.getItem("hack-theme") === "dark") {
                document.documentElement.classList.add("dark");
            }
        } catch (error) {
        }
    </script>
    <link rel="stylesheet" href="style.css?v=contact-icons-4">
    <script src="theme-toggle.js" defer></script>
    <script src="main.js" defer></script>
    <title><?= public_h($title) ?> | HACK KUET</title>
</head>
<body>
    <header class="site-header">
        <div class="container site-header-inner">
            <a class="brand" href="home.php"><span></span>HACK - Hardware Acceleration Club of KUET</a>
            <button class="nav-toggle" type="button" aria-label="Toggle menu" aria-expanded="false">Menu</button>
            <nav>
                <?php foreach ($links as $key => [$label, $href]): ?>
                    <a<?= $key === $active ? ' class="active"' : '' ?> href="<?= public_h($href) ?>"><?= public_h($label) ?></a>
                <?php endforeach; ?>
            </nav>
            <button class="theme-toggle" type="button" aria-label="Switch to dark mode" aria-pressed="false"></button>
        </div>
    </header>
    <?php
}

function public_footer(string $text): void {
    ?>
    <footer class="site-footer">
        <div class="container"><?= public_h($text) ?></div>
    </footer>
</body>
</html>
    <?php
}

function public_empty(string $message): void {
    ?>
    <article class="card">
        <h3>No published items yet</h3>
        <p><?= public_h($message) ?></p>
    </article>
    <?php
}
