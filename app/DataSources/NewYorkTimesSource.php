<?php

namespace App\DataSources;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class NewYorkTimesSource implements NewsSource
{
    public function slug(): string
    {
        return 'the-new-york-times';
    }

    /**
     * @throws ConnectionException
     */
    public function fetch(): iterable
    {
        $response = Http::acceptJson()->get(
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
                hash('sha256', $article['url']),
                $article['section'] ?? 'General',
                $article['byline'] ?? null,
                $article['title'],
                $article['abstract'] ?? $article['title'],
                $article['url'],
                $article['published_date'] ?? now()->toDateTimeString(),
            );
        }

        return $articles;
    }
}
