# Community Hub

A lightweight PHP front controller with public news pages, a member rewards portal, an admin dashboard, and Tailwind-powered UI scaffolding. Sessions, CSRF tokens, and basic validation are built-in for quick prototyping.

## Structure
- `public/`: Front controller entrypoint, rewrite rules, and web assets output.
- `app/`: Config, controllers, models, services, and view templates.
- `resources/css/`: Tailwind input stylesheet; build outputs to `public/assets/app.css`.

## Getting started
1. Install PHP 8.1+, Composer, and Node.js locally.
2. Copy `.env.example` to `.env` (create as needed) and adjust app settings.
3. Install PHP dependencies and generate autoload files:
   ```bash
   composer install
   ```
4. Install JS tooling and build styles:
   ```bash
   npm install
   npm run build
   ```
5. Point your web server to `public/` (use the included `.htaccess` or `public/nginx.conf` snippet). When using PHP's built-in server:
   ```bash
   php -S localhost:8000 -t public
   ```
