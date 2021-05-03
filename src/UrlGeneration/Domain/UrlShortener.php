<?php
declare(strict_types=1);

namespace App\UrlGeneration\Domain;

interface UrlShortener
{
    public function shorten(string $targetUrl): string;
}