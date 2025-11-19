<?php

namespace App\Controllers;

use App\Services\Session;
use App\Services\View;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        echo View::render($view, $data);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    protected function requireAuth(): void
    {
        if (!Session::get('user')) {
            Session::flash('error', 'You must be signed in to access that page.');
            $this->redirect('/login');
        }
    }

    protected function requireAdmin(): void
    {
        $user = Session::get('user');
        if (!$user || ($user['role'] ?? '') !== 'admin') {
            Session::flash('error', 'Administrator access required.');
            $this->redirect('/login');
        }
    }
}
