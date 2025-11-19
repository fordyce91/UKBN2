<?php ob_start(); ?>
<h1 class="mb-6 text-2xl font-semibold text-slate-900">Password Reset</h1>
<form method="POST" action="/password/reset" class="space-y-4">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
    <div>
        <label class="block text-sm font-medium text-slate-700" for="email">Email</label>
        <input class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" type="email" id="email" name="email" required>
    </div>
    <button class="w-full rounded-md bg-blue-600 px-4 py-2 font-semibold text-white shadow hover:bg-blue-700" type="submit">Send reset link</button>
</form>
<p class="mt-4 text-sm text-slate-600">We will email you a secure link to reset your password.</p>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
