<?php

declare(strict_types=1);

namespace App\Tests\Unit\UrlGeneration\Domain;

use App\UrlGeneration\Application\UnvalidatedUrlRequest;

class UnvalidatedUrlRequestStub
{
    const SIMPLE_TARGET_URL = 'https://some.target.url';

    public static function simpleRequest(): UnvalidatedUrlRequest
    {
        return self::createRequest(self::SIMPLE_TARGET_URL);
    }

    private static function createRequest(string $url): UnvalidatedUrlRequest
    {
        return UnvalidatedUrlRequest::fromJson(
            json_encode([
                'targetUrl' => $url,
            ])
        );
    }
}