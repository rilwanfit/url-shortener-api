<?php
declare(strict_types=1);

namespace App\UrlGeneration\Infrastructure;

use App\UrlGeneration\Domain\ShorteningUrl;
use App\UrlGeneration\Domain\ShorteningUrlRequest;
use App\UrlGeneration\Domain\UrlRepository;
use DateTime;
use Doctrine\DBAL\Connection;

class DoctrineDbalUrlRepository implements UrlRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findByUrlRequest(ShorteningUrlRequest $request): ?ShorteningUrl
    {
        $foundUrl = $this->connection->createQueryBuilder()
            ->select([
                'input_url',
                'generated_url',
                'created_at',
                'unique_hash'
            ])
            ->from('url')
            ->andWhere('unique_hash = :uniqueHash')
            ->setParameter('uniqueHash', $request->uniqueHash())
            ->execute()
            ->fetch();

        if (!$foundUrl) {
            return null;
        }

        return ShorteningUrl::fromState($foundUrl);
    }

    public function save(ShorteningUrl $shorteningUrl, DateTime $createdAt): void
    {
        $this->connection->createQueryBuilder()
            ->insert('url')
            ->values([
                'input_url' => ':inputUrl',
                'generated_url' => ':generatedUrl',
                'created_at' => ':createdAt',
                'unique_hash' => ':uniqueHash'
            ])
            ->setParameter('inputUrl', $shorteningUrl->targetUrl())
            ->setParameter('generatedUrl', $shorteningUrl->generatedUrl())
            ->setParameter('createdAt', $createdAt->format('Y-m-d H:i:s'))
            ->setParameter('uniqueHash', $shorteningUrl->uniqueHash())
            ->execute();
    }
}