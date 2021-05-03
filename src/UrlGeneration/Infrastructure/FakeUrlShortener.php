<?php
declare(strict_types=1);

namespace App\UrlGeneration\Infrastructure;

use App\UrlGeneration\Domain\UrlShortener;

final class FakeUrlShortener implements UrlShortener
{
    public function shorten(string $targetUrl): string
    {
        return 'short://' . $targetUrl;
    }
}