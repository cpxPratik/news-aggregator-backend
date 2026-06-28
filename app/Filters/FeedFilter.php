<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

readonly class FeedFilter
{
    public function __construct(private Request $request) {
    }

    public function apply(Builder $query): Builder
    {
        $user = $this->request->user();

        $sourceIds = $user->sources()->pluck('sources.id');
        $categoryIds = $user->categories()->pluck('categories.id');
        $authorIds = $user->authors()->pluck('authors.id');

        if ($sourceIds->isNotEmpty() || $categoryIds->isNotEmpty() || $authorIds->isNotEmpty()) {
            return $query->where(function ($q) use ($sourceIds, $categoryIds, $authorIds) {
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

        return $query;
    }
}
