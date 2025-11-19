<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;
use App\Services\Csrf;
use App\Services\RateLimiter;
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

        $rateKey = 'login:' . ($_SERVER['REMOTE_ADDR'] ?? 'cli');
        if (RateLimiter::tooManyAttempts($rateKey, 5, 300)) {
            Session::flash('error', 'Too many login attempts. Please wait before trying again.');
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

        $user = User::findByEmail($_POST['email']);
        if (!$user || !User::verifyPassword($user, $_POST['password'])) {
            RateLimiter::hit($rateKey, 5, 300);
            Session::flash('error', 'Invalid credentials provided.');
            $this->redirect('/login');
        }

        RateLimiter::clear($rateKey);

        if (!($user['email_verified'] ?? false)) {
            Session::flash('error', 'Please verify your email before signing in.');
            $this->redirect('/login');
        }

        Session::set('user', $user);
        Session::refresh();
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

        $rateKey = 'register:' . ($_SERVER['REMOTE_ADDR'] ?? 'cli');
        if (RateLimiter::tooManyAttempts($rateKey, 3, 600)) {
            Session::flash('error', 'Too many registration attempts. Please try again later.');
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

        try {
            $user = User::create($_POST);
        } catch (\InvalidArgumentException $exception) {
            RateLimiter::hit($rateKey, 3, 600);
            Session::flash('error', 'An account with that email already exists.');
            $this->redirect('/register');
        }

        RateLimiter::clear($rateKey);

        Session::set('user', $user);
        Session::refresh();
        Session::flash('success', 'Account created successfully. Please verify your email using the link we sent.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        if (!Csrf::check($_POST['_token'] ?? null)) {
            Session::flash('error', 'Invalid session token.');
            $this->redirect('/');
        }

        Session::invalidate();
        Session::flash('success', 'You have been signed out.');
        $this->redirect('/');
    }

    public function showReset(): void
    {
        $this->render('auth/reset', [
            'title' => 'Password Reset',
            'resetToken' => $_GET['token'] ?? null,
        ]);
    }

    public function reset(): void
    {
        if (!Csrf::check($_POST['_token'] ?? null)) {
            Session::flash('error', 'Invalid session token.');
            $this->redirect('/password/reset');
        }

        if (isset($_POST['token']) && isset($_POST['password'])) {
            $errors = Validator::validate($_POST, [
                'token' => 'required',
                'password' => 'required|min:6',
            ]);

            if (!empty($errors)) {
                Session::flash('error', 'Please provide a valid reset token and password.');
                $this->redirect('/password/reset?token=' . urlencode($_POST['token']));
            }

            if (User::completePasswordReset($_POST['token'], $_POST['password'])) {
                Session::flash('success', 'Your password has been reset. You can now sign in.');
                $this->redirect('/login');
            }

            Session::flash('error', 'The reset token is invalid or has expired.');
            $this->redirect('/password/reset');
        }

        $rateKey = 'password-reset:' . ($_SERVER['REMOTE_ADDR'] ?? 'cli');
        if (RateLimiter::tooManyAttempts($rateKey, 5, 600)) {
            Session::flash('error', 'Too many reset attempts. Please wait before trying again.');
            $this->redirect('/password/reset');
        }

        $errors = Validator::validate($_POST, [
            'email' => 'required|email',
        ]);

        if (!empty($errors)) {
            Session::flash('error', 'Please use a valid email address.');
            $this->redirect('/password/reset');
        }

        $token = User::startPasswordReset($_POST['email']);
        RateLimiter::hit($rateKey, 5, 600);

        if ($token) {
            Session::flash('success', 'We have emailed a password reset link: /password/reset?token=' . $token);
        } else {
            Session::flash('success', 'If your email is on file, a reset link was sent.');
        }

        $this->redirect('/login');
    }

    public function verifyEmail(): void
    {
        $token = $_GET['token'] ?? null;
        if (!$token) {
            Session::flash('error', 'Missing verification token.');
            $this->redirect('/login');
        }

        $user = User::markEmailVerified($token);
        if ($user) {
            Session::set('user', $user);
            Session::refresh();
            Session::flash('success', 'Email verified successfully. You can continue.');
            $this->redirect('/');
        }

        Session::flash('error', 'Invalid or expired verification token.');
        $this->redirect('/login');
    }
}
