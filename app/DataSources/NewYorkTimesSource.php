<?php

namespace App\DataSources;

use Illuminate\Http\Client\ConnectionException;

class NewYorkTimesSource implements NewsSource
{
    use ResilientHttpClientTrait;
    use UrlNormalizerTrait;

    public function slug(): string
    {
        return 'the-new-york-times';
    }

    /**
     * @throws ConnectionException
     */
    public function fetch(): iterable
    {
        $response = $this->http()->get(
            'https://api.nytimes.com/svc/topstories/v2/home.json',
            [
                'api-key' => config('services.nyt.key'),
            ]
        );

        if ($response->failed()) {
            return [];
        }

        $articles = [];
        foreach ($response->json('results', []) as $article) {
            if (empty($article['url']) || empty($article['title'])) {
                continue;
            }

            $articles[] = new ArticleDto(
                hashedUrl: hash('sha256', $this->normalizeUrl($article['uri'] ?? $article['url'])),
                category: $article['section'] ?? 'General',
                author: $article['byline'] ?? null,
                title: $article['title'],
                content: $article['abstract'] ?? $article['title'],
                url: $article['url'],
                publishedAt: $article['published_date'] ?? now()->toDateTimeString(),
            );
        }

        return $articles;
    }
}
