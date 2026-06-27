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
        $response = Http::acceptJson()->get('https://content.guardianapis.com/search', [
            'order-by' => 'newest',
            'page-size' => 5,
            'show-fields' => 'bodyText,thumbnail,byline',
            'api-key' => config('services.guardian.key'),
        ]);

        if ($response->failed()) {
            return [];
        }

        $articles = [];
        foreach ($response->json('response.results', []) as $item) {
            if (empty($item['webUrl']) || empty($item['webTitle'])) {
                continue;
            }

            $fields = $item['fields'] ?? [];

            $articles[] = new ArticleDto(
                hash('sha256', $item['id']),
                $item['sectionName'] ?? 'General',
                $fields['byline'] ?? null,
                $item['webTitle'],
                $fields['bodyText'],
                $item['webUrl'],
                $item['webPublicationDate'] ?? now()->toDateTimeString(),
            );
        }

        return $articles;
    }
}
