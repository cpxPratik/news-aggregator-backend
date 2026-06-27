<?php

namespace App\DataSources;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GuardianSource implements NewsSource
{
    public function slug(): string
    {
        return 'the-guardian';
    }

    /**
     * @throws ConnectionException
     */
    public function fetch(): iterable
    {
        $response = Http::acceptJson()->get(
            'https://content.guardianapis.com/search',
            [
                'order-by' => 'newest',
                'page-size' => 5,
                'show-fields' => 'bodyText,thumbnail,byline',
                'api-key' => config('services.guardian.key'),
            ]
        );

        if ($response->failed()) {
            return [];
        }

        $articles = [];
        foreach ($response->json('response.results', []) as $article) {
            if (empty($article['webUrl']) || empty($article['webTitle'])) {
                continue;
            }

            $fields = $article['fields'] ?? [];

            $articles[] = new ArticleDto(
                hash('sha256', $article['id']),
                $article['sectionName'] ?? 'General',
                $fields['byline'] ?? null,
                $article['webTitle'],
                $fields['bodyText'],
                $article['webUrl'],
                $article['webPublicationDate'] ?? now()->toDateTimeString(),
            );
        }

        return $articles;
    }
}
