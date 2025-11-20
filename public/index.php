<?php

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\MemberController;
use App\Controllers\NewsController;
use App\Services\Env;
use App\Services\Router;
use App\Services\Session;
use App\Services\View;

// Show all errors to aid debugging during development
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require $composerAutoload;
} else {
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $baseDir = __DIR__ . '/../app/';
        if (str_starts_with($class, $prefix)) {
            $relative = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) {
                require $file;
            }
        }
    });
}

Session::start();
Env::load(__DIR__ . '/../.env');
$config = require __DIR__ . '/../app/config/app.php';

$router = new Router();

$router->add('GET', '/', function () {
    echo View::render('home', ['title' => 'Welcome']);
});

$router->add('GET', '/news', function () {
    (new NewsController())->index();
});

$router->add('GET', '/member/promotions', function () {
    (new MemberController())->promotions();
});

$router->add('GET', '/admin', function () {
    (new AdminController())->index();
});

$router->add('GET', '/login', function () {
    (new AuthController())->showLogin();
});

$router->add('GET', '/email/verify', function () {
    (new AuthController())->verifyEmail();
});

$router->add('POST', '/login', function () {
    (new AuthController())->login();
});

$router->add('GET', '/register', function () {
    (new AuthController())->showRegister();
});

$router->add('POST', '/register', function () {
    (new AuthController())->register();
});

$router->add('POST', '/logout', function () {
    (new AuthController())->logout();
});

$router->add('GET', '/password/reset', function () {
    (new AuthController())->showReset();
});

$router->add('POST', '/password/reset', function () {
    (new AuthController())->reset();
});

$router->add('POST', '/admin/promotions', function () {
    (new AdminController())->createPromotion();
});

$router->add('POST', '/admin/newsletters', function () {
    (new AdminController())->sendNewsletter();
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
