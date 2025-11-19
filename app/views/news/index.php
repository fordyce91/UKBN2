<?php ob_start(); ?>
<h1 class="mb-6 text-2xl font-semibold text-slate-900">Latest News</h1>
<div class="grid gap-4 md:grid-cols-2">
    <?php foreach ($news as $item): ?>
        <article class="rounded-lg border border-slate-200 bg-slate-50/60 p-4">
            <div class="flex items-center justify-between text-xs text-slate-500">
                <span><?= htmlspecialchars($item['published_at'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="rounded-full bg-blue-50 px-2 py-1 text-blue-700">Announcement</span>
            </div>
            <h2 class="mt-2 text-lg font-bold text-slate-900"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <p class="mt-2 text-sm text-slate-700"><?= htmlspecialchars($item['body'], ENT_QUOTES, 'UTF-8'); ?></p>
        </article>
    <?php endforeach; ?>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
