<?php ob_start(); ?>
<h1 class="mb-6 text-2xl font-semibold text-slate-900">Password Reset</h1>

<?php if (!empty($resetToken)): ?>
    <form method="POST" action="/password/reset" class="space-y-4" novalidate>
        <input type="hidden" name="_token" value="<?= e($csrfToken); ?>">
        <input type="hidden" name="token" value="<?= e($resetToken); ?>">
        <div>
            <label class="block text-sm font-medium text-slate-700" for="password">New password</label>
            <input class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" type="password" id="password" name="password" minlength="6" required autocomplete="new-password">
        </div>
        <?php if (!empty($captchaPrompt)): ?>
            <div>
                <label class="block text-sm font-medium text-slate-700" for="captcha">Security Check</label>
                <div class="mt-1 flex items-center gap-3">
                    <span class="rounded bg-slate-100 px-3 py-2 text-sm text-slate-800"><?= e($captchaPrompt); ?></span>
                    <input class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" type="text" id="captcha" name="captcha" inputmode="numeric" autocomplete="off" required>
                </div>
            </div>
        <?php endif; ?>
        <button class="w-full rounded-md bg-blue-600 px-4 py-2 font-semibold text-white shadow hover:bg-blue-700" type="submit">Update password</button>
    </form>
    <p class="mt-4 text-sm text-slate-600">Enter a new password to finish resetting your account.</p>
<?php else: ?>
    <form method="POST" action="/password/reset" class="space-y-4" novalidate>
        <input type="hidden" name="_token" value="<?= e($csrfToken); ?>">
        <div>
            <label class="block text-sm font-medium text-slate-700" for="email">Email</label>
            <input class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" type="email" id="email" name="email" required autocomplete="email">
        </div>
        <?php if (!empty($captchaPrompt)): ?>
            <div>
                <label class="block text-sm font-medium text-slate-700" for="captcha">Security Check</label>
                <div class="mt-1 flex items-center gap-3">
                    <span class="rounded bg-slate-100 px-3 py-2 text-sm text-slate-800"><?= e($captchaPrompt); ?></span>
                    <input class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" type="text" id="captcha" name="captcha" inputmode="numeric" autocomplete="off" required>
                </div>
            </div>
        <?php endif; ?>
        <button class="w-full rounded-md bg-blue-600 px-4 py-2 font-semibold text-white shadow hover:bg-blue-700" type="submit">Send reset link</button>
    </form>
    <p class="mt-4 text-sm text-slate-600">We will email you a secure link to reset your password.</p>
<?php endif; ?>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
