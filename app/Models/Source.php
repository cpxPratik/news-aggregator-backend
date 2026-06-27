<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    protected $fillable = ['slug'];

    public function articles(): HasMany|Source
    {
        return $this->hasMany(Article::class);
    }
}
