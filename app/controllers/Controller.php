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

    protected function requireAuth(): array
    {
        $user = Session::get('user');

        if (!$user) {
            Session::flash('error', 'You must be signed in to access that page.');
            $this->redirect('/login');
        }

        if (Session::isExpired()) {
            Session::invalidate();
            Session::flash('error', 'Your session has expired. Please sign in again.');
            $this->redirect('/login');
        }

        Session::refresh();

        return $user;
    }

    protected function requireRole(string|array $roles): array
    {
        $user = $this->requireAuth();
        $roles = (array) $roles;

        if (!in_array($user['role'] ?? '', $roles, true)) {
            Session::flash('error', 'You do not have permission to perform that action.');
            $this->redirect('/');
        }

        return $user;
    }

    protected function requireVerified(string|array $roles = []): array
    {
        $user = $roles ? $this->requireRole($roles) : $this->requireAuth();

        if (!($user['email_verified'] ?? false)) {
            Session::flash('error', 'Please verify your email before continuing.');
            $this->redirect('/login');
        }

        return $user;
    }
}
