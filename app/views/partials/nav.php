<?php use App\Services\Session; ?>
<nav class="border-b border-slate-200 bg-white/70 backdrop-blur supports-[backdrop-filter]:bg-white/50">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
        <a href="/" class="text-lg font-semibold text-slate-800">Community Hub</a>
        <div class="flex items-center gap-4 text-sm text-slate-700">
            <a class="hover:text-blue-600" href="/news">News</a>
            <a class="hover:text-blue-600" href="/member/promotions">Member Portal</a>
            <a class="hover:text-blue-600" href="/admin">Admin</a>
        </div>
        <div class="flex items-center gap-3 text-sm text-slate-700">
            <?php if (Session::get('user')): ?>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-800">Hi, <?= htmlspecialchars(Session::get('user')['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                <form method="POST" action="/logout" class="m-0">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <button class="rounded-md bg-red-500 px-3 py-1 text-white shadow hover:bg-red-600" type="submit">Logout</button>
                </form>
            <?php else: ?>
                <a class="rounded-md border border-slate-200 px-3 py-1 hover:border-blue-500 hover:text-blue-600" href="/login">Login</a>
                <a class="rounded-md bg-blue-600 px-3 py-1 text-white shadow hover:bg-blue-700" href="/register">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
