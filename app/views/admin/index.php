<?php ob_start(); ?>
<h1 class="mb-6 text-2xl font-semibold text-slate-900">Admin Panel</h1>
<div class="grid gap-6 md:grid-cols-2">
    <section class="rounded-lg border border-slate-200 bg-slate-50/60 p-4">
        <header class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">News</h2>
            <button class="rounded-md bg-blue-600 px-3 py-1 text-xs font-semibold text-white shadow">Create</button>
        </header>
        <ul class="space-y-2 text-sm text-slate-700">
            <?php foreach ($news as $item): ?>
                <li class="flex justify-between rounded border border-slate-200 bg-white px-3 py-2"><span><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></span><span class="text-xs text-slate-500"><?= htmlspecialchars($item['published_at'], ENT_QUOTES, 'UTF-8'); ?></span></li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="rounded-lg border border-slate-200 bg-slate-50/60 p-4">
        <header class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Promotions</h2>
            <button class="rounded-md bg-blue-600 px-3 py-1 text-xs font-semibold text-white shadow">Add</button>
        </header>
        <ul class="space-y-2 text-sm text-slate-700">
            <?php foreach ($promotions as $promotion): ?>
                <li class="flex justify-between rounded border border-slate-200 bg-white px-3 py-2"><span><?= htmlspecialchars($promotion['name'], ENT_QUOTES, 'UTF-8'); ?></span><span class="text-xs text-slate-500">Points: <?= htmlspecialchars($promotion['points'], ENT_QUOTES, 'UTF-8'); ?></span></li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="rounded-lg border border-slate-200 bg-slate-50/60 p-4">
        <header class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Users</h2>
            <button class="rounded-md bg-blue-600 px-3 py-1 text-xs font-semibold text-white shadow">Invite</button>
        </header>
        <div class="overflow-hidden rounded border border-slate-200 bg-white">
            <table class="min-w-full divide-y divide-slate-200 text-xs">
                <thead class="bg-slate-50 text-left font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-3 py-2">Name</th>
                        <th class="px-3 py-2">Email</th>
                        <th class="px-3 py-2">Role</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="px-3 py-2 text-slate-900"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-3 py-2 text-slate-700"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-3 py-2"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700"><?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <section class="rounded-lg border border-slate-200 bg-slate-50/60 p-4">
        <header class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Newsletters</h2>
            <button class="rounded-md bg-blue-600 px-3 py-1 text-xs font-semibold text-white shadow">Send</button>
        </header>
        <ul class="space-y-2 text-sm text-slate-700">
            <?php foreach ($newsletters as $newsletter): ?>
                <li class="flex justify-between rounded border border-slate-200 bg-white px-3 py-2"><span><?= htmlspecialchars($newsletter['subject'], ENT_QUOTES, 'UTF-8'); ?></span><span class="text-xs text-slate-500">Sent <?= htmlspecialchars($newsletter['sent_at'], ENT_QUOTES, 'UTF-8'); ?></span></li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
