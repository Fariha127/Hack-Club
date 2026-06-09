<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        try { if (localStorage.getItem('hack-theme') === 'dark') document.documentElement.classList.add('dark'); } catch(e) {}
    </script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/css/admin.css?v=blog-preview-3">
    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <title><?= h($pageTitle ?? 'Dashboard') ?> | HACK Admin</title>
</head>
<body>
