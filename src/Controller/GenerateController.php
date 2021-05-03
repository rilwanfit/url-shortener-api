<?php

declare(strict_types=1);

namespace App\Controller;

use App\UrlGeneration\Application\UnvalidatedUrlRequest;
use App\UrlGeneration\Application\UrlGeneration;
use App\UrlGeneration\Domain\ShorteningUrlRequest;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class GenerateController extends AbstractController
{
    private LoggerInterface $logger;

    private UrlGeneration $urlGeneration;

    private ValidatorInterface $validator;

    public function __construct(
        UrlGeneration $urlGeneration,
        ValidatorInterface $validator,
        LoggerInterface $logger
    ) {
        $this->urlGeneration = $urlGeneration;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * @Route("/generate", methods={"POST"}, name="app_generate")
     */
    public function __invoke(Request $request): JsonResponse
    {
        $unvalidatedUrlRequest = UnvalidatedUrlRequest::fromJson($request->getContent());

        $violations = $this->validator->validate($unvalidatedUrlRequest);
        if ($violations->count() > 0) {
            $violationsMessage = $this->createValidationMessage($violations);
            $this->logger->warning('Received invalid input', [
                'input' => $request->getContent(),
                'violations' => $violationsMessage
            ]);

            return $this->errorResponse($violationsMessage);
        }

        $this->logger->info('Begin url generation with valid input', [
            'input' => $request->getContent()
        ]);

        $generatedUrl = $this->urlGeneration->generate(
            ShorteningUrlRequest::fromUnvalidatedUrlRequest($unvalidatedUrlRequest)
        );

        return new JsonResponse($generatedUrl);
    }

    private function errorResponse(string $description, int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return new JsonResponse(['message' => $description], $statusCode);
    }

    private function createValidationMessage(ConstraintViolationListInterface $violations): string
    {
        $messages = [];
        foreach ($violations as $violation) {
            $messages[] = $violation->getMessage();
        }

        return implode(',', $messages);
    }
}
