<?php ob_start(); ?>
<div class="text-center">
    <h1 class="mb-4 text-3xl font-bold text-red-700">Server Error</h1>
    <p class="text-slate-700"><?= e($message ?? 'Something went wrong. Please try again later.'); ?></p>
    <?php if (!empty($requestId)): ?>
        <p class="mt-2 text-sm text-slate-600">Reference ID: <?= e($requestId); ?></p>
    <?php endif; ?>
    <?php if (!empty($details)): ?>
        <div class="mx-auto mt-6 max-w-2xl rounded border border-slate-200 bg-slate-50 p-4 text-left text-sm text-slate-800">
            <p class="font-semibold text-slate-900">Debug details</p>
            <p class="mt-2"><span class="font-semibold">Type:</span> <?= e($details['type'] ?? 'Unknown'); ?></p>
            <p class="mt-1"><span class="font-semibold">Location:</span> <?= e(($details['file'] ?? 'unknown') . ':' . ($details['line'] ?? '?')); ?></p>
            <?php if (!empty($details['trace'])): ?>
                <pre class="mt-3 overflow-auto rounded bg-white p-3 text-xs text-slate-700"><?= e($details['trace']); ?></pre>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
