<?php ob_start(); ?>
<h1 class="mb-6 text-2xl font-semibold text-slate-900">Sign In</h1>
<form method="POST" action="/login" class="space-y-4">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
    <div>
        <label class="block text-sm font-medium text-slate-700" for="email">Email</label>
        <input class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" type="email" id="email" name="email" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700" for="password">Password</label>
        <input class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" type="password" id="password" name="password" required>
    </div>
    <div class="flex items-center justify-between text-sm">
        <label class="flex items-center gap-2 text-slate-700"><input type="checkbox" name="remember" class="rounded border-slate-300"> Remember me</label>
        <a href="/password/reset" class="text-blue-600 hover:underline">Forgot password?</a>
    </div>
    <button class="w-full rounded-md bg-blue-600 px-4 py-2 font-semibold text-white shadow hover:bg-blue-700" type="submit">Login</button>
</form>
<p class="mt-4 text-sm text-slate-600">New here? <a class="text-blue-600 hover:underline" href="/register">Create an account</a>.</p>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
