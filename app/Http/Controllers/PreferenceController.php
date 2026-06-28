<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Models\User;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    public function show(Request $request)
    {
        return $this->formatPreferences($request->user());
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'sources' => 'sometimes|array',
            'sources.*' => 'string|exists:sources,slug',
            'categories' => 'sometimes|array',
            'categories.*' => 'string|exists:categories,slug',
            'authors' => 'sometimes|array',
            'authors.*' => 'string|exists:authors,slug',
        ]);

        $user = $request->user();

        if (array_key_exists('sources', $data)) {
            $user->sources()->sync(Source::whereIn('slug', $data['sources'])->pluck('id'));
        }
        if (array_key_exists('categories', $data)) {
            $user->categories()->sync(Category::whereIn('slug', $data['categories'])->pluck('id'));
        }
        if (array_key_exists('authors', $data)) {
            $user->authors()->sync(Author::whereIn('slug', $data['authors'])->pluck('id'));
        }

        return $this->formatPreferences($user->load(['sources', 'categories', 'authors']));
    }

    private function formatPreferences(User $user): array
    {
        return [
            'sources' => $user->sources->pluck('slug')->values()->all(),
            'categories' => $user->categories->pluck('slug')->values()->all(),
            'authors' => $user->authors->pluck('slug')->values()->all(),
        ];
    }
}
