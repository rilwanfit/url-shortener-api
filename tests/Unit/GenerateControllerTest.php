<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Controller\GenerateController;
use App\UrlGeneration\Application\UnvalidatedUrlRequest;
use App\UrlGeneration\Application\UrlGeneration;
use App\UrlGeneration\Domain\ShorteningUrl;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GenerateControllerTest extends TestCase
{
    /** @test */
    public function it_logs_all_valid_requests()
    {
        $urlGenerator = $this->prophesize(UrlGeneration::class);
        $urlGenerator->generate(Argument::any())->willReturn(new ShorteningUrl('irrelevant', 'not-important', 'hash'));
        $validator = $this->prophesize(ValidatorInterface::class);
        $validator->validate(Argument::type(UnvalidatedUrlRequest::class))->willReturn(new ConstraintViolationList([]));
        $logger = $this->prophesize(LoggerInterface::class);

        $sut = new GenerateController($urlGenerator->reveal(), $validator->reveal(), $logger->reveal());
        $request = $this->prophesize(Request::class);
        $request->getContent()->willReturn('{
          "targetUrl": "https://test.nl/?test=true"
        }');

        $sut($request->reveal());

        $logger->info('Begin url generation with valid input', Argument::type('array'))->shouldHaveBeenCalled();
    }
}