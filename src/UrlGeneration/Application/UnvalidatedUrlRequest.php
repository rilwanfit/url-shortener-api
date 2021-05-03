<?php
declare(strict_types=1);

namespace App\UrlGeneration\Application;

use Symfony\Component\Validator\Constraints as Assert;

final class UnvalidatedUrlRequest
{
    /**
     * @Assert\NotBlank(message="targetUrl should not be blank")
     * @Assert\Url(message="targetUrl should be a valid URL")
     * @Assert\Length(max=1024,maxMessage="targetUrl should not be longer than {{ limit }} characters")
     */
    private string $targetUrl;

    private function __construct()
    {
    }

    public static function fromJson(string $json): self
    {
        $urlRequest = new self();

        $requestData = json_decode($json, true);

        $urlRequest->targetUrl = trim($requestData['targetUrl'] ?? '', ' ');

        return $urlRequest;
    }

    public function targetUrl(): string
    {
        return $this->targetUrl;
    }
}