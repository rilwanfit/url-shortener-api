<?php
declare(strict_types=1);

namespace App\UrlGeneration\Application;

use App\UrlGeneration\Domain\ShorteningUrl;
use App\UrlGeneration\Domain\ShorteningUrlRequest;
use App\UrlGeneration\Domain\UrlRepository;
use App\UrlGeneration\Domain\UrlShortener;
use DateTime;

class UrlGeneration
{
    private UrlRepository $urlRepository;

    private UrlShortener $shortener;

    public function __construct(UrlRepository $urlRepository, UrlShortener $shortener)
    {
        $this->urlRepository = $urlRepository;
        $this->shortener = $shortener;
    }

    public function generate(ShorteningUrlRequest $request): ShorteningUrl
    {
        $shorteningUrl = $this->urlRepository->findByUrlRequest($request);

        if ($shorteningUrl === null) {
            $generatedUrl = $this->shortener->shorten($request->targetUrl());

            $shorteningUrl = new ShorteningUrl(
                $request->targetUrl(),
                $generatedUrl,
                $request->uniqueHash()
            );

            $this->urlRepository->save($shorteningUrl, $this->currentTime());
        }

        return $shorteningUrl;
    }

    public function currentTime(): DateTime
    {
        return new DateTime('now');
    }
}