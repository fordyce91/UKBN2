<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;
use App\Services\Captcha;
use App\Services\Csrf;
use App\Services\Logger;
use App\Services\RateLimiter;
use App\Services\Request;
use App\Services\Session;
use App\Services\Validator;

class AuthController extends Controller
{
    private array $securityConfig;
    private Request $request;
    private Logger $logger;

    public function __construct(array $securityConfig = [])
    {
        $this->securityConfig = $securityConfig;
        $this->request = Request::instance();
        $this->logger = Logger::get();
    }

    public function showLogin(): void
    {
        $this->render('auth/login', [
            'title' => 'Login',
            'captchaPrompt' => Captcha::prompt(),
        ]);
    }

    public function login(): void
    {
        if (!Csrf::check($_POST['_token'] ?? null)) {
            Session::flash('error', 'Invalid session token. Please try again.');
            $this->redirect('/login');
        }

        $rateConfig = $this->securityConfig['rate_limits']['login'] ?? ['attempts' => 5, 'decay' => 300];
        $rateKey = 'login:' . $this->request->ip() . ':' . strtolower((string) ($this->request->input('email') ?? ''));
        if (RateLimiter::tooManyAttempts($rateKey, $rateConfig['attempts'], $rateConfig['decay'])) {
            Session::flash('error', 'Too many login attempts. Please wait before trying again.');
            $this->logger->warning('Rate limit hit for login', ['rate_key' => $rateKey]);
            $this->redirect('/login');
        }

        $data = $this->request->all();
        $errors = Validator::validate($data, [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ]);

        if (!Captcha::verify($data['captcha'] ?? null)) {
            $errors['captcha'][] = 'CAPTCHA challenge failed.';
        }

        if (!empty($errors)) {
            Session::flash('error', 'Please correct the highlighted fields.');
            $this->logger->warning('Login validation failed', ['errors' => $errors]);
            $this->redirect('/login');
        }

        $user = User::findByEmail($data['email']);
        if (!$user || !User::verifyPassword($user, $data['password'])) {
            RateLimiter::hit($rateKey, $rateConfig['attempts'], $rateConfig['decay']);
            Session::flash('error', 'Invalid credentials provided.');
            $this->logger->warning('Invalid login attempt', ['email' => $data['email']]);
            $this->redirect('/login');
        }

        RateLimiter::clear($rateKey);

        if (!($user['email_verified'] ?? false)) {
            Session::flash('error', 'Please verify your email before signing in.');
            $this->logger->notice('Unverified email login attempt', ['email' => $data['email']]);
            $this->redirect('/login');
        }

        Session::set('user', $user);
        Session::refresh();
        Session::flash('success', 'Welcome back!');
        $this->logger->info('User logged in', ['user_id' => $user['id'], 'email' => $data['email']]);
        $this->redirect('/');
    }

    public function showRegister(): void
    {
        $this->render('auth/register', [
            'title' => 'Register',
            'captchaPrompt' => Captcha::prompt(),
        ]);
    }

    public function register(): void
    {
        if (!Csrf::check($_POST['_token'] ?? null)) {
            Session::flash('error', 'Invalid session token. Please try again.');
            $this->redirect('/register');
        }

        $rateConfig = $this->securityConfig['rate_limits']['register'] ?? ['attempts' => 3, 'decay' => 600];
        $rateKey = 'register:' . $this->request->ip();
        if (RateLimiter::tooManyAttempts($rateKey, $rateConfig['attempts'], $rateConfig['decay'])) {
            Session::flash('error', 'Too many registration attempts. Please try again later.');
            $this->logger->warning('Rate limit hit for register', ['rate_key' => $rateKey]);
            $this->redirect('/register');
        }

        $data = $this->request->all();
        $errors = Validator::validate($data, [
            'name' => ['required', 'min:2'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ]);

        if (!Captcha::verify($data['captcha'] ?? null)) {
            $errors['captcha'][] = 'CAPTCHA challenge failed.';
        }

        if (!empty($errors)) {
            Session::flash('error', 'Please correct the highlighted fields.');
            $this->logger->warning('Registration validation failed', ['errors' => $errors]);
            $this->redirect('/register');
        }

        try {
            $user = User::create($data);
        } catch (\InvalidArgumentException $exception) {
            RateLimiter::hit($rateKey, $rateConfig['attempts'], $rateConfig['decay']);
            Session::flash('error', 'An account with that email already exists.');
            $this->logger->warning('Duplicate registration attempt', ['email' => $data['email']]);
            $this->redirect('/register');
        }

        RateLimiter::clear($rateKey);

        Session::set('user', $user);
        Session::refresh();
        Session::flash('success', 'Account created successfully. Please verify your email using the link we sent.');
        $this->logger->info('User registered', ['user_id' => $user['id'], 'email' => $data['email']]);
        $this->redirect('/login');
    }

    public function logout(): void
    {
        if (!Csrf::check($_POST['_token'] ?? null)) {
            Session::flash('error', 'Invalid session token.');
            $this->redirect('/');
        }

        $user = Session::get('user');
        Session::invalidate();
        Session::flash('success', 'You have been signed out.');
        $this->logger->info('User logged out', ['user_id' => $user['id'] ?? null]);
        $this->redirect('/');
    }

    public function showReset(): void
    {
        $this->render('auth/reset', [
            'title' => 'Password Reset',
            'resetToken' => $this->request->input('token'),
            'captchaPrompt' => Captcha::prompt(),
        ]);
    }

    public function reset(): void
    {
        if (!Csrf::check($_POST['_token'] ?? null)) {
            Session::flash('error', 'Invalid session token.');
            $this->redirect('/password/reset');
        }

        $data = $this->request->all();
        $rateConfig = $this->securityConfig['rate_limits']['password-reset'] ?? ['attempts' => 5, 'decay' => 600];
        $rateKey = 'password-reset:' . $this->request->ip();

        if (isset($data['token']) && isset($data['password'])) {
            $errors = Validator::validate($data, [
                'token' => ['required'],
                'password' => ['required', 'min:6'],
            ]);

            if (!Captcha::verify($data['captcha'] ?? null)) {
                $errors['captcha'][] = 'CAPTCHA challenge failed.';
            }

            if (!empty($errors)) {
                Session::flash('error', 'Please provide a valid reset token and password.');
                $this->logger->warning('Password reset validation failed', ['errors' => $errors]);
                $this->redirect('/password/reset?token=' . urlencode((string) $data['token']));
            }

            if (User::completePasswordReset($data['token'], $data['password'])) {
                Session::flash('success', 'Your password has been reset. You can now sign in.');
                $this->logger->info('Password reset completed', ['email' => $data['email'] ?? null]);
                $this->redirect('/login');
            }

            Session::flash('error', 'The reset token is invalid or has expired.');
            $this->logger->warning('Password reset failed', ['token' => $data['token']]);
            $this->redirect('/password/reset');
        }

        if (RateLimiter::tooManyAttempts($rateKey, $rateConfig['attempts'], $rateConfig['decay'])) {
            Session::flash('error', 'Too many reset attempts. Please wait before trying again.');
            $this->logger->warning('Rate limit hit for password reset', ['rate_key' => $rateKey]);
            $this->redirect('/password/reset');
        }

        $errors = Validator::validate($data, [
            'email' => ['required', 'email'],
        ]);

        if (!Captcha::verify($data['captcha'] ?? null)) {
            $errors['captcha'][] = 'CAPTCHA challenge failed.';
        }

        if (!empty($errors)) {
            Session::flash('error', 'Please use a valid email address.');
            $this->logger->warning('Password reset request validation failed', ['errors' => $errors]);
            $this->redirect('/password/reset');
        }

        $token = User::startPasswordReset($data['email']);
        RateLimiter::hit($rateKey, $rateConfig['attempts'], $rateConfig['decay']);

        if ($token) {
            Session::flash('success', 'We have emailed a password reset link: /password/reset?token=' . $token);
            $this->logger->info('Password reset token issued', ['email' => $data['email']]);
        } else {
            Session::flash('success', 'If your email is on file, a reset link was sent.');
            $this->logger->notice('Password reset attempted for unknown email', ['email' => $data['email']]);
        }

        $this->redirect('/login');
    }

    public function verifyEmail(): void
    {
        $token = $this->request->input('token');
        if (!$token) {
            Session::flash('error', 'Missing verification token.');
            $this->redirect('/login');
        }

        $user = User::markEmailVerified($token);
        if ($user) {
            Session::set('user', $user);
            Session::refresh();
            Session::flash('success', 'Email verified successfully. You can continue.');
            $this->logger->info('Email verified', ['user_id' => $user['id']]);
            $this->redirect('/');
        }

        Session::flash('error', 'Invalid or expired verification token.');
        $this->logger->warning('Email verification failed', ['token' => $token]);
        $this->redirect('/login');
    }
}
