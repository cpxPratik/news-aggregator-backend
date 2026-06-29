<?php

namespace Tests\Feature;

use Database\Seeders\SourceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_sources(): void
    {
        $this->seed(SourceSeeder::class);

        $this->getJson('/api/v1/sources')->assertOk()->assertJsonCount(3, 'data');
    }
}
