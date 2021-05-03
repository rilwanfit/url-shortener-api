<?php
declare(strict_types=1);

namespace App\UrlGeneration\Application;

use App\UrlGeneration\Domain\ShorteningUrl;
use App\UrlGeneration\Domain\ShorteningUrlRequest;
use App\UrlGeneration\Domain\UrlRepository;
use App\UrlGeneration\Domain\UrlShortener;
use DateTime;
use Travaux\VariantRetriever\Retriever\VariantRetrieverInterface;
use Travaux\VariantRetriever\ValueObject\Experiment;

class UrlGeneration
{
    private UrlRepository $urlRepository;

    private UrlShortener $bitlyUrlShortener;

    private UrlShortener $ownUrlShortener;

    private VariantRetrieverInterface $variantRetriever;

    public function __construct(UrlRepository $urlRepository, UrlShortener $bitlyUrlShortener, UrlShortener $ownUrlShortener, VariantRetrieverInterface $variantRetriever)
    {
        $this->urlRepository = $urlRepository;
        $this->bitlyUrlShortener = $bitlyUrlShortener;
        $this->ownUrlShortener = $ownUrlShortener;
        $this->variantRetriever = $variantRetriever;
    }

    public function generate(ShorteningUrlRequest $request): ShorteningUrl
    {
        $shorteningUrl = $this->urlRepository->findByUrlRequest($request);
        if ($shorteningUrl === null) {
            $shorteningUrl = new ShorteningUrl(
                $request->targetUrl(),
                $this->generateUrl($request->targetUrl()),
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

    private function generateUrl(string $targetUrl): string
    {
        $affectedVariant = $this->variantRetriever
            ->getVariantForExperiment(
                new Experiment('shortening-url-experiment'),
                $this->getUserId()
            );

        if ((string) $affectedVariant === 'control') {
            return $this->bitlyUrlShortener->shorten($targetUrl);
        }

        return $this->ownUrlShortener->shorten($targetUrl);
    }

    private function getUserId(): string
    {
        return (string) bin2hex(random_bytes(16));
    }
}