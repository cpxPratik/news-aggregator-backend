<?php

namespace App\DataSources;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class NewsApiSource implements NewsSource
{
    private const array NEWSAPI_CATEGORIES = [
        'business', 'entertainment', 'general', 'health', 'science', 'sports', 'technology',
    ];

    public function slug(): string
    {
        return 'news-api';
    }

    /**
     * @throws ConnectionException
     */
    public function fetch(): iterable
    {
        $articles = [];
        foreach (self::NEWSAPI_CATEGORIES as $category) {
            $response = Http::acceptJson()->get('https://newsapi.org/v2/top-headlines', [
                'category' => $category,
                'pageSize' => 5,
                'apiKey' => config('services.newsapi.key'),
            ]);

            if ($response->failed()) {
                continue;
            }

            foreach ($response->json('articles', []) as $item) {
                if (empty($item['url']) || empty($item['title'])) {
                    continue;
                }

                $articles[] = new ArticleDto(
                    hash('sha256', $item['url']),
                    $category,
                    $item['author'] ?? null,
                    $item['title'],
                    $item['content'] ?? $item['description'] ?? $item['title'],
                    $item['url'],
                    $item['publishedAt'] ?? now()->toDateTimeString(),
                );
            }
        }

        return $articles;
    }
}
