<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return \Inertia\Inertia::render('Home/Index');
    }
}