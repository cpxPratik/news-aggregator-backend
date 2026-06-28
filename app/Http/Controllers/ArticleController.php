<?php

namespace App\Http\Controllers;

use App\Filters\ArticleFilter;
use App\Http\Requests\IndexArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index(IndexArticleRequest $request, ArticleFilter $filter)
    {
        $validated = (int) $request->validated('per_page', 15);

        $query = $filter->apply(Article::query()->with(['category', 'source', 'author']));

        $articles = $query->orderByDesc('published_at')->paginate($validated);

        return ArticleResource::collection($articles);
    }

    public function show(Article $article)
    {
        return new ArticleResource(
            $article->load(['source', 'category', 'author'])
        );
    }
}
