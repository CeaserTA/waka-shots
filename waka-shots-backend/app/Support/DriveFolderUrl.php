<?php

namespace App\Support;

use InvalidArgumentException;

class DriveFolderUrl
{
    public static function extractId(string $url): string
    {
        $url = trim($url);
        $parts = parse_url($url);

        if (! is_array($parts) || ! isset($parts['host']) || ! str_ends_with(strtolower($parts['host']), 'google.com')) {
            throw new InvalidArgumentException('Enter a valid Google Drive folder link.');
        }

        if (preg_match('~/folders/([a-zA-Z0-9_-]+)~', $parts['path'] ?? '', $matches)) {
            return $matches[1];
        }

        parse_str($parts['query'] ?? '', $query);
        if (isset($query['id']) && preg_match('/^[a-zA-Z0-9_-]+$/', $query['id'])) {
            return $query['id'];
        }

        throw new InvalidArgumentException('Enter a valid Google Drive folder link.');
    }
}
