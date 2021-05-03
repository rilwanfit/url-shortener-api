<?php
declare(strict_types=1);

namespace App\Tests\Unit\UrlGeneration\Domain;

use App\UrlGeneration\Domain\ShorteningUrlRequest;
use PHPUnit\Framework\TestCase;

class ShorteningUrlRequestTest extends TestCase
{
    /** @test */
    public function it_can_be_assembled_from_unvalidated_request()
    {
        $shorteningUrl = ShorteningUrlRequest::fromUnvalidatedUrlRequest(
            UnvalidatedUrlRequestStub::simpleRequest()
        );

        self::assertEquals(UnvalidatedUrlRequestStub::SIMPLE_TARGET_URL, $shorteningUrl->targetUrl());
    }

    /** @test */
    public function it_generates_a_unique_hash()
    {
        $shorteningUrl = ShorteningUrlRequest::fromUnvalidatedUrlRequest(
            UnvalidatedUrlRequestStub::simpleRequest()
        );

        $actual = $shorteningUrl->uniqueHash();

        // Assert
        self::assertEquals(
            '4c6576038386f4649c1607c3693475b56f654ff4055be4de8d384cb797f9b526',
            $actual,
            'The generated hash does not match with the expected one'
        );
    }
}