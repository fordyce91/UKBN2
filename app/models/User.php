<?php

namespace App\Models;

class User
{
    private static array $defaultUsers = [];

    public static function create(array $data): array
    {
        $users = &self::store();

        $email = strtolower(trim($data['email']));
        if (isset($users[$email])) {
            throw new \InvalidArgumentException('User already exists.');
        }

        $user = [
            'id' => self::nextId(),
            'name' => $data['name'],
            'email' => $email,
            'password_hash' => self::hashPassword($data['password']),
            'role' => 'member',
            'email_verified' => false,
            'verification_token' => bin2hex(random_bytes(16)),
            'reset_token' => null,
            'reset_expires_at' => null,
        ];

        $users[$email] = $user;

        return $user;
    }

    public static function all(): array
    {
        $users = self::store();
        return array_values($users);
    }

    public static function findByEmail(string $email): ?array
    {
        $users = self::store();
        $normalized = strtolower(trim($email));
        return $users[$normalized] ?? null;
    }

    public static function update(array $user): void
    {
        $users = &self::store();
        $users[$user['email']] = $user;
    }

    public static function verifyPassword(array $user, string $password): bool
    {
        return password_verify($password, $user['password_hash']);
    }

    public static function markEmailVerified(string $token): ?array
    {
        $users = &self::store();
        foreach ($users as $email => $user) {
            if (($user['verification_token'] ?? null) === $token) {
                $user['email_verified'] = true;
                $user['verification_token'] = null;
                $users[$email] = $user;
                return $user;
            }
        }

        return null;
    }

    public static function startPasswordReset(string $email): ?string
    {
        $users = &self::store();
        $normalized = strtolower(trim($email));

        if (!isset($users[$normalized])) {
            return null;
        }

        $token = bin2hex(random_bytes(24));
        $users[$normalized]['reset_token'] = $token;
        $users[$normalized]['reset_expires_at'] = time() + 3600;

        return $token;
    }

    public static function completePasswordReset(string $token, string $password): bool
    {
        $users = &self::store();
        foreach ($users as $email => $user) {
            if (($user['reset_token'] ?? null) === $token && ($user['reset_expires_at'] ?? 0) > time()) {
                $user['password_hash'] = self::hashPassword($password);
                $user['reset_token'] = null;
                $user['reset_expires_at'] = null;
                $users[$email] = $user;
                return true;
            }
        }

        return false;
    }

    private static function &store(): array
    {
        if (empty($_SESSION['_users'])) {
            $_SESSION['_users'] = self::seedDefaults();
        }

        return $_SESSION['_users'];
    }

    private static function seedDefaults(): array
    {
        if (!self::$defaultUsers) {
            $adminPassword = self::hashPassword('admin-password');
            $memberPassword = self::hashPassword('member-password');

            self::$defaultUsers = [
                'admin@example.com' => [
                    'id' => 1,
                    'name' => 'Admin User',
                    'email' => 'admin@example.com',
                    'password_hash' => $adminPassword,
                    'role' => 'admin',
                    'email_verified' => true,
                    'verification_token' => null,
                    'reset_token' => null,
                    'reset_expires_at' => null,
                ],
                'member@example.com' => [
                    'id' => 2,
                    'name' => 'Member Jane',
                    'email' => 'member@example.com',
                    'password_hash' => $memberPassword,
                    'role' => 'member',
                    'email_verified' => true,
                    'verification_token' => null,
                    'reset_token' => null,
                    'reset_expires_at' => null,
                ],
            ];
        }

        return self::$defaultUsers;
    }

    private static function nextId(): int
    {
        $users = self::store();
        $ids = array_map(fn ($user) => $user['id'], $users);
        return empty($ids) ? 1 : max($ids) + 1;
    }

    private static function hashPassword(string $password): string
    {
        if (defined('PASSWORD_ARGON2ID')) {
            return password_hash($password, PASSWORD_ARGON2ID);
        }

        return password_hash($password, PASSWORD_BCRYPT);
    }
}
