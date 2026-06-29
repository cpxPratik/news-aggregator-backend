<?php

namespace App\DataSources;

use Illuminate\Http\Client\ConnectionException;

class GuardianSource implements NewsSource
{
    use ResilientHttpClientTrait;

    public function slug(): string
    {
        return 'the-guardian';
    }

    /**
     * @throws ConnectionException
     */
    public function fetch(): iterable
    {
        $response = $this->http()->get(
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
                hashedUrl: hash('sha256', $article['id']),
                category: $article['sectionName'] ?? 'General',
                author: $fields['byline'] ?? null,
                title: $article['webTitle'],
                content: $fields['bodyText'],
                url: $article['webUrl'],
                publishedAt: $article['webPublicationDate'] ?? now()->toDateTimeString(),
            );
        }

        return $articles;
    }
}
