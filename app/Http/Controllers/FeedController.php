<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $sourceIds = $user->sources()->pluck('sources.id');
        $categoryIds = $user->categories()->pluck('categories.id');
        $authorIds = $user->authors()->pluck('authors.id');

        $query = Article::query()->with(['source', 'category', 'author']);

        if ($sourceIds->isNotEmpty() || $categoryIds->isNotEmpty() || $authorIds->isNotEmpty()) {
            $query->where(function ($q) use ($sourceIds, $categoryIds, $authorIds) {
                if ($sourceIds->isNotEmpty()) {
                    $q->orWhereIn('source_id', $sourceIds);
                }
                if ($categoryIds->isNotEmpty()) {
                    $q->orWhereIn('category_id', $categoryIds);
                }
                if ($authorIds->isNotEmpty()) {
                    $q->orWhereIn('author_id', $authorIds);
                }
            });
        }

        return $query->orderByDesc('published_at')->paginate(10);
    }
}
