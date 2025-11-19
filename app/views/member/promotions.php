<?php ob_start(); ?>
<h1 class="mb-6 text-2xl font-semibold text-slate-900">Member Promotions & Rewards</h1>
<div class="overflow-hidden rounded-lg border border-slate-200">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
            <tr>
                <th class="px-4 py-3">Promotion</th>
                <th class="px-4 py-3">Points</th>
                <th class="px-4 py-3">Expires</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 bg-white">
            <?php foreach ($promotions as $promotion): ?>
                <tr>
                    <td class="px-4 py-3 font-medium text-slate-900"><?= htmlspecialchars($promotion['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="px-4 py-3 text-slate-700"><?= htmlspecialchars($promotion['points'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="px-4 py-3 text-slate-700"><?= htmlspecialchars($promotion['expires_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<p class="mt-4 text-sm text-slate-600">Access available to members; redeem points from your portal.</p>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
