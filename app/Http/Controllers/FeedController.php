<?php

namespace App\Http\Controllers;

use App\Filters\ArticleFilter;
use App\Filters\FeedFilter;
use App\Http\Requests\IndexArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;

class FeedController extends Controller
{
    public function __invoke(IndexArticleRequest $request, FeedFilter $feedFilter, ArticleFilter $articleFilter)
    {
        $validated = (int) $request->validated('per_page', 10);

        $query = Article::query()->with(['source', 'category', 'author']);

        $feedFilter->apply($query);
        $articleFilter->apply($query);

        $articles = $query->orderByDesc('published_at')->paginate($validated);

        return ArticleResource::collection($articles);
    }
}
