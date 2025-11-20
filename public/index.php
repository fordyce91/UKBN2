<?php

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\MemberController;
use App\Controllers\NewsController;
use App\Services\Env;
use App\Services\ErrorHandler;
use App\Services\Request;
use App\Services\Router;
use App\Services\Security;
use App\Services\Session;
use App\Services\View;

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require $composerAutoload;
} else {
    spl_autoload_register(function ($class) {
        $prefixes = [
            'App\\' => __DIR__ . '/../app/',
            'Psr\\' => __DIR__ . '/../app/support/Psr/',
        ];

        foreach ($prefixes as $prefix => $baseDir) {
            if (!str_starts_with($class, $prefix)) {
                continue;
            }

            $relative = substr($class, strlen($prefix));
            $relativePath = str_replace('\\', '/', $relative) . '.php';
            $file = $baseDir . $relativePath;

            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    });
}

require __DIR__ . '/../app/helpers.php';

Session::start();
Env::load(__DIR__ . '/../.env');

$appConfig = require __DIR__ . '/../app/config/app.php';
$securityConfig = require __DIR__ . '/../app/config/security.php';

$debug = $appConfig['env'] !== 'production';
ini_set('display_errors', $debug ? '1' : '0');
ini_set('display_startup_errors', $debug ? '1' : '0');
error_reporting($debug ? E_ALL : E_ALL & ~E_DEPRECATED & ~E_STRICT);

ErrorHandler::register($appConfig['env']);
Security::applyHeaders($securityConfig);

$request = Request::instance();

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

$router->add('GET', '/login', function () use ($securityConfig) {
    (new AuthController($securityConfig))->showLogin();
});

$router->add('GET', '/email/verify', function () use ($securityConfig) {
    (new AuthController($securityConfig))->verifyEmail();
});

$router->add('POST', '/login', function () use ($securityConfig) {
    (new AuthController($securityConfig))->login();
});

$router->add('GET', '/register', function () use ($securityConfig) {
    (new AuthController($securityConfig))->showRegister();
});

$router->add('POST', '/register', function () use ($securityConfig) {
    (new AuthController($securityConfig))->register();
});

$router->add('POST', '/logout', function () use ($securityConfig) {
    (new AuthController($securityConfig))->logout();
});

$router->add('GET', '/password/reset', function () use ($securityConfig) {
    (new AuthController($securityConfig))->showReset();
});

$router->add('POST', '/password/reset', function () use ($securityConfig) {
    (new AuthController($securityConfig))->reset();
});

$router->add('POST', '/admin/promotions', function () {
    (new AdminController())->createPromotion();
});

$router->add('POST', '/admin/newsletters', function () {
    (new AdminController())->sendNewsletter();
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
