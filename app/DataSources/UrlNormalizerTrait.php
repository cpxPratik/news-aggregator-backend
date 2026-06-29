<?php

namespace App\DataSources;

trait UrlNormalizerTrait
{
    protected function normalizeUrl(string $url): string
    {
        $url = trim($url);

        // Removes https://, http://, and www. case insensitively
        $url = (string) preg_replace('~^https?://(www\.)?~i', '', $url);

        // Splits the host and paths/queries
        preg_match('~^([^/?#]*)(.*)$~', $url, $parts);

        // The host is lowercased here
        return rtrim(strtolower($parts[1]).$parts[2], '/');
    }
}
