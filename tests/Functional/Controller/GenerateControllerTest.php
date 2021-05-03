<?php
declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GenerateControllerTest extends WebTestCase
{
    private $endpoint = '/generate';

    /** @test */
    public function it_returns_ok_for_a_generated_url()
    {
        $response = $this->requestWithPayload($this->getPayload());
        WebTestCase::assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_returns_bad_request_for_invalid_json()
    {
        $response = $this->requestWithPayload($this->getInvalidPayload());
        WebTestCase::assertEquals(400, $response->getStatusCode());
    }

    private function requestWithPayload($payload)
    {
        $client = static::createClient();
        $client->request(
            'POST',
            $this->endpoint,
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/json',
            ],
            $payload
        );

        return $client->getResponse();
    }

    private function getPayload(): string
    {
        return <<<JSON
{
    "targetUrl": "https://test.vonq.com"
}
JSON;
    }

    private function getInvalidPayload(): string
    {
        return '"invalid-json"';
    }
}