<?php

namespace App\Controllers;

use App\Models\News;
use App\Services\Session;

class NewsController extends Controller
{
    public function index(): void
    {
        $news = News::all();
        $this->render('news/index', ['news' => $news, 'title' => 'News']);
    }
}
