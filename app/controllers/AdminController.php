<?php

namespace App\Controllers;

use App\Models\News;
use App\Models\Promotion;
use App\Models\User;
use App\Models\Newsletter;
use App\Services\Session;

class AdminController extends Controller
{
    public function index(): void
    {
        $this->requireVerified('admin');

        $this->render('admin/index', [
            'news' => News::all(),
            'promotions' => Promotion::active(),
            'users' => User::all(),
            'newsletters' => Newsletter::all(),
            'title' => 'Admin Panel',
        ]);
    }

    public function createPromotion(): void
    {
        $this->requireVerified('admin');

        // Placeholder for restricted promotion creation logic.
        Session::flash('success', 'Promotion creation is restricted to administrators.');
        $this->redirect('/admin');
    }

    public function sendNewsletter(): void
    {
        $this->requireVerified('admin');

        // Placeholder for restricted newsletter sending logic.
        Session::flash('success', 'Newsletter creation is limited to administrators.');
        $this->redirect('/admin');
    }
}
