<?php
declare(strict_types=1);

namespace App\UrlGeneration\Infrastructure;

use App\UrlGeneration\Domain\ServiceException;
use App\UrlGeneration\Domain\UrlShortener;
use Exception;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class BitlyUrlShortener implements UrlShortener
{
    public const BITLY_DOMAIN = 'https://api-ssl.bitly.com/';
    public const SHORT_URL_DOMAIN = 'bit.ly';
    public const TIMEOUT = 5;

    private string $token;

    private HttpClientInterface $httpClient;

    private LoggerInterface $logger;

    public function __construct(string $bitlyToken, HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->token = $bitlyToken;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    public function shorten(string $targetUrl): string
    {
        self::guardParameterIsURL($targetUrl);

        try {
            $response = $this->httpClient->request(
                'POST',
                self::BITLY_DOMAIN.'/v4/shorten',
                [
                    'body' => json_encode([
                        'domain' => self::SHORT_URL_DOMAIN,
                        'long_url' => $targetUrl
                    ]),
                    'timeout' => self::TIMEOUT,
                    'headers' => [
                        'Content-type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->token
                    ]
                ]
            );
        } catch (ClientException $e) {
            $this->logger->error(
                'Url Shortener error during Request',
                [
                    'trace' => $e->getTrace(),
                    'response' => !is_null($e->getResponse()) ? $e->getResponse()->getBody()->getContents() : 'no response captured'
                ]
            );
            throw ServiceException::duringUrlShortning();
        } catch (Exception $e) {
            $this->logger->error(
                'Url Shortener error',
                [
                    'trace' => $e->getTrace()
                ]
            );
            throw ServiceException::duringUrlShortning();
        }

        return $this->decodePayload($response);
    }

    private static function guardParameterIsURL(string $targetUrl)
    {
        if (filter_var($targetUrl, FILTER_VALIDATE_URL) === false) {
            throw new ServiceException('URL passed for shortning is not an URL : ' . $targetUrl);
        }
    }

    private function decodePayload(ResponseInterface $response): string
    {
        $content = json_decode($response->getContent());

        if ($content === null) {
            return '';
        }

        return $content->link;
    }
}