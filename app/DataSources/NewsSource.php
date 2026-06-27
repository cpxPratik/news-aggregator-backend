<?php

namespace App\DataSources;

use Illuminate\Http\Client\ConnectionException;

interface NewsSource
{
    public function slug(): string;

    /**
     * @throws ConnectionException
     */
    public function fetch(): iterable;
}
