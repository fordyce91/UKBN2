<?php

namespace App\Controllers;

use App\Models\Promotion;
use App\Services\Session;

class MemberController extends Controller
{
    public function promotions(): void
    {
        $this->requireAuth();
        $promotions = Promotion::active();
        $this->render('member/promotions', ['promotions' => $promotions, 'title' => 'Member Promotions']);
    }
}
