<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::query()
            ->with(['category', 'author'])
            ->where('is_published', true)
            ->when($request->filled('category'), function ($query) use ($request): void {
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $request->input('category')));
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('articles.index', compact('articles'));
    }

    public function show(Article $article)
    {
        abort_unless($article->is_published, 404);

        $article->load(['category', 'author']);

        return view('articles.show', compact('article'));
    }
}
