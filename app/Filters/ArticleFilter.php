<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

readonly class ArticleFilter implements Filter
{
    public function __construct(private Request $request) {}

    public function apply(Builder $query): Builder
    {
        $this->search($query);
        $this->date($query);
        $this->category($query);
        $this->source($query);
        $this->author($query);

        return $query;
    }

    private function search(Builder $query): void
    {
        if (! $this->request->filled('q')) {
            return;
        }

        $searchString = $this->request->input('q');
        $query->where('title', 'like', "%{$searchString}%");
    }

    private function date(Builder $query): void
    {
        if ($this->request->filled('date')) {
            $query->whereDate('published_at', '=', $this->request->date('date'));
        }
    }

    private function category(Builder $query): void
    {
        if ($this->request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $this->request->input('category')));
        }
    }

    private function source(Builder $query): void
    {
        if ($this->request->filled('source')) {
            $query->whereHas('source', fn ($q) => $q->where('slug', $this->request->input('source')));
        }
    }

    private function author(Builder $query): void
    {
        if ($this->request->filled('author')) {
            $query->whereHas('author', fn ($q) => $q->where('slug', $this->request->input('author')));
        }
    }
}
