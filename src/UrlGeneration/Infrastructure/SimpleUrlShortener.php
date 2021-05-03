<?php
declare(strict_types=1);

namespace App\UrlGeneration\Infrastructure;

use App\UrlGeneration\Domain\ServiceException;
use App\UrlGeneration\Domain\UrlShortener;
use Exception;
use Psr\Log\LoggerInterface;

final class SimpleUrlShortener implements UrlShortener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function shorten(string $targetUrl): string
    {
        self::guardParameterIsURL($targetUrl);

        try {
            return 'localhost.url.' .substr(hash('sha256', $targetUrl),0,6);
        } catch (Exception $e) {
            $this->logger->error(
                'Url Shortener error',
                [
                    'trace' => $e->getTrace()
                ]
            );
            throw ServiceException::duringUrlShortning();
        }
    }

    private static function guardParameterIsURL(string $targetUrl)
    {
        if (filter_var($targetUrl, FILTER_VALIDATE_URL) === false) {
            throw new ServiceException('URL passed for shortning is not an URL : ' . $targetUrl);
        }
    }
}