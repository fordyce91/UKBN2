<?php use App\Services\Session; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Community Hub', ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="/assets/app.css">
</head>
<body class="bg-slate-50 text-slate-900">
    <?php include __DIR__ . '/../partials/nav.php'; ?>
    <main class="mx-auto mt-8 max-w-6xl px-4">
        <?php include __DIR__ . '/../partials/flash.php'; ?>
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
            <?= $content ?? '' ?>
        </div>
    </main>
</body>
</html>
