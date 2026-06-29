<?php

namespace App\DataSources;

use Illuminate\Http\Client\ConnectionException;

class NewsApiSource implements NewsSource
{
    use ResilientHttpClientTrait;
    use UrlNormalizerTrait;

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
            $response = $this->http()->get(
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
                    hashedUrl: hash('sha256', $this->normalizeUrl($article['url'])),
                    category: $category,
                    author: $article['author'] ?? null,
                    title: $article['title'],
                    content: $article['content'] ?? $article['description'] ?? $article['title'],
                    url: $article['url'],
                    publishedAt: $article['publishedAt'] ?? now()->toDateTimeString(),
                );
            }
        }

        return $articles;
    }
}
