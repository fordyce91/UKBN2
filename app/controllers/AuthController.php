<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;
use App\Services\Csrf;
use App\Services\Session;
use App\Services\Validator;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->render('auth/login', ['title' => 'Login']);
    }

    public function login(): void
    {
        if (!Csrf::check($_POST['_token'] ?? null)) {
            Session::flash('error', 'Invalid session token. Please try again.');
            $this->redirect('/login');
        }

        $errors = Validator::validate($_POST, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!empty($errors)) {
            Session::flash('error', 'Please correct the highlighted fields.');
            $this->redirect('/login');
        }

        $user = [
            'name' => 'Member',
            'email' => $_POST['email'],
            'role' => $_POST['email'] === 'admin@example.com' ? 'admin' : 'member',
        ];

        Session::set('user', $user);
        Session::flash('success', 'Welcome back!');
        $this->redirect('/');
    }

    public function showRegister(): void
    {
        $this->render('auth/register', ['title' => 'Register']);
    }

    public function register(): void
    {
        if (!Csrf::check($_POST['_token'] ?? null)) {
            Session::flash('error', 'Invalid session token. Please try again.');
            $this->redirect('/register');
        }

        $errors = Validator::validate($_POST, [
            'name' => 'required|min:2',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!empty($errors)) {
            Session::flash('error', 'Please correct the highlighted fields.');
            $this->redirect('/register');
        }

        $user = User::create($_POST);
        Session::set('user', $user);
        Session::flash('success', 'Account created successfully.');
        $this->redirect('/member/promotions');
    }

    public function logout(): void
    {
        if (!Csrf::check($_POST['_token'] ?? null)) {
            Session::flash('error', 'Invalid session token.');
            $this->redirect('/');
        }

        Session::forget('user');
        Session::flash('success', 'You have been signed out.');
        $this->redirect('/');
    }

    public function showReset(): void
    {
        $this->render('auth/reset', ['title' => 'Password Reset']);
    }

    public function reset(): void
    {
        if (!Csrf::check($_POST['_token'] ?? null)) {
            Session::flash('error', 'Invalid session token.');
            $this->redirect('/password/reset');
        }

        $errors = Validator::validate($_POST, [
            'email' => 'required|email',
        ]);

        if (!empty($errors)) {
            Session::flash('error', 'Please use a valid email address.');
            $this->redirect('/password/reset');
        }

        Session::flash('success', 'If your email is on file, a reset link was sent.');
        $this->redirect('/login');
    }
}
