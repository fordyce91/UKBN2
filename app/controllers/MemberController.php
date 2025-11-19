<?php

namespace App\Controllers;

use App\Models\Promotion;
use App\Services\Session;

class MemberController extends Controller
{
    public function promotions(): void
    {
        $this->requireVerified(['member', 'admin']);
        $promotions = Promotion::active();
        $this->render('member/promotions', ['promotions' => $promotions, 'title' => 'Member Promotions']);
    }
}
