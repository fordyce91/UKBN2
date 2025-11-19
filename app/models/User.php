<?php

namespace App\Models;

class User
{
    public static function create(array $data): array
    {
        return [
            'id' => rand(1000, 9999),
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => 'member',
        ];
    }

    public static function all(): array
    {
        return [
            ['id' => 1, 'name' => 'Admin User', 'email' => 'admin@example.com', 'role' => 'admin'],
            ['id' => 2, 'name' => 'Member Jane', 'email' => 'jane@example.com', 'role' => 'member'],
        ];
    }
}
