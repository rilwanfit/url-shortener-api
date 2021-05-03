<?php
declare(strict_types=1);

namespace App\UrlGeneration\Domain;

use DateTime;

interface UrlRepository
{
    public function findByUrlRequest(ShorteningUrlRequest $request): ?ShorteningUrl;

    public function save(ShorteningUrl $shorteningUrl, DateTime $createdAt): void;
}