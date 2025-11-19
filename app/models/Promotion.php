<?php

namespace App\Models;

class Promotion
{
    public static function active(): array
    {
        return [
            ['id' => 1, 'name' => 'Holiday Bonus', 'points' => 500, 'expires_at' => '2024-12-31'],
            ['id' => 2, 'name' => 'Referral Reward', 'points' => 200, 'expires_at' => '2025-01-31'],
        ];
    }
}
