<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            ['slug' => 'news-api'],
            ['slug' => 'the-guardian'],
            ['slug' => 'the-new-york-times'],
        ];

        foreach ($sources as $source) {
            Source::updateOrCreate($source);
        }
    }
}
