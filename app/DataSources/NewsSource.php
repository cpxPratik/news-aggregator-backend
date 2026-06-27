<?php

namespace App\DataSources;

use Illuminate\Http\Client\ConnectionException;

interface NewsSource
{
    /**
     * @return string
     */
    public function slug(): string;

    /**
     * @return iterable
     * @throws ConnectionException
     */
    public function fetch(): iterable;
}
