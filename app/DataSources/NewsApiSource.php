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
            $response = Http::acceptJson()->get(
                'https://newsapi.org/v2/top-headlines',
                [
                    'category' => $category,
                    'pageSize' => 2,
                    'apiKey' => config('services.newsapi.key'),
                ]
            );

            if ($response->failed()) {
                continue;
            }

            foreach ($response->json('articles', []) as $article) {
                if (empty($article['url']) || empty($article['title'])) {
                    continue;
                }

                $articles[] = new ArticleDto(
                    hash('sha256', $article['url']),
                    $category,
                    $article['author'] ?? null,
                    $article['title'],
                    $article['content'] ?? $article['description'] ?? $article['title'],
                    $article['url'],
                    $article['publishedAt'] ?? now()->toDateTimeString(),
                );
            }
        }

        return $articles;
    }
}
