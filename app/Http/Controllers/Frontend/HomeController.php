<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Department;
use App\Models\Doctor;

class HomeController extends Controller
{
    public function index()
    {
        return view('public.home', [
            'departments' => Department::query()->where('is_active', true)->latest()->take(6)->get(),
            'doctors' => Doctor::query()->where('is_active', true)->latest()->take(8)->get(),
            'articles' => Article::query()->where('is_published', true)->latest('published_at')->take(3)->get(),
        ]);
    }
}
