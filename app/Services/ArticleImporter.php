<?php

namespace App\Services;

use App\DataSources\ArticleDto;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Support\Str;

class ArticleImporter
{
    private const string DEFAULT_CATEGORY = 'general';

    public function import(Source $source, iterable $articles): int
    {
        $count = 0;
        foreach ($articles as $article) {
            $this->persist($source, $article);
            $count++;
        }

        return $count;
    }

    private function persist(Source $source, ArticleDto $article): void
    {
        Article::updateOrCreate(
            [
                'source_id' => $source->id,
                'hashed_url' => $article->hashedUrl,
            ],
            [
                'category_id' => $this->getCategoryId($article->category),
                'author_id' => $this->getAuthorId($article->author),
                'title' => mb_substr($article->title, 0, 255),
                'content' => $article->content,
                'url' => $article->url,
                'published_at' => $article->publishedAt,
            ],
        );
    }

    private function getAuthorId(?string $name): ?int
    {
        $name = $name ? trim($name) : null;
        if ($name === null || $name === '') {
            return null;
        }

        return Author::firstOrCreate(['slug' => Str::slug($name)])->id;
    }

    private function getCategoryId(string $name): int
    {
        if (trim($name) === '') {
            $name = self::DEFAULT_CATEGORY;
        }

        return Category::firstOrCreate(['slug' => Str::slug($name)])->id;
    }
}
