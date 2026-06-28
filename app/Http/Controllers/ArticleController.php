<?php

namespace App\Http\Controllers;

use App\Filters\ArticleFilter;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request, ArticleFilter $filter)
    {
        $request->validate([
            'q' => 'sometimes|string',
            'date' => 'sometimes|date',
            'category' => 'sometimes|string',
            'source' => 'sometimes|string',
            'author' => 'sometimes|string',
            'per_page' => 'sometimes|integer|min:1|max:50',
        ]);

        $query = $filter->apply(Article::query()->with(['category', 'source', 'author']));

        $articles = $query->orderByDesc('published_at')
            ->paginate((int) $request->input('per_page', 10));

        return ArticleResource::collection($articles);
    }

    public function show(Article $article)
    {
        return new ArticleResource(
            $article->load(['source', 'category', 'author'])
        );
    }
}
