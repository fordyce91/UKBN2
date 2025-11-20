<?php ob_start(); ?>
<div class="text-center">
    <h1 class="mb-4 text-3xl font-bold text-red-700">Server Error</h1>
    <p class="text-slate-700"><?= e($message ?? 'Something went wrong. Please try again later.'); ?></p>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
