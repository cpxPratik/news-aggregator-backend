<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePreferencesRequest;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Models\User;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    public function show(Request $request)
    {
        return [
            'data' => $this->formatPreferences($request->user()),
        ];
    }

    public function update(UpdatePreferencesRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();

        $user->sources()->sync(Source::whereIn('slug', $validated['sources'] ?? [])->pluck('id'));
        $user->categories()->sync(Category::whereIn('slug', $validated['categories'] ?? [])->pluck('id'));
        $user->authors()->sync(Author::whereIn('slug', $validated['authors'] ?? [])->pluck('id'));

        return [
            'data' => $this->formatPreferences($user->load(['sources', 'categories', 'authors'])),
        ];
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
