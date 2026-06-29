<?php

namespace App\DataSources;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

trait ResilientHttpClientTrait
{
    protected function http(): PendingRequest
    {
        return Http::acceptJson()
            ->timeout(10)
            ->retry(3, 200, throw: false);
    }
}
