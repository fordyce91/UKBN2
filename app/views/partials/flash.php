<?php use App\Services\Session; ?>
<?php $success = Session::getFlash('success'); $error = Session::getFlash('error'); ?>
<?php if ($success): ?>
    <div class="mb-4 rounded-md bg-green-50 p-4 text-green-800"><?= e($success); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="mb-4 rounded-md bg-red-50 p-4 text-red-800"><?= e($error); ?></div>
<?php endif; ?>
