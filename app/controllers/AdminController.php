<?php

namespace App\Controllers;

use App\Models\News;
use App\Models\Promotion;
use App\Models\User;
use App\Models\Newsletter;

class AdminController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();

        $this->render('admin/index', [
            'news' => News::all(),
            'promotions' => Promotion::active(),
            'users' => User::all(),
            'newsletters' => Newsletter::all(),
            'title' => 'Admin Panel',
        ]);
    }
}
