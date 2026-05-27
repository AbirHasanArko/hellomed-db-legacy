<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Http\Requests\StoreArticleRequest;

class AdminArticleController extends Controller
{
    public function index()
    {
        return view('admin.articles.index', [
            'articles' => Article::query()->with('category')->latest()->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.articles.create', [
            'categories' => ArticleCategory::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreArticleRequest $request)
    {
        Article::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'is_featured' => $request->boolean('is_featured'),
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('admin.articles.index')->with('status', 'Article created.');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.edit', [
            'article' => $article,
            'categories' => ArticleCategory::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(StoreArticleRequest $request, Article $article)
    {
        $article->update([
            ...$request->validated(),
            'is_featured' => $request->boolean('is_featured'),
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('admin.articles.index')->with('status', 'Article updated.');
    }
}
