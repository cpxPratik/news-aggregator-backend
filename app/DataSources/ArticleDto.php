<?php

namespace App\DataSources;

final readonly class ArticleDto
{
    public function __construct(
        public string $hashedUrl,
        public string $category,
        public ?string $author,
        public string $title,
        public string $content,
        public string $url,
        public string $publishedAt,
    ) {
    }
}
