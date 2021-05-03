<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class HealthCheckController
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/health", name="app_health_check")
     */
    public function index(): JsonResponse
    {
        $this->logger->info('health check: ok');
        return new JsonResponse(['status' => '👍']);
    }
}
