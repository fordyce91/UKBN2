<?php ob_start(); ?>
<div class="text-center">
    <h1 class="mb-4 text-3xl font-bold text-slate-900">Page Not Found</h1>
    <p class="text-slate-700">The path "<?= e($path ?? ''); ?>" could not be located.</p>
    <a class="mt-4 inline-block text-blue-600 hover:underline" href="/">Return home</a>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
