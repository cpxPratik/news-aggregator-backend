<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = [
        'source_id',
        'hashed_url',
        'category_id',
        'author_id',
        'title',
        'content',
        'url',
        'published_at',
    ];

    protected $casts = ['published_at' => 'immutable_datetime'];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }
}
