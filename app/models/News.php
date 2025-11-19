<?php

namespace App\Models;

class News
{
    public static function all(): array
    {
        return [
            ['id' => 1, 'title' => 'Welcome to the Community', 'body' => 'Stay connected with announcements and updates.', 'published_at' => '2024-10-01'],
            ['id' => 2, 'title' => 'New Rewards', 'body' => 'Members can now redeem points for exclusive perks.', 'published_at' => '2024-11-15'],
        ];
    }
}
