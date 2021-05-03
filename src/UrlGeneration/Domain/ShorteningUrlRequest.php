<?php
declare(strict_types=1);

namespace App\UrlGeneration\Domain;

use App\UrlGeneration\Application\UnvalidatedUrlRequest;

final class ShorteningUrlRequest
{
    private string $targetUrl;

    private function __construct(string $targetUrl)
    {
        $this->targetUrl = $targetUrl;
    }

    public static function fromUnvalidatedUrlRequest(
        UnvalidatedUrlRequest $unvalidatedUrlRequest
    ): self {
        return new self(
            $unvalidatedUrlRequest->targetUrl()
        );
    }

    public function uniqueHash(): string
    {
        return hash('sha256', $this->targetUrl());
    }

    public function targetUrl(): string
    {
        return $this->targetUrl;
    }
}