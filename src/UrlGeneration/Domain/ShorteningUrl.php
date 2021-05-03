<?php
declare(strict_types=1);

namespace App\UrlGeneration\Domain;

use JsonSerializable;

final class ShorteningUrl implements JsonSerializable
{
    private string $targetUrl;

    private string $generatedUrl;

    private string $uniqueHash;

    public function __construct(string $targetUrl, string $generatedUrl, string $uniqueHash)
    {
        $this->targetUrl = $targetUrl;
        $this->generatedUrl = $generatedUrl;
        $this->uniqueHash = $uniqueHash;
    }

    public static function fromState(array $state): self
    {
        return new self(
            $state['input_url'],
            $state['generated_url'],
            $state['unique_hash']
        );
    }

    public function targetUrl(): string
    {
        return $this->targetUrl;
    }

    public function generatedUrl(): string
    {
        return $this->generatedUrl;
    }

    public function uniqueHash(): string
    {
        return $this->uniqueHash;
    }

    public function jsonSerialize()
    {
        return [
            'generatedUrl' => $this->generatedUrl
        ];
    }
}