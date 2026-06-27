<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

#[Signature('app:fetch-news')]
#[Description('Fetch the latest articles from the news sources')]
class FetchNews extends Command
{
    private const NEWSAPI_CATEGORIES = [
        'business', 'entertainment', 'general', 'health', 'science', 'sports', 'technology',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $source = Source::where('slug', 'news-api')->first();
        if (! $source) {
            return self::FAILURE;
        }

        $count = 0;
        foreach (self::NEWSAPI_CATEGORIES as $category) {
            $response = Http::acceptJson()->get('https://newsapi.org/v2/top-headlines', [
                'category' => $category,
                'pageSize' => 50,
                'apiKey' => config('services.newsapi.key'),
            ]);

            if ($response->failed()) {
                $this->warn("NewsAPI fetch failed for category: {$category}.");

                continue;
            }

            $categoryId = Category::firstOrCreate(['slug' => Str::slug($category)])->id;

            foreach ($response->json('articles', []) as $item) {
                if (empty($item['url']) || empty($item['title'])) {
                    continue;
                }

                Article::updateOrCreate(
                    ['source_id' => $source->id, 'hashed_url' => hash('sha256', $item['url'])],
                    [
                        'category_id' => $categoryId,
                        'author_id' => $this->authorId($item['author'] ?? null),
                        'title' => $item['title'],
                        'content' => $item['content'] ?? $item['description'] ?? $item['title'],
                        'url' => $item['url'],
                        'published_at' => $item['publishedAt'] ?? now(),
                    ],
                );
                $count++;
            }
        }

        $this->info("NewsAPI: {$count} articles fetched.");
        return self::SUCCESS;
    }

    private function authorId(?string $name): ?int
    {
        $name = $name ? trim($name) : null;
        if ($name === null || $name === '') {
            return null;
        }

        return Author::firstOrCreate(['slug' => Str::slug($name)])->id;
    }
}
