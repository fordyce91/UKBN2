<?php

namespace App\Models;

class Newsletter
{
    public static function all(): array
    {
        return [
            ['id' => 1, 'subject' => 'Weekly Update', 'sent_at' => '2024-11-20'],
            ['id' => 2, 'subject' => 'Reward Expansions', 'sent_at' => '2024-12-05'],
        ];
    }
}
