<?php ob_start(); ?>
<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <div>
        <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">Community Hub</p>
        <h1 class="mt-2 text-3xl font-bold text-slate-900">Stay informed, earn rewards, and manage your members.</h1>
        <p class="mt-3 text-slate-700">Public news, a member promotions portal, and an admin console ship with CSRF protection, validation, and session-aware navigation.</p>
        <div class="mt-4 flex gap-3">
            <a class="rounded-md bg-blue-600 px-4 py-2 font-semibold text-white shadow hover:bg-blue-700" href="/news">View News</a>
            <a class="rounded-md border border-slate-200 px-4 py-2 font-semibold text-slate-800 hover:border-blue-500 hover:text-blue-600" href="/member/promotions">Member Portal</a>
        </div>
    </div>
    <div class="rounded-xl border border-blue-100 bg-blue-50/50 p-6 shadow-inner">
        <h2 class="text-lg font-semibold text-blue-900">Access Control</h2>
        <ul class="mt-3 space-y-2 text-sm text-blue-900">
            <li>• Public news is available to everyone.</li>
            <li>• Members can view promotions and rewards.</li>
            <li>• Admins manage news, promotions, users, and newsletters.</li>
        </ul>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/layouts/main.php'; ?>
